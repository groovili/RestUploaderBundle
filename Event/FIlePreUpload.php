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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FilePreUpload
 *
 * @package Groovili\RestUploaderBundle\Event
 */
class FilePreUpload extends Event
{
    CONST FILE_PRE_UPLOAD = 'rest_uploader.file.preUpload';
    
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $file;
    
    /**
     * @var Request $request
     */
    protected $request;
    
    /**
     * FilePreUpload constructor.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(UploadedFile $file, Request $request)
    {
        $this->file = $file;
        $this->request = $request;
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
}