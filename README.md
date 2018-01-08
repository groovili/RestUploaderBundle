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

## Examples

RestFileType for file upload 

```php
<?php
    /** @var @UploadedFile $upload */
    $upload = $request->files->get('file');
    
    if (isset($upload)) {
        $form = $this->createFormBuilder()
            ->add('file', RestFileType::class, [
                'allow_delete' => true,
                'validate_extensions' => true,
                'validate_size' => true,
                'private' => false,
            ])
            ->getForm();
    
        $form->handleRequest($request);
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit(['file' => $upload], $clearMissing);
    
        $data = $form->getData();
    
        if (isset($data['file'])) {
            /** @var File $file */
            $file = $data['file'];
            $em = $this->getDoctrine()->getManager();
            $em->persist($file);
            $em->flush();
        }
     }
```

RestFileType submit of existing entity

```php
<?php
    /**
    * $file = ['file' => ['id' => 8]]
    */
    $file = json_decode($request->getContent(), true);
    
    $form = $this->createFormBuilder()
        ->add('file', RestFileType::class, [
            'allow_delete' => true,
            'validate_extensions' => true,
            'validate_size' => true,
            'private' => false,
        ])
        ->getForm();

    $form->handleRequest($request);
    $clearMissing = $request->getMethod() != 'PATCH';
    $form->submit($file , $clearMissing);
```

Upload and validate file via service

```php
<?php
    /** @var @UploadedFile $upload */
    $upload = $request->files->get('file');
    
    if (isset($upload)) {
        $validator = $this->container->get('rest_uploader.validator');
        $uploader = $this->container->get('rest_uploader.manager');
        
        if ($validator->isExtensionAllowed($upload) && $validator->isSizeValid($upload)){
            /** @var File $file */
            $file = $uploader->upload($upload, false);
        }
     }
```