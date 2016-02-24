<?php

namespace UCI\Boson\BackendBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Parser;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class BackendExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $configBundles = $config['bundles'];
        $bundles = $container->getParameter('kernel.bundles');
        $menu = array();
        $configRoutes = array();
        foreach ($configBundles as $index => $configBundle) {
            if (array_key_exists($configBundle, $bundles)) {
                $refClass = new \ReflectionClass($bundles[$configBundle]);
                $bundleDir = dirname($refClass->getFileName());
                $backendConfig = $bundleDir . '/Resources/config/backend.yml';

                if (file_exists($backendConfig)) {
                    $yaml = new Parser();
                    $value = $yaml->parse(file_get_contents($backendConfig));
                    if (array_key_exists('config_route', $value)) {
                        $configRoutes[] = $value['config_route'];
                    }
                    if (array_key_exists('menu', $value)) {
                        foreach ($value['menu'] as $key => $item) {
                            $menu[$key] = $item;
                        }
                    }
                }
            }
        }

        $container->setParameter('backend.config_routes', $configRoutes);
        $container->setParameter('backend.menu', $menu);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        //doctrine
        $doctrineConfig = $container->getExtensionConfig('doctrine')[0];
        $extensions = array(
            'orm' => array(
                'dql' => array(
                    'string_functions' => array(
                        'cast' => 'UCI\Boson\BackendBundle\Doctrine\ORM\Query\AST\Functions\Cast'
                    )
                )
            )
        );
        $doctrineConfig = array_merge_recursive($doctrineConfig, $extensions);
        $container->prependExtensionConfig('doctrine', $doctrineConfig);

    }


}
