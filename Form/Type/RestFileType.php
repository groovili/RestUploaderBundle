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
    
    namespace Groovili\RestUploaderBundle\Form\Type;
    
    use Doctrine\ORM\EntityManagerInterface;
    use Groovili\RestUploaderBundle\Entity\File;
    use Groovili\RestUploaderBundle\Form\DataTransformer\RestFileTransformer;
    use Groovili\RestUploaderBundle\Service\FileManager;
    use Groovili\RestUploaderBundle\Service\FileValidator;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\Form\FormView;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    /**
     * Class RestFileType
     *
     * @package Groovili\RestUploaderBundle\Form\Type
     */
    class RestFileType extends AbstractType
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
         * @var \Doctrine\ORM\EntityManagerInterface
         */
        protected $em;
        
        /**
         * RestFileType constructor.
         *
         * @param \Groovili\RestUploaderBundle\Service\FileManager $fileManager
         * @param \Groovili\RestUploaderBundle\Service\FileValidator $fileValidator
         */
        public function __construct(
          FileManager $fileManager,
          FileValidator $fileValidator,
          EntityManagerInterface $entityManager
        ) {
            $this->fileManager = $fileManager;
            $this->fileValidator = $fileValidator;
            $this->em = $entityManager;
        }
        
        /**
         * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
         */
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
              'data_class'          => null,
              'validate_extensions' => true,
              'validate_size'       => true,
              'allow_delete'        => true,
              'private'             => false,
              'compound'            => true,
            ]);
            
            $resolver->setAllowedTypes('validate_extensions', 'bool');
            $resolver->setAllowedTypes('validate_size', 'bool');
            $resolver->setAllowedTypes('allow_delete', 'bool');
            $resolver->setAllowedTypes('private', 'bool');
            
            $resolver->setRequired('validate_extensions');
            $resolver->setRequired('validate_size');
            $resolver->setRequired('allow_delete');
            $resolver->setRequired('private');
        }
        
        /**
         * @param \Symfony\Component\Form\FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(
          FormBuilderInterface $builder,
          array $options
        ): void {
            $builder->add('file', FileType::class, [
              'required'    => $options['required'],
              'data_class'  => File::class,
              'multiple'    => null,
              'constraints' => [],
            ]);
            
            $builder->addModelTransformer(new RestFileTransformer
            ($this->fileManager, $this->fileValidator, $options, $this->em),
              true);
            
            if ($options['allow_delete']) {
                $this->buildDeleteField($builder, $options);
            }
        }
        
        /**
         * @param \Symfony\Component\Form\FormBuilderInterface $builder
         * @param array $options
         */
        protected function buildDeleteField(
          FormBuilderInterface $builder,
          array $options
        ): void {
            $builder->addEventListener(FormEvents::PRE_SET_DATA,
              function (FormEvent $event) use ($options): void {
                  $form = $event->getForm();
                  $object = $form->getParent()->getData();
                  
                  // no object or no uploaded file: no delete button
                  if (null === $object) {
                      return;
                  }
                  
                  $form->add('delete', CheckboxType::class, [
                    'mapped'   => false,
                    'required' => false,
                  ]);
              });
            
            $builder->addEventListener(FormEvents::POST_SUBMIT,
              function (FormEvent $event): void {
                  $form = $event->getForm();
                  $object = $form->getParent()->getData();
                  $delete = $form->has('delete') ? $form->get('delete')
                                                        ->getData() : false;
                  if (!$delete) {
                      return;
                  }
                  
                  $this->fileManager->remove($object);
              });
        }
        
        /**
         * @param \Symfony\Component\Form\FormView $view
         * @param \Symfony\Component\Form\FormInterface $form
         * @param array $options
         */
        public function buildView(
          FormView $view,
          FormInterface $form,
          array $options
        ) {
            parent::buildView($view, $form, $options);
        }
        
        
        /**
         * @return mixed
         */
        public function getParent()
        {
            return FileType::class;
        }
        
        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix(): string
        {
            return 'rest_uploader_file';
        }
    }