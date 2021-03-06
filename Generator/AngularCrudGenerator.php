<?php
/**
 * Created by PhpStorm.
 * User: killer
 * Date: 11/02/16
 * Time: 15:42
 */

namespace UCI\Boson\BackendBundle\Generator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use UCI\Boson\BackendBundle\Command\Util;

class AngularCrudGenerator extends Generator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var BundleInterface
     */
    protected $bundle;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var array
     */
    protected $fieldList;

    /**
     * @var array
     */
    protected $searchField;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string
     */
    protected $routeNamePrefix;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string
     */
    protected $state;

    /**
     * AngularCrudGenerator constructor.
     * @param EntityManager $entityManager
     * @param FormFactory $formFactory
     * @param Filesystem $filesystem
     * @param TwigEngine $templating
     */
    public function __construct(EntityManager $entityManager, FormFactory $formFactory, Filesystem $filesystem, TwigEngine $templating)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->filesystem = $filesystem;
        $this->templating = $templating;
    }


    /**
     * @param BundleInterface $bundle
     * @param $entity
     * @param $format
     * @param $routePrefix
     */
    public function generate(BundleInterface $bundle, $entity, $format, $routePrefix)
    {
        $this->entity = $entity;
        $this->bundle = $bundle;
        $this->setFormat($format);
        $this->metadata = $this->getEntityMetadata();
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = str_replace('/', '_', $routePrefix);
        $this->actions = array('index', 'show', 'new', 'edit', 'delete');

        $this->initFields();

        if (count($this->metadata->identifier) != 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple or no primary keys.');
        }

        $this->setSkeletonDirs(__DIR__ . '/../Resources/skeleton');

        $this->generateControllerClass(true);

        $this->generateFormClass(true);

        // Generate list
        $dir = sprintf('%s/Resources/public/adminApp/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));
        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }
        $this->generateListView($dir);

        // Generate delete dialog
        $this->generateDeleteView($dir);

        // Generate save dialog
        $this->generateSaveView($dir);

        // Generate update dialog
        $this->generateUpdateView($dir);

        // Generate ctrl
        $dir = sprintf('%s/Resources/public/adminApp/controllers/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));
        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }
        $this->generateCtrl($dir);

        // Generate service
        $dir = sprintf('%s/Resources/public/adminApp/services/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));
        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }
        $this->generateSvc($dir);

        $this->generateTestClass();
        $this->generateConfiguration();

        $this->updateBackendConfig();
        $this->updateJsConfig();
    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Generates the controller class only.
     *
     */
    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('BackendBundle:Skeleton:crud/controller.php.twig', $target, array(
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_class' => $entityClass,
            'namespace' => $this->bundle->getNamespace(),
            'entity_namespace' => $entityNamespace,
            'format' => $this->format,
            'field_list' => $this->fieldList,
            'search_field' => $this->searchField
        ));
    }

    /**
     * Generates the form class only
     *
     * @param $forceOverwrite
     */
    protected function generateFormClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Form/%s/%sType.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('BackendBundle:Skeleton:crud/form/FormType.php.twig', $target, array(
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_class' => $entityClass,
            'namespace' => $this->bundle->getNamespace(),
            'entity_namespace' => $entityNamespace,
            'format' => $this->format,
            'field_list' => $this->fieldList,
            'search_field' => $this->searchField
        ));
    }

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateListView($dir)
    {
        $this->renderFile('BackendBundle:Skeleton:crud/views/index.html.twig', $dir . '/list.html', array(
            'entity' => $this->entity,
            'fields' => $this->fieldList['fields'],
        ));
    }

    protected function generateDeleteView($dir)
    {
        $this->renderFile('BackendBundle:Skeleton:crud/views/delete.html.twig', $dir . '/delete-dialog.html', array());
    }

    protected function generateSaveView($dir)
    {
        $this->renderFile('BackendBundle:Skeleton:crud/views/save.html.twig', $dir . '/save-dialog.html', array(
            'form' => $this->form->createView(),
            'entity' => $this->entity
        ));
    }

    protected function generateUpdateView($dir)
    {
        $this->renderFile('BackendBundle:Skeleton:crud/views/update.html.twig', $dir . '/update-dialog.html', array(
            'form' => $this->form->createView(),
            'entity' => $this->entity
        ));
    }

    protected function generateCtrl($dir)
    {
        $form = $this->form->createView();
        $this->renderFile('BackendBundle:Skeleton:crud/js/Ctrl.js.twig', $dir . '/' . lcfirst($this->entity) . 'Ctrl.js', array(
            'bundle' => $this->bundle->getName(),
            'target_dir' => Util::createAppName($this->bundle->getName()),
            'entity' => $this->entity,
            'filename' => lcfirst($this->entity),
            'form' => $this->form->createView()->children,
            'fields' => $this->fieldList['fields']
        ));
    }

    protected function generateSvc($dir)
    {
        $this->renderFile('BackendBundle:Skeleton:crud/js/Svc.js.twig', $dir . '/' . lcfirst($this->entity) . 'Svc.js', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'route_name_prefix' => $this->routeNamePrefix,
            'filename' => lcfirst($this->entity)
        ));
    }

    protected function render($template, $parameters)
    {
        return $this->templating->render($template, $parameters);
    }

    /**
     * Generates the functional test class only.
     *
     */
    protected function generateTestClass()
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $dir = $this->bundle->getPath() . '/Tests/Controller';
        $target = $dir . '/' . str_replace('\\', '/', $entityNamespace) . '/' . $entityClass . 'ControllerTest.php';

        $this->renderFile('BackendBundle:Skeleton:crud/tests/test.php.twig', $target, array(
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity' => $this->entity,
            'bundle' => $this->bundle->getName(),
            'entity_class' => $entityClass,
            'namespace' => $this->bundle->getNamespace(),
            'entity_namespace' => $entityNamespace,
            'actions' => $this->actions,
            'form_type_name' => strtolower(str_replace('\\', '_', $this->bundle->getNamespace()) . ($parts ? '_' : '') . implode('_', $parts) . '_' . $entityClass),
        ));
    }

    /**
     * Generates the routing configuration.
     *
     */
    protected function generateConfiguration()
    {
        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/routing/%s.%s',
            $this->bundle->getPath(),
            strtolower(str_replace('\\', '_', $this->entity)),
            $this->format
        );

        $this->renderFile('BackendBundle:Skeleton:crud/config/routing.' . $this->format . '.twig', $target, array(
            'actions' => $this->actions,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
        ));
    }

    /**
     * @return ClassMetadata
     */
    protected function getEntityMetadata()
    {
        $entityClass = $this->bundle->getName() . ':' . $this->entity;
        return $this->entityManager->getClassMetadata($entityClass);
    }

    /**
     * @return void
     */
    protected function initFields()
    {
        $entityClass = $this->bundle->getNamespace() . '\\Entity\\' . $this->entity;
        $reflection = new \ReflectionClass($entityClass);
        $entity = $reflection->newInstance();

        $propertyAccessor = new PropertyAccessor(false, true);

        $fieldMappings = $this->metadata->fieldMappings;
        $form = $this->formFactory->createNamedBuilder(strtolower($this->bundle->getName()) . '_' . $this->routeNamePrefix, 'form', $entity, array(//            'csrf_protection' => false
        ));

        $listFields = array(
            'fields' => array(),
            'associations' => array()
        );

        foreach ($fieldMappings as $index => $fieldMapping) {
            if ($propertyAccessor->isReadable($entity, $index)) {
                $listFields['fields'][] = $index;
                if ($index != 'id') {
                    $form->add($index);
                }
            }
        }

        $associations = $this->metadata->getAssociationNames();

        foreach ($associations as $index => $association) {
            $associationMetadata = $this->entityManager->getClassMetadata($this->metadata->getAssociationTargetClass($association));
            $listFields['associations'][$association] = array(
                'fields' => $associationMetadata->fieldNames,
                'associations' => array()
            );
        }

        $this->fieldList = $listFields;
        $this->searchField = $fieldMappings;
        $this->form = $form->getForm();
    }

    protected function updateBackendConfig()
    {
        $configFile = $this->bundle->getPath() . '/Resources/config/backend.yml';
        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents($configFile));

        foreach ($data['menu'] as $key => $value) {
            if ($value['type'] == 'toggle') {
                if (!array_key_exists('children', $value)) {
                    $data['menu'][$key]['children'] = array();
                }
                $data['menu'][$key]['children'][strtolower($this->entity)] = array(
                    'title' => Util::transformTitle($this->entity),
                    'type' => 'link',
                    'state' => $value['includes'] . '.' . strtolower($this->entity)
                );
                $this->state = $value['includes'];
                break;
            }
        }

        $dumper = new Dumper();
        file_put_contents($configFile, $dumper->dump($data, 5));
    }

    protected function updateJsConfig()
    {
        $file = $this->render('BackendBundle:Skeleton:crud/js/routeConfig.js.twig', array(
            'actions' => $this->actions,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'state' => $this->state,
            'target_dir' => Util::createAppName($this->bundle->getName()),
            'filename' => lcfirst($this->entity)
        ));

        print_r($file . "\n");
    }

}