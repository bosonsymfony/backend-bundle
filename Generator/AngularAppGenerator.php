<?php

namespace UCI\Boson\BackendBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;


/**
 * Created by PhpStorm.
 * User: killer
 * Date: 27/01/16
 * Time: 10:58
 */
class AngularAppGenerator extends Generator
{

    private $filesystem;

    /**
     * AngularAppGenerator constructor.
     * @param $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generate(BundleInterface $bundle, $appName, $routeFormat)
    {
        $dir = $bundle->getPath();
//        if (file_exists($controllerFile)) {
//            throw new \RuntimeException(sprintf('Controller "%s" already exists', 'Admin'));
//        }

        $basename = strtolower($this->getBasename($bundle->getName()));
        $targetDir = 'bundles/' . preg_replace('/bundle$/', '', strtolower($bundle->getName()));
        $parameters = array(
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle->getName(),
            'format' => array(
                'routing' => $routeFormat
            ),
            'controller' => 'Admin',
            'bundle_base_name' => $basename,
            'app_name' => $appName,
            'target_dir' => $targetDir
        );

        $this->setSkeletonDirs(__DIR__ . '/../Resources/skeleton');

        $this->renderFile('angularApp/js/config.js.twig', $dir . '/Resources/views/Scripts/config.js.twig', $parameters);
        $this->renderFile('angularApp/js/homeCtrl.js.twig', $dir . '/Resources/public/adminApp/controllers/' . $appName . 'HomeCtrl.js', $parameters);
        $this->renderFile('angularApp/js/homeSvc.js.twig', $dir . '/Resources/public/adminApp/services/' . $appName . 'HomeSvc.js', $parameters);
        $this->renderFile('angularApp/js/homeDirective.js.twig', $dir . '/Resources/public/adminApp/directives/' . $appName . 'HomeDirective.js', $parameters);
        $this->renderFile('angularApp/js/homeFilter.js.twig', $dir . '/Resources/public/adminApp/filters/' . $appName . 'HomeFilter.js', $parameters);
        $this->renderFile('angularApp/views/home.html.twig', $dir . '/Resources/public/adminApp/views/home.html', $parameters);
        $this->renderFile('angularApp/controller/Controller.php.twig', $dir . '/Controller/AdminController.php', $parameters);
        $this->renderFile('angularApp/config/backend.yml.twig', $dir . '/Resources/config/backend.yml', $parameters);
        $this->generateRouting($bundle, $routeFormat, $basename, $appName);
    }

    public function getBasename($name)
    {
        return substr($name, 0, -6);
    }

    public function generateRouting(BundleInterface $bundle, $format, $basename, $appName)
    {
        if (!in_array($format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/routing/%s.%s',
            $bundle->getPath(),
            'angular_app',
            $format
        );

        $this->renderFile('angularApp/controller/routing.' . $format . '.twig', $target, array(
            'bundle_base_name' => $basename,
            'bundle' => $bundle->getName(),
            'controller' => 'Admin',
            'app_name' => $appName
        ));
    }
}