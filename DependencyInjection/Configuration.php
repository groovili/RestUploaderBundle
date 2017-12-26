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
    
    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;
    
    /**
     * Class Configuration
     *
     * @package Groovili\RestUploaderBundle\DependencyInjection
     */
    class Configuration implements ConfigurationInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('rest_uploader');
            
            $rootNode->children()
                     ->scalarNode('public_dir')
                     ->defaultValue('web/assets/files')
                     ->cannotBeEmpty()
                     ->end()
                     ->scalarNode('private_dir')
                     ->defaultValue('private')
                     ->end()
                     ->arrayNode('allowed_extensions')
                     ->end()
                     ->integerNode('file_max_size')
                     ->defaultValue(25)
                     ->end();
            
            return $treeBuilder;
        }
    }
