<?php
    /**
     *
     *  * This file is part of the RestUploaderBundle package.
     *  * (c) groovili
     *  * For the full copyright and license information, please view the LICENSE
     *  * file that was distributed with this source code.
     *
     */
    declare(strict_types = 1);
    
    namespace Groovili\RestUploaderBundle\Service;
    
    use Groovili\RestUploaderBundle\Entity\File;
    use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    
    /**
     * Class FileManager
     *
     * @package Groovili\RestUploaderBundle\Service
     */
    class FileManager
    {
        protected const DEFAULT_PUBLIC_FILES_PATH  = 'web/assets/files';
        
        protected const DEFAULT_PRIVATE_FILES_PATH = '../private';
        
        /**
         * @var string
         */
        private $publicDir;
        
        /**
         * @var string
         */
        private $privateDir;
        
        /**
         * @var string
         */
        private $kernelRoot;
        
        /**
         * @var array
         */
        private $config;
        
        /**
         * FileManager constructor.
         *
         * @param string $kernelRoot
         */
        public function __construct(string $kernelRoot)
        {
            $this->publicDir = $_SERVER['DOCUMENT_ROOT'].'/'.self::DEFAULT_PUBLIC_FILES_PATH;
            $this->privateDir = $_SERVER['DOCUMENT_ROOT'].'/'.self::DEFAULT_PRIVATE_FILES_PATH;
            $this->kernelRoot = $kernelRoot;
        }
        
        /**
         * @param array $config
         */
        public function setConfig(array $config): void
        {
            if (!isset($config['public_dir'])) {
                throw new InvalidArgumentException('Public files dir "public_dir" is required for RestUploader bundle');
            }
            
            $this->config = $config;
        }
        
        
        /**
         * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
         * @param string|null $path
         *
         * @return File|null
         */
        public function uploadFile(
          UploadedFile $uploadedFile,
          bool $private = false
        ): ?File
        {
            $uploadPath = $this->publicDir;
            $relPath = self::DEFAULT_PUBLIC_FILES_PATH;
            $scheme = File::SCHEME['Public'];
            
            if (true === $private) {
                $uploadPath = $this->privateDir;
                $relPath = self::DEFAULT_PRIVATE_FILES_PATH;
                $scheme = File::SCHEME['Private'];
            }
            
            try {
                $target = $uploadedFile->move($uploadPath,
                  md5($uploadedFile->getBasename()).'.'.$uploadedFile->getClientOriginalExtension());
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            
            $file = new File($uploadedFile->getClientOriginalName(),
              $target->getMimeType(), $target->getExtension(),
              $relPath.'/'.md5($uploadedFile->getBasename()).'.'.$uploadedFile->getClientOriginalExtension(),
              $scheme);
            
            return $file;
        }
        
        /**
         * @param File $file
         *
         * @return string
         */
        public function getFileRealPath(File $file): string
        {
            if (File::SCHEME['Private'] === $file->getScheme()) {
                return $this->kernelRoot.'/'.$file->getPath();
            }
            
            return $this->kernelRoot.'/../'.$file->getPath();
        }
    }