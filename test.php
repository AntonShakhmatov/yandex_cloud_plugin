<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class Yandex_Cloud
{
    private $s3;
    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }

    public function sendToStorage(string $dir, $prefix = ''): void
    {
        if (!defined('IMAGETYPE_SVG')) {
            define('IMAGETYPE_SVG', 13);
        }
        $folders = scandir($dir);
        foreach ($folders as $folder) {
            if (in_array($folder, array(".", ".."))) {
                continue;
            }
            $folderPath = $dir . '/' . $folder;
            if (is_dir($folderPath)) {
                $newPrefix = $prefix . '/' . $folder;
                $this->sendToStorage($folderPath, $newPrefix);
            } else {
                $type = exif_imagetype($folderPath);
                if (!in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_SVG, IMAGETYPE_BMP, IMAGETYPE_WEBP, IMAGETYPE_ICO])) {
                    continue; // skip non-image files
                }
                $image = null;
                $resizedImage = null;
                $tempFile = '';
                try {
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            list($width, $height) = getimagesize($folderPath);
                            $image = imagecreatefromjpeg($folderPath);
                            break;
                        case IMAGETYPE_PNG:
                            $image = imagecreatefrompng($folderPath);
                            $width = imagesx($image);
                            $height = imagesy($image);
                            break;
                        case IMAGETYPE_GIF:
                            $image = imagecreatefromgif($folderPath);
                            $width = imagesx($image);
                            $height = imagesy($image);
                            break;
                        case IMAGETYPE_SVG:
                            $this->s3->putObject([
                                'Bucket' => '',
                                'Key' => 'files/images' . $prefix . '/' . $folder,
                                'ContentType' => 'image/svg+xml',
                                'Body' => file_get_contents($folderPath),
                                'ACL' => 'public-read',
                            ]);
                            continue 2; // skip to next iteration of loop
                    }
                    $newWidth = $width * 0.9;
                    $newHeight = $height * 0.9;
                    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresized($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    $tempFile = tempnam(sys_get_temp_dir(), '');
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            imagejpeg($resizedImage, $tempFile, 75);
                            break;
                        case IMAGETYPE_PNG:
                            imagepng($resizedImage, $tempFile, 1);
                            break;
                        case IMAGETYPE_GIF:
                            imagegif($resizedImage, $tempFile);
                            break;
                    }
                    $this->s3->putObject([
                        'Bucket' => '',
                        'Key' => 'files/images' . $prefix . '/' . $folder,
                        'ContentType' => $type,
                        'Body' => file_get_contents($tempFile),
                        'ACL' => 'public-read',
                    ]);
                } catch (Exception $e) {
                    echo "There was an error uploading the file.\n . {$e}";
                } finally {
                    if ($image) {
                        imagedestroy($image);
                    }
                    if ($resizedImage) {
                        imagedestroy($resizedImage);
                    }
                    if ($tempFile) {
                        unlink($tempFile);
                    }
                }
            }
        }
    }
}

$s3 = new S3Client([
    'version' => 'latest',
    'endpoint' => 'https://storage.yandexcloud.net',
    'region' => 'ru-central1',
    'credentials' => [
        'key' => '',
        'secret' => '',
    ],
]);
$yc = new Yandex_Cloud($s3);
$dir = '/var/www/html/public_html/files/images/';
$yc->sendToStorage($dir);
echo 'Files uploaded to Yandex Cloud';
