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

namespace Groovili\RestUploaderBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use function dump;
use Groovili\RestUploaderBundle\Entity\File;
use Groovili\RestUploaderBundle\Service\FileManager;
use function is_array;
use function is_numeric;
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
     * @var array
     */
    protected $options;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * RestFileTransformer constructor.
     * @param FileManager $fileManager
     * @param array $options
     * @param EntityManagerInterface $entityManager
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
     * @param $data
     * @return File|null|object
     */
    private function search4FileEntity($data)
    {
        if (false === $data || null === $data || !is_array($data)) {
            return $data;
        }

        if (isset($data['id']) && is_numeric($data['id'])) {
            $fileEntity = $this->em->getRepository('RestUploaderBundle:File')
                ->findOneBy([
                    'id' => $data['id'],
                ]);

            if (null === $fileEntity) {
                throw new TransformationFailedException(sprintf('A file with id "%s" does not exist!',
                    $data['id']));
            }

            return $fileEntity;
        }


        return $data;
    }

    /**
     * @param mixed $data
     * @return File|mixed|null|object
     */
    public function transform($data)
    {
        return $this->search4FileEntity($data);
    }

    /**
     * @param mixed $data
     * @return File|mixed|null|object
     * @throws \Exception
     */
    public function reverseTransform($data)
    {
        if ($data instanceof UploadedFile) {
            $private = false;

            if (true === $this->options['private']) {
                $private = true;
            }

            return $this->fileManager->upload($data, $private);
        }

        return $this->search4FileEntity($data);
    }
}