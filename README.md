# RestUploaderBundle
A Symfony bundle for file uploade and management for REST services

## Installation

Require the `groovili/rest-uploader-bundle` package in your composer.json and update your dependencies.

    $ composer require groovili/rest-uploader-bundle

Add the RestUploaderBundle to your application's kernel:

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

## Configuration

The `public_dir` and `private_dir` are path strings from web folder.
If not exist, would be added automatically. This parameters should be only strings.

`allowed_extensions` is array of strings with allowed file extensions.

`file_max_size` is integer number in MB, which would be maximum limit.

Configuration which provided below is default for this bundle.

```yaml
    rest_uploader:
        public_dir: files
        private_dir: '../private'
        allowed_extensions: []
        file_max_size: 25
```