<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class Yandex_Cloud
{
    private $s3;
    private $bucket = '';
    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }
    public function sendToStorage(): void
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . 'path/to/images/';
        // Получение списка файлов в папке
        $folders = array_diff(scandir($dir), array('.', '..'));
        foreach ($folders as $folder) {
            $folderName = basename($folder);
            // Создаем папку в корне бакета
            $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $folderName . '/',
            ]);
            $files = array_diff(scandir($dir . $folderName), array('.', '..'));
            // Цикл по всем файлам
            foreach ($files as $file) {
                if (in_array($file, array(".", ".."))) {
                    continue;
                }
                // Пропускаем . и ..
                // Путь к текущей картинке
                $filePath = $dir . $folderName . '/' . $file;
                try {
                    $result = $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key' => $folderName . '/' . $file,
                        'SourceFile' => $filePath,
                        'ACL' => 'public-read',
                    ]);
                } catch (Aws\S3\Exception\S3Exception$e) {
                    echo "There was an error uploading the file.\n . {$e}";
                }
            }
        }
        $bucketName = $this->bucket;
        $objectName = $folderName . '/' . $file;

        $objectUrl = $this->s3->getObjectUrl($bucketName, $objectName);
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
$yc->sendToStorage();
