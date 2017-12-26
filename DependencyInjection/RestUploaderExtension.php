<?php
    /**
     *
     *  * This file is part of the RestUploaderBundle package.
     *  * (c) groovili
     *  * For the full copyright and license information, please view the LICENSE
     *  * file that was distributed with this source code.
     *
     */
    declare(strict_types = 1);
    
    namespace Groovili\RestUploaderBundle\DependencyInjection;
    
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\HttpKernel\DependencyInjection\Extension;
    use Symfony\Component\DependencyInjection\Loader;
    
    /**
     * Class RestUploaderExtension
     *
     * @package Groovili\RestUploaderBundle\DependencyInjection
     */
    class RestUploaderExtension extends Extension
    {
        /**
         * {@inheritdoc}
         */
        public function load(array $configs, ContainerBuilder $container)
        {
            $configuration = new Configuration();
            $config = $this->processConfiguration($configuration, $configs);
            
            $loader = new Loader\YamlFileLoader($container,
              new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');
            
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
    }
