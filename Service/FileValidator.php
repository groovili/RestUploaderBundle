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
    
    use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    
    /**
     * Class FileValidator
     *
     * @package Groovili\RestUploaderBundle\Service
     */
    class FileValidator
    {
        protected const DEFAULT_FILE_MAX_SIZE = 25;
        
        /**
         * @var array
         */
        protected static $excelExtensions = [
          'xls',
          'xlt',
          'xlm',
          'xlsx',
          'xlsm',
          'xltx',
          'xltm',
          'xlsb',
          'xla',
          'xlam',
          'xll',
          'xlw',
        ];
        
        /**
         * @var array
         */
        protected static $imageExtensions = [
          'ani',
          'bmp',
          'cal',
          'fax',
          'gif',
          'img',
          'jbg',
          'jpe',
          'jpe',
          'jpg',
          'mac',
          'pbm',
          'pcd',
          'pcx',
          'pct',
          'pgm',
          'png',
          'ppm',
          'psd',
          'ras',
          'tga',
          'tiff',
          'wma',
        ];
        
        /**
         * @var array
         */
        private $config;
        
        /**
         * @var string
         */
        private $kernelRoot;
        
        /**
         * FileValidator constructor.
         *
         * @param string $kernelRoot
         */
        public function __construct(string $kernelRoot)
        {
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
         * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
         *
         * @return bool
         */
        public function checkFileSize(UploadedFile $file): bool
        {
            // Upload size in MB
            $size = $file->getSize() / 1000000;
            
            if (self::DEFAULT_FILE_MAX_SIZE >= $size) {
                return true;
            }
            
            return false;
        }
        
        /**
         * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
         *
         * @return bool
         */
        public function checkIsExcel(UploadedFile $file): bool
        {
            if (!in_array($file->getClientOriginalExtension(),
              self::$excelExtensions)
            ) {
                return false;
            }
            
            return true;
        }
        
        /**
         * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
         *
         * @return bool
         */
        public function checkIsImage(UploadedFile $file): bool
        {
            if (!in_array($file->getClientOriginalExtension(),
              self::$imageExtensions)
            ) {
                return false;
            }
            
            return true;
        }
    }