<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class Test_Yandex_Cloud
{
    private $s3;
    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }
    public function sendToStorage(string $dir, $prefix = ''): void
    {
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
                try {
                    $result = $this->s3->putObject([
                        'Bucket' => '',
                        'Key' => 'files/images' . $prefix . '/' . $folder,
                        'SourceFile' => $folderPath,
                        'ACL' => 'public-read',
                    ]);
                } catch (Aws\S3\Exception\S3Exception$e) {
                    echo "There was an error uploading the file.\n . {$e}";
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
