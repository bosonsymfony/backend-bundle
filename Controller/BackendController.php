<?php

namespace UCI\Boson\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            $errors = array_merge($errors, $this->getAllErrorsMessages($child));
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
