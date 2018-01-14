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

namespace Groovili\RestUploaderBundle\Service;

use Groovili\RestUploaderBundle\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface FileManagerInterface
 * @package Groovili\RestUploaderBundle\Service
 */
interface FileManagerInterface
{
    /**
     * @param UploadedFile $uploadedFile
     * @param bool $private
     * @return File|null
     */
    public function upload(UploadedFile $uploadedFile, bool $private): ?File;

    /**
     * @param File $file
     * @return Response
     */
    public function download(File $file): Response;

    /**
     * @param File $file
     * @return bool
     */
    public function remove(File $file): bool;

    /**
     * @param File $file
     * @return string
     */
    public function getFileRealPath(File $file): string;
}