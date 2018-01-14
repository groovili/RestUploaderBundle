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
use Groovili\RestUploaderBundle\Event\FilePostUpload;
use Groovili\RestUploaderBundle\Event\FilePreDelete;
use Groovili\RestUploaderBundle\Event\FilePreDownload;
use Groovili\RestUploaderBundle\Event\FilePreGetPath;
use Groovili\RestUploaderBundle\Event\FilePreUpload;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileManager
 *
 * @package Groovili\RestUploaderBundle\Service
 */
class FileManager implements FileManagerInterface
{
    /**
     * @var string
     */
    private $kernelRoot;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;

    /**
     * @var array
     */
    private $config;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * FileManager constructor.
     *
     * @param string $kernelRoot
     */
    public function __construct(
        string $kernelRoot,
        Filesystem $filesystem,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->kernelRoot = $kernelRoot;
        $this->fileSystem = $filesystem;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     * @param string|null $path
     *
     * @return File|null
     */
    public function upload(
        UploadedFile $uploadedFile,
        bool $private = false
    ): ?File {
        $event = new FilePreUpload($uploadedFile);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch(FilePreUpload::FILE_PRE_UPLOAD,
            $event);

        $uploadPath = $this->kernelRoot . '/' . $this->config['public_dir'];
        $scheme = File::SCHEME['Public'];

        if (true === $private) {
            $uploadPath = $this->kernelRoot . '/' . $this->config['private_dir'];
            $scheme = File::SCHEME['Private'];
        }

        try {
            $target = $uploadedFile->move($uploadPath,
                md5($uploadedFile->getBasename()) . '.' . $uploadedFile->getClientOriginalExtension());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $file = new File($scheme, $uploadedFile->getClientOriginalName(),
            $target->getExtension(), $target->getSize(),
            '/' . md5($uploadedFile->getBasename()) . '.' . $uploadedFile->getClientOriginalExtension());

        $event = new FilePostUpload($uploadedFile, $file);
        $dispatcher->dispatch(FilePostUpload::FILE_POST_UPLOAD, $event);

        return $file;
    }

    /**
     * @param \Groovili\RestUploaderBundle\Entity\File $file
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download(File $file): Response
    {
        $download = file_get_contents($this->getFileRealPath($file));

        $event = new FilePreDownload($file);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch(FilePreDownload::FILE_PRE_DOWNLOAD,
            $event);

        $headers = [
            'Content-Type' => $file->getExt(),
            'Content-Disposition' => 'inline; filename="' . $file->getName() . '"',
        ];

        return new Response($download, 200, $headers);
    }

    /**
     * @param \Groovili\RestUploaderBundle\Entity\File $file
     *
     * @return bool
     */
    public function remove(File $file): bool
    {
        $event = new FilePreDelete($file);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch(FilePreDelete::FILE_PRE_DELETE, $event);

        try {
            $this->fileSystem->remove($this->getFileRealPath($file));
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    public function getFileRealPath(File $file): string
    {
        $kernelRoot = $this->kernelRoot;

        $event = new FilePreGetPath($file, $kernelRoot);
        $dispatcher = $this->dispatcher;
        $dispatcher->dispatch(FilePreGetPath::FILE_PRE_GET_PATH, $event);

        if (File::SCHEME['Private'] === $file->getScheme()) {
            return $kernelRoot . '/' . $this->config['private_dir'] . $file->getPath();
        }

        return $kernelRoot . '/' . $this->config['public_dir'] . $file->getPath();
    }
}