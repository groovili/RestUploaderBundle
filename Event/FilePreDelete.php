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
 * Class FilePreDelete
 *
 * @package Groovili\RestUploaderBundle\Event
 */
class FilePreDelete extends Event
{
    CONST FILE_PRE_DELETE = 'rest_uploader.file.preDelete';
    
    /**
     * @var Request $request
     */
    protected $request;
    
    /**
     * @var \Groovili\RestUploaderBundle\Entity\File
     */
    protected $fileEntity;
    
    /**
     * FilePreDelete constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Groovili\RestUploaderBundle\Entity\File $fileEntity
     */
    public function __construct(Request $request, File
    $fileEntity)
    {
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
     * @return \Groovili\RestUploaderBundle\Entity\File
     */
    public function getFileEntity(): File
    {
        return $this->fileEntity;
    }
}