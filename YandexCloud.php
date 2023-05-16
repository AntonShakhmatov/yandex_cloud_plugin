<?php

namespace plugins\yacloud;

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class Yandex_Cloud
{
    private $s3;
    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }
    public function sendToStorage(string $dir): void
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (in_array($file, array(".", ".."))) {
                continue;
            }
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                $this->sendToStorage($filePath);
            } else {
                $folderName = $dir;
                try {
                    $result = $this->s3->putObject([
                        'Bucket' => 'astregoimagestorage',
                        'Key' => $folderName . '/' . $file,
                        'SourceFile' => $filePath,
                        'ACL' => 'public-read',
                    ]);
                } catch (Aws\S3\Exception\S3Exception$e) {
                    echo "There was an error uploading the file.\n . {$e}";
                }
            }
        }
    }
}
