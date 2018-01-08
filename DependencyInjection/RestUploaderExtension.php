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

namespace Groovili\RestUploaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class RestUploaderExtension
 *
 * @package Groovili\RestUploaderBundle\DependencyInjection
 */
class RestUploaderExtension extends Extension
{
    protected const DEFAULT_DIR_PERMISSIONS = 0777;

    protected const DEFAULT_PUBLIC_FILES_PATH = '../web/files';

    protected const DEFAULT_PRIVATE_FILES_PATH = '../private';

    protected const DEFAULT_FILE_MAX_SIZE = 25;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container,
            new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $kernelRootDir = $container->getParameter('kernel.root_dir') . '/';

        // Validate public dir ir set it from default config
        if (isset($config['public_dir']) && is_string($config['public_dir'])) {
            if (!self::validateDir($kernelRootDir . $config['public_dir'])) {
                self::createDir($kernelRootDir . $config['public_dir']);
            }
        } else {
            throw new InvalidArgumentException('Invalid parameter for "public_dir"');
        }

        // Validate private dir or create it from default config
        if (isset($config['private_dir']) && is_string($config['private_dir'])) {
            if (!self::validateDir($kernelRootDir . $config['private_dir'])) {
                self::createDir($kernelRootDir . $config['private_dir']);
            }
        } else {
            $config['private_dir'] = self::DEFAULT_PRIVATE_FILES_PATH;
            if (!self::validateDir($kernelRootDir . $config['private_dir'])) {
                self::createDir($kernelRootDir . $config['private_dir']);
            }
        }

        // Validate file size or set default
        if (isset($config['file_max_size'])) {
            self::validateMaxFileSize($config['file_max_size']);
        } else {
            // Set 25 MB limit for files by default
            $config['file_max_size'] = self::DEFAULT_FILE_MAX_SIZE;
        }

        if (isset($config['allowed_extensions'])) {
            self::validateExtensionsArray($config['allowed_extensions']);
        } else {
            // Allow all extensions by default
            $config['allowed_extensions'] = [];
        }

        // Once the services definition are read, we get our service and
        // add a method call to setConfig()
        $manager = $container->getDefinition('rest_uploader.manager');
        $manager->addMethodCall('setConfig', [
            $config,
        ]);

        $validator = $container->getDefinition('rest_uploader.validator');
        $validator->addMethodCall('setConfig', [
            $config,
        ]);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private static function validateDir(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        // TODO: Check permissions

        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private static function createDir(string $path): bool
    {
        if (!is_dir($path)) {
            if (!mkdir($path, self::DEFAULT_DIR_PERMISSIONS, true)) {
                throw new \LogicException('Unable to create following directory ' . $path);
            }
        }

        @chmod($path, self::DEFAULT_DIR_PERMISSIONS & ~umask());

        return true;
    }

    /**
     * @param int $file_size
     *
     * @return bool
     */
    private static function validateMaxFileSize(int $file_size): bool
    {
        if (!is_numeric($file_size) || !is_integer($file_size)) {
            throw new InvalidArgumentException('Invalid parameter for "file_max_size". Should be integer.');
        }

        return true;
    }

    /**
     * @param array $extensions
     *
     * @return bool
     */
    private static function validateExtensionsArray(array $extensions): bool
    {
        foreach ($extensions as $extension) {
            if (!is_string($extension)) {
                throw new InvalidArgumentException('Invalid parameter for "allowed_extensions". Should be string.');
            }
        }

        return true;
    }
}
