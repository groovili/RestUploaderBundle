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
        protected static $emptyTransform = [
          'file' => null,
        ];
        
        /**
         * RestFileTransformer constructor.
         *
         * @param \Groovili\RestUploaderBundle\Service\FileManager $fileManager
         * @param \Groovili\RestUploaderBundle\Service\FileValidator $fileValidator
         * @param array $options
         */
        public function __construct(
          FileManager $fileManager,
          FileValidator $fileValidator,
          array $options,
          EntityManagerInterface $entityManager
        ) {
            $this->fileManager = $fileManager;
            $this->fileValidator = $fileValidator;
            $this->options = $options;
            $this->em = $entityManager;
        }
        
        /**
         * @param mixed $file
         *
         * @return array
         */
        public function transform($file): array
        {
            $private = false;
            
            if (true === $this->options['validate_extensions']) {
                if (!$this->fileValidator->isExtensionAllowed($file)) {
                    return self::$emptyTransform;
                }
            }
            
            if (true === $this->options['validate_size']) {
                if (!$this->fileValidator->isSizeValid($file)) {
                    return self::$emptyTransform;
                }
            }
            
            if (true === $this->options['private']) {
                $private = true;
            }
            
            if ($file instanceof UploadedFile) {
                return [
                  'file' => $this->fileManager->upload($file, $private),
                ];
            }
            
            return [
              'file' => $file,
            ];
        }
        
        /**
         * @param \Groovili\RestUploaderBundle\Entity\File $data
         *
         * @return \Groovili\RestUploaderBundle\Entity\File|null|object
         */
        public function reverseTransform($file)
        {
            if (!$file) {
                return null;
            }
            
            $issue = $this->em->getRepository('RestUploaderBundle:File')
                              ->findOneBy(['name' => $file->getName()]);
            
            if (null === $issue) {
                throw new TransformationFailedException(sprintf('An issue with number "%s" does not exist!',
                  $file->getName()));
            }
            
            return $issue;
        }
    }