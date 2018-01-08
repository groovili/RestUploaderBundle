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

namespace Groovili\RestUploaderBundle\Event;

use Groovili\RestUploaderBundle\Entity\File;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FilePreGetPath
 *
 * @package Groovili\RestUploaderBundle\Event
 */
class FilePreGetPath extends Event
{
    CONST FILE_PRE_GET_PATH = 'rest_uploader.file.preGetPath';

    /**
     * @var string
     */
    protected $root;

    /**
     * @var \Groovili\RestUploaderBundle\Entity\File
     */
    protected $fileEntity;

    /**
     * FilePreGetPath constructor.
     *
     * @param \Groovili\RestUploaderBundle\Entity\File $fileEntity
     * @param string $root
     */
    public function __construct(
        File $fileEntity,
        string $root
    ) {
        $this->fileEntity = $fileEntity;
        $this->root = $root;
    }

    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return \Groovili\RestUploaderBundle\Entity\File
     */
    public function getFileEntity(): File
    {
        return $this->fileEntity;
    }
}