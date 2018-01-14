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

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FileValidatorInterface
 * @package Groovili\RestUploaderBundle\Service
 */
interface FileValidatorInterface
{
    /**
     * @param UploadedFile $uploadedFile
     * @return bool
     */
    public function isSizeValid(UploadedFile $uploadedFile): bool;

    /**
     * @param UploadedFile $uploadedFile
     * @param array $validationArray
     * @return bool
     */
    public static function isByFormat(UploadedFile $uploadedFile, array $validationArray): bool;
}