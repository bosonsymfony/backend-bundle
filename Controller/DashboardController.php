<?php

namespace UCI\Boson\BackendBundle\Controller;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use UCI\Boson\BackendBundle\Descriptor\ArrayDescriptor;
use UCI\Boson\BackendBundle\Entity\Bundle;
use UCI\Boson\BackendBundle\Form\BundleType;


class DashboardController extends BackendController
{

    /**
     * @Route(path="/backend/dashboard/bundles", name="backend_dashboard_bundles", options={"expose"=true})
     */
    public function getEnabledBundlesAction()
    {
        $kernel = $this->get('kernel');
        $bundles = array();
        foreach ($kernel->getBundles() as $bundle) {
            $bundles[] = array(
                'name' => $bundle->getName(),
                'namespace' => $bundle->getNamespace(),
                'path' => $bundle->getPath()
            );
        }
        return new Response($this->serialize($bundles));
    }

    /**
     * @Route(path="/backend/dashboard/generate_bundle", name="backend_dashboard_generate_bundle", options={"expose"=true})
     * @Method("POST")
     */
    public function generateBundleAction(Request $request)
    {
        $bundle = new Bundle();
        $form = $this->createForm(new BundleType(), $bundle);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $kernel = $this->get('kernel');
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput(array(
                'command' => 'generate:bundle',
                '--namespace' => $bundle->getNamespace(),
                '--dir' => $kernel->getRootDir() . '/../src',
                '--bundle-name' => $bundle->getName(),
                '--format' => $bundle->getFormat(),
                '--structure' => $bundle->isStructure(),
                '--no-interaction' => true
            ));

            $input->setInteractive(false);

            $output = new BufferedOutput();
            try {
                $application->doRun($input, $output);
            } catch (\Exception $e) {
                return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new Response($output->fetch());
        }

        $errors = array(
            'form_errors' => $this->getAllErrorsMessages($form)
        );

        return new Response($this->serialize($errors), Response::HTTP_INTERNAL_SERVER_ERROR);


    }

    /**
     * @Route(path="/backend/dashboard/routing", name="backend_dashboard_routing", options={"expose"=true})
     */
    public function getRoutingAction()
    {
        $router = $this->get('router')->getRouteCollection()->all();
        $routes = array();
        foreach ($router as $name => $route) {
            $routes[] = array(
                'name' => $name,
                'path' => $route->getPath(),
                'controller' => $route->getDefault('_controller'),
                'methods' => $route->getMethods()
            );
        }
        return new Response($this->serialize($routes));
    }

    /**
     * @Route(path="/backend/dashboard/generate_controller", name="backend_dashboard_generate_controller", options={"expose"=true})
     * @Method("POST")
     */
    public function generateControllerAction(Request $request)
    {
        $bundleName = $request->request->get('bundle');
        $controller = $request->request->get('controller');
        $routeFormat = $request->request->get('route-format');
        $templateFormat = $request->request->get('template-format');
        $actions = $request->request->get('actions');

        // Validate controller data
        try {
            Validators::validateControllerName($bundleName . ':' . $controller);
            Validators::validateFormat($routeFormat);

            list($bundle, $controller) = $this->parseShortcutNotation($bundleName . ':' . $controller);
            $b = $this->get('kernel')->getBundle($bundle);

            if (file_exists($b->getPath() . '/Controller/' . $controller . 'Controller.php')) {
                return new Response("Controller \"$bundle:$controller\" already exists.");
            }
            if (!in_array($templateFormat, array('twig', 'php'))) {
                return new Response("The template format must be twig or php, \"$templateFormat\" given");
            }


            foreach ($actions as $index => $action) {
                $name = $action['name'];
            }
        } catch (\Exception $e) {
            return new Response("Bundle \"$bundleName\" does not exist.");
        }
    }

    /**
     * @Route(path="/backend/dashboard/database", name="backend_dashboard_database", options={"expose"=true})
     */
    public function getDatabaseAction()
    {
        $doctrine = $this->get('doctrine.orm.entity_manager');
        $metadata = $this->getAllMetadata();
        $database = array(
            'params' => $doctrine->getConnection()->getParams(),
            'entities' => array()
        );
        foreach ($metadata as $entity) {
            $database['entities'][] = array(
                'class' => $entity->getName(),
                'field_names' => $entity->getFieldNames()
            );
        }
        return new Response($this->serialize($database));
    }

    /**
     * @return ClassMetadata[]
     */
    public function getAllMetadata()
    {
        $doctrine = $this->get('doctrine.orm.entity_manager');
        $metadata = $doctrine->getMetadataFactory()->getAllMetadata();
        return $metadata;
    }

    /**
     * @Route(path="/backend/dashboard/generate_entity", name="backend_dashboard_generate_entity", options={"expose"=true})
     * @Method("POST")
     */
    public function generateEntityAction()
    {

    }

    /**
     * @Route(path="/backend/dashboard/commands", name="backend_dashboard_commands", options={"expose"=true})
     */
    public function getCommandsAction()
    {
        $kernel = $this->get('kernel');
        $descriptor = new ArrayDescriptor($kernel);

        return new Response(json_encode($descriptor->describeApplication()), 200, array(
            'content-type' => 'application/json'
        ));
    }

    /**
     * @Route(path="/backend/dashboard/execute_command", name="backend_dashboard_execute_command", options={"expose"=true})
     * @Method("POST")
     */
    public function executeCommandAction(Request $request)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $arrayInput = $request->request->all();
        if (array_key_exists('XDEBUG_SESSION_START', $arrayInput)) {
            unset($arrayInput['XDEBUG_SESSION_START']);
        }
        $input = new ArrayInput($arrayInput);

        $input->setInteractive(false);

        $output = new BufferedOutput();
        try {
            $application->doRun($input, $output);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($output->fetch());
    }

    /**
     * @Route(path="/backend/dashboard/generate_command", name="backend_dashboard_generate_command", options={"expose"=true})
     * @Method("POST")
     */
    public function generateCommandAction()
    {

    }

    public function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The controller name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }
}
