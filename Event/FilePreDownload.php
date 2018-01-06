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
 * Class FilePreDownload
 *
 * @package Groovili\RestUploaderBundle\Event
 */
class FilePreDownload extends Event
{
    CONST FILE_PRE_DOWNLOAD = 'rest_uploader.file.preDownload';
    
    /**
     * @var \Groovili\RestUploaderBundle\Entity\File
     */
    protected $fileEntity;
    
    /**
     * FilePreDownload constructor.
     *
     * @param \Groovili\RestUploaderBundle\Entity\File $fileEntity
     */
    public function __construct(File
    $fileEntity)
    {
        $this->fileEntity = $fileEntity;
    }
    
    /**
     * @return \Groovili\RestUploaderBundle\Entity\File
     */
    public function getFileEntity(): File
    {
        return $this->fileEntity;
    }
}