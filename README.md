Eng:
Translate to English:

Plugin settings:

- plugin_title - Plugin name
- plugin_version - Plugin version
- extension_id - Extension ID
- plugin_alias - Plugin alias

Plugin methods:

- init() - Plugin initialization method
- start() - Method for uploading images to Yandex.Cloud server

Yandex_Cloud class:

- sendToStorage() - Method for uploading images to Yandex.Cloud server

Installation:

1. Copy plugin files to the 'plugins/yacloud' directory
2. Add settings for connecting to Yandex.Cloud in the config.php file:
$key_cloud = ''; // API key
$secret_key = ''; // Secret API key
'region' => 'ru-central1', // Server region
'Bucket' => '', // Name of the bucket where images will be uploaded

3. Activate the plugin in the BFF administrative panel or run it using cron:
#!/bin/bash
* * * * * php /path/to/Yandex_Cloud.php >/dev/null 2>&1
(The plugin is written for a custom MVC framework called BFF and may require some adjustments when connecting to another framework)

Рус:
Настройки плагина:
- plugin_title - Название плагина
- plugin_version - Версия плагина
- extension_id - Идентификатор расширения
- plugin_alias - Alias плагина

Методы плагина:
- init() - Метод инициализации плагина
- start() - Метод для загрузки изображений на сервер Яндекс.Облако

Класс Yandex_Cloud:
- sendToStorage() - Метод загрузки изображений на сервер Яндекс.Облако

Установка:
1. Скопируйте файлы плагина в директорию 'plugins/yacloud'
2. В файле config.php добавьте настройки для подключения к Яндекс.Облако:
    $key_cloud = ''; // API-ключ
    $secret_key = ''; // Секретный API-ключ
    'region' => 'ru-central1', // Регион сервера
    'Bucket' => '', // Имя бакета, куда будут загружаться изображения
3. Активируйте плагин в административной панели BFF либо запустите его через крон: 
#!/bin/bash
* * * * * php /path/to/Yandex_Cloud.php >/dev/null 2>&1
(Плагин написан для самописного mvc фреймворка bff и вероятно требует перенастройки при подключении к другому фреймворку)