# RestUploaderBundle
A **Symfony bundle** for file upload and management **for REST API**.

Provides `File` entity, `rest_uploader.manager`,`rest_uploader.validator` services, `RestFileType` and list of **events to subscribe**:
1. `rest_uploader.file.preUpload`
2. `rest_uploader.file.postUpload`
3. `rest_uploader.file.preDownload`
4. `rest_uploader.file.preDelete`
5. `rest_uploader.file.preGetPath`

Examples can be found in examples section below.

## Installation

Require the `groovili/rest-uploader-bundle` package in your **composer.json** and update your dependencies.

    composer require groovili/rest-uploader-bundle

Add the **RestUploaderBundle** to your application's kernel:

```php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Groovili\RestUploaderBundle\RestUploaderBundle(),
            // ...
        ];
        // ...
    }
```

**FOSRest**, **JMSSerializer** and **Nelmio Api-doc** bundle would be installed as well and should be configured in your **config.yml** file.
Please notice that `csrf_protection` should be `false` to use **RestFileType**.

## Configuration

The `public_dir` and `private_dir` are path strings from **app** folder.
If not exist, would be added automatically. This parameters should be only strings.

`allowed_extensions` is array of strings with allowed file extensions.

`file_max_size` is integer number in MB, which would be maximum limit.

Configuration which provided below is default for this bundle:

```yaml
    rest_uploader:
        public_dir: ../web/files
        private_dir: '../private'
        allowed_extensions: []
        file_max_size: 25
```