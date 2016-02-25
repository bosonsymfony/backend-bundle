<?php

namespace UCI\Boson\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

class BackendController extends Controller
{

    public function serialize($data, $format = 'json')
    {
        $serializer = $this->get('jms_serializer');
        return $serializer->serialize($data, $format);
    }

    public function jsResponse($view, array $parameters = array())
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/javascript');
        return $this->render($view, $parameters, $response);
    }

    /**
     * @Route(path="/backend", name="backend_homepage")
     */
    public function homeAction()
    {
        return $this->render('BackendBundle:Default:index.html.twig', array(// ...
            'config_routes' => $this->getParameter('backend.config_routes')
        ));
    }

    /**
     * @Route(path="/backend/views/aside.html", name="backend_aside")
     */
    public function getAsideAction()
    {
        return $this->render('BackendBundle:Partials:aside.html.twig', array(
            'menu' => $this->getParameter('backend.menu')
        ));
    }

    /**
     * @Route(path="/backend/views/layout.html", name="backend_layout")
     */
    public function getLayoutAction()
    {
        return $this->render('BackendBundle:Partials:layout.html.twig');
    }

    /**
     * @Route(path="/backend/views/content.html", name="backend_content")
     */
    public function getContentAction()
    {
        return $this->render('BackendBundle:Partials:content.html.twig');
    }

    /**
     * @Route(path="/backend/scripts/router.js", name="backend_router")
     */
    public function getRouterAction()
    {
        return $this->jsResponse('BackendBundle:Scripts:router.js.twig');
    }

    /**
     * @Route(path="/backend/scripts/config.js", name="backend_config")
     */
    public function getConfigAction()
    {
        return $this->jsResponse('BackendBundle:Scripts:config.js.twig');
    }

    /**
     * @Route(path="/backend/scripts/app.js", name="backend_app")
     */
    public function getAppAction()
    {
        return $this->jsResponse('BackendBundle:Scripts:app.js.twig');
    }

    /**
     * @Route(path="/backend/authenticated_user", name="backend_auth_user")
     */
    public function getAuthenticatedUser()
    {
        return new Response($this->serialize($this->getUser()));
    }

    /**
     * @Route(path="/backend/cache_clear", name="backend_cache_clear")
     */
    public function cacheClearAction()
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'cache:clear',
            '--env' => $kernel->getEnvironment()
        ));
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->doRun($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response(str_replace("\n", "<br>", $content));
    }

    /**
     * @Route(path="/backend/assets_install", name="backend_assets_install")
     */
    public function installAssetsAction()
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'assets:install',
            '--symlink' => true,
            'target' => $kernel->getRootDir() . '/../web'
        ));
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->doRun($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response(str_replace("\n", "<br>", $content));
    }

    /**
     * @Route(path="/backend/commands_list", name="backend_assets_install")
     */
    public function listCommandsAction()
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'list',
            '--format' => 'json'
        ));
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->doRun($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response(str_replace("\n", "<br>", $content));
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    public function getAllErrorsMessages(Form $form)
    {
        $errors = array();

        if ($err = $this->childErrors($form)) {
            $errors[$form->getName()] = $err;
        }

        foreach ($form->all() as $key => $child) {
            if ($err = $this->childErrors($child)) {
                $errors[$key] = $err;
            }
        }
        return $errors;
    }

    /**
     * @param Form $form
     * @return array
     */
    private function childErrors(Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            array_push($errors, $error->getMessage());
        }
        return $errors;
    }

}
