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
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use Symfony\Component\HttpFoundation\Response;
    
    /**
     * Class FileManager
     *
     * @package Groovili\RestUploaderBundle\Service
     */
    class FileManager
    {
        /**
         * @var string
         */
        private $kernelRoot;
        
        /**
         * @var \Symfony\Component\Filesystem\Filesystem
         */
        private $fileSystem;
        
        /**
         * @var array
         */
        private $config;
        
        /**
         * FileManager constructor.
         *
         * @param string $kernelRoot
         */
        public function __construct(string $kernelRoot, Filesystem $filesystem)
        {
            $this->kernelRoot = $kernelRoot;
            $this->fileSystem = $filesystem;
        }
        
        /**
         * @param array $config
         */
        public function setConfig(array $config): void
        {
            $this->config = $config;
        }
        
        /**
         * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
         * @param string|null $path
         *
         * @return File|null
         */
        public function upload(
          UploadedFile $uploadedFile,
          bool $private = false
        ): ?File
        {
            $uploadPath = $this->kernelRoot.'/'.$this->config['public_dir'];
            $relPath = $this->config['public_dir'];
            $scheme = File::SCHEME['Public'];
            
            if (true === $private) {
                $uploadPath = $this->kernelRoot.'/'.$this->config['private_dir'];
                $relPath = $this->config['private_dir'];
                $scheme = File::SCHEME['Private'];
            }
            
            try {
                $target = $uploadedFile->move($uploadPath,
                  md5($uploadedFile->getBasename()).'.'.$uploadedFile->getClientOriginalExtension());
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            
            $file = new File($scheme, $uploadedFile->getClientOriginalName(),
              $target->getExtension(), $target->getSize(),
              $relPath.'/'.md5($uploadedFile->getBasename()).'.'.$uploadedFile->getClientOriginalExtension());
            
            return $file;
        }
        
        /**
         * @param \Groovili\RestUploaderBundle\Entity\File $file
         *
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function download(File $file): Response
        {
            $download = file_get_contents($this->getFileRealPath($file));
            
            $headers = [
              'Content-Type'        => $file->getExt(),
              'Content-Disposition' => 'inline; filename="'.$file->getName().'"',
            ];
            
            return new Response($download, 200, $headers);
        }
        
        /**
         * @param \Groovili\RestUploaderBundle\Entity\File $file
         *
         * @return bool
         */
        public function remove(File $file): bool
        {
            try {
                $this->fileSystem->remove($this->getFileRealPath($file));
            } catch (\Exception $exception) {
                return false;
            }
            
            return true;
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
            
            return $this->kernelRoot.'/'.$file->getPath();
        }
    }