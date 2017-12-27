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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FilePostUpload
 *
 * @package Groovili\RestUploaderBundle\Event
 */
class FilePostUpload extends Event
{
    CONST FILE_POST_UPLOAD = 'rest_uploader.file.postUpload';
    
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $file;
    
    /**
     * @var Request $request
     */
    protected $request;
    
    /**
     * @var \Groovili\RestUploaderBundle\Entity\File
     */
    protected $fileEntity;
    
    /**
     * FilePostUpload constructor.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Groovili\RestUploaderBundle\Entity\File $fileEntity
     */
    public function __construct(UploadedFile $file, Request $request, File
    $fileEntity)
    {
        $this->file = $file;
        $this->request = $request;
        $this->fileEntity = $fileEntity;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file;
    }
    
    /**
     * @return \Groovili\RestUploaderBundle\Entity\File
     */
    public function getFileEntity(): File
    {
        return $this->fileEntity;
    }
}