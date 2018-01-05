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
    
    namespace Groovili\RestUploaderBundle\Form\DataTransformer;
    
    use Doctrine\ORM\EntityManagerInterface;
    use Groovili\RestUploaderBundle\Entity\File;
    use Groovili\RestUploaderBundle\Service\FileManager;
    use Groovili\RestUploaderBundle\Service\FileValidator;
    use Symfony\Component\Form\DataTransformerInterface;
    use Symfony\Component\Form\Exception\TransformationFailedException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    
    /**
     * Class RestFileTransformer
     *
     * @package Groovili\RestUploaderBundle\Form\DataTransformer
     */
    class RestFileTransformer implements DataTransformerInterface
    {
        /**
         * @var \Groovili\RestUploaderBundle\Service\FileManager
         */
        protected $fileManager;
        
        /**
         * @var \Groovili\RestUploaderBundle\Service\FileValidator
         */
        protected $fileValidator;
        
        /**
         * @var array
         */
        protected $options;
        
        /**
         * @var \Doctrine\ORM\EntityManagerInterface
         */
        protected $em;
        
        /**
         * @var array
         */
        protected static $emptyTransform = null;
        
        /**
         * RestFileTransformer constructor.
         *
         * @param \Groovili\RestUploaderBundle\Service\FileManager $fileManager
         * @param \Groovili\RestUploaderBundle\Service\FileValidator $fileValidator
         * @param array $options
         */
        public function __construct(
          FileManager $fileManager,
          array $options,
          EntityManagerInterface $entityManager
        ) {
            $this->fileManager = $fileManager;
            $this->options = $options;
            $this->em = $entityManager;
        }
        
        /**
         * @param mixed $file
         *
         * @return array|null
         */
        public function transform($file)
        {
            if (!$file) {
                return self::$emptyTransform;
            }
            
            $fileEntity = $this->em->getRepository('RestUploaderBundle:File')
                                   ->findOneBy([
                                     'name' => $file->getClientOriginalName(),
                                   ]);
            
            if (null === $fileEntity) {
                throw new TransformationFailedException(sprintf('A file with name "%s" does not exist!',
                  $file->getClientOriginalName()));
            }
            
            return $fileEntity;
        }
        
        /**
         * @param mixed $data
         *
         * @return mixed
         */
        public function reverseTransform($file)
        {
            if (!$file instanceof UploadedFile) {
                return self::$emptyTransform;
            }
            
            $private = false;
            
            if (true === $this->options['private']) {
                $private = true;
            }
            
            return $this->fileManager->upload($file, $private);
        }
    }