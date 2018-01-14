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
 * Class FileValidator
 *
 * @package Groovili\RestUploaderBundle\Service
 */
class FileValidator implements FileValidatorInterface
{
    /**
     * @var array
     */
    protected static $defaultDocumentExtensions = [
        'xls',
        'xlt',
        'xlm',
        'xlsx',
        'xlsm',
        'xltx',
        'xltm',
        'xlsb',
        'xla',
        'xlam',
        'xll',
        'xlw',
        'docx',
        'docm',
        'dotx',
        'dotm',
        'doc',
        'wpd',
        'xml',
        'psw',
        'rtf',
        'csv',
        'txt',
    ];

    /**
     * @var array
     */
    protected static $defaultImageExtensions = [
        'ani',
        'bmp',
        'cal',
        'fax',
        'gif',
        'img',
        'jbg',
        'jpe',
        'jpe',
        'jpeg',
        'jpg',
        'mac',
        'pbm',
        'pcd',
        'pcx',
        'pct',
        'pgm',
        'png',
        'ppm',
        'psd',
        'ras',
        'tga',
        'tiff',
        'wma',
    ];

    /**
     * @var array
     */
    protected static $defaultArchiveExtensions = [
        's7z',
        '7z',
        'zip',
        'zipx',
        'rar',
        'tar.gz',
        'tgz',
        'tar',
    ];

    /**
     * @var array
     */
    protected static $defaultVideoExtensions = [
        'mpv',
        'mpeg',
        'mpg',
        'ogg',
        'webm',
        'wmv',
        'vob',
        'mp2',
        'mp4',
        'm4p',
        'm4v',
        'mov',
        'mkv',
        'flv',
        'f4v',
        'avi',
        '3gp',
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $kernelRoot;

    /**
     * FileValidator constructor.
     *
     * @param string $kernelRoot
     */
    public function __construct(string $kernelRoot)
    {
        $this->kernelRoot = $kernelRoot;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function isSizeValid(UploadedFile $file): bool
    {
        // Upload size in MB
        $size = $file->getSize() / 1000000;

        if ($this->config['file_max_size'] >= $size) {
            return true;
        }

        return false;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param array $validationArray
     *
     * @return bool
     */
    public static function isByFormat(
        UploadedFile $file,
        array $validationArray
    ): bool {
        if (count($validationArray) > 0) {
            if (!in_array(strtolower($file->getClientOriginalExtension()),
                $validationArray)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function isExtensionAllowed(UploadedFile $file): bool
    {
        return self::isByFormat($file, $this->config['allowed_extensions']);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function isDocument(UploadedFile $file): bool
    {
        return self::isByFormat($file, self::$defaultDocumentExtensions);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function isImage(UploadedFile $file): bool
    {
        return self::isByFormat($file, self::$defaultImageExtensions);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function isArchive(UploadedFile $file): bool
    {
        return self::isByFormat($file, self::$defaultArchiveExtensions);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function isVideo(UploadedFile $file): bool
    {
        return self::isByFormat($file, self::$defaultVideoExtensions);
    }
}