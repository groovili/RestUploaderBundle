<?php
/**
 *
 *  * This file is part of the RestUploaderBundle package.
 *  * (c) groovili
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */
declare(strict_types=1);

namespace Groovili\RestUploaderBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Groovili\RestUploaderBundle\Form\DataTransformer\RestFileTransformer;
use Groovili\RestUploaderBundle\Service\FileManager;
use Groovili\RestUploaderBundle\Service\FileValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            'data_class' => null,
            'validate_extensions' => true,
            'validate_size' => true,
            'allow_delete' => true,
            'private' => false,
            'error_bubbling' => false,
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
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {

        // Validation on pre submit of form to avoid invalid file download
        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            function (FormEvent $event): void {
                $upload = $event->getData();
                $form = $event->getForm();
                $config = $form->getConfig();
                $options = $config->getOptions();
                $error = false;

                if ($upload instanceof UploadedFile) {
                    if (true === $options['validate_extensions']) {
                        if (!$this->fileValidator->isExtensionAllowed($upload)) {
                            $form->addError(new FormError('File extensions is not allowed.'));
                            $error = true;
                        }
                    }

                    if (true === $options['validate_size']) {
                        if (!$this->fileValidator->isSizeValid($upload)) {
                            $form->addError(new FormError('File size is too big.'));
                            $error = true;
                        }
                    }

                    if ($error) {
                        $form->setData(null);
                        $event->setData(null);
                    }
                }
            });

        // Transform data to File and upload it or return existing File if id provided
        $dataTransformer = new RestFileTransformer
        ($this->fileManager, $options, $this->em);

        $builder->addModelTransformer($dataTransformer);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'rest_uploader_file';
    }
}