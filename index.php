<?php

class Plugin_Ya_cloud_p0e1fb2 extends \Plugin
{
    public function init()
    {
        parent::init();
        
        $this->setSettings([
            'plugin_title' => 'Яндекс облако',
            'plugin_version' => '1.0.0',
            'extension_id' => 'p0e1fb229d26becb05d1fff884005a563d2c8ce2',
            'plugin_alias' => 'yacloud',
        ]);

        $this->configSettings([
            //
        ]);
    }

    protected function start()
    {
        require $this->path('YandexCloud.php');
        $key_cloud = 'YCAJESKk8_jFs5VEULY8RFfqq';
        $secret_key = 'YCPbDGhg37i9a2eFlp8Zmg9WSgduTcm1flHMINQy';
        $s3 = new S3Client([
            'version' => 'latest',
            'endpoint' => 'https://storage.yandexcloud.net',
            'region' => 'ru-central1',
            'credentials' => [
                'key' => $key_cloud,
                'secret' => $secret_key,
            ],
        ]);
        $yc = new Yandex_Cloud($s3);
        $result = $yc->sendToStorage();
        bff::hook('plugins.start.ya_cloud_p0e1fb2', $result);
    }
}
