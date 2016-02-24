<?php
/**
 * Created by PhpStorm.
 * User: killer
 * Date: 18/02/16
 * Time: 8:46
 */

namespace UCI\Boson\BackendBundle\Twig;


use Symfony\Component\DependencyInjection\Container;

class BackendExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('underscore', array($this, 'underscore')),
            new \Twig_SimpleFilter('space_separator', array($this, 'spaceSeparator')),
            new \Twig_SimpleFilter('starts_with', array($this, 'startsWith')),
            new \Twig_SimpleFilter('ends_with', array($this, 'endsWith'))
        );
    }

    /**
     * @param string $string
     * @return string
     */
    public function underscore($string)
    {
        return Container::underscore($string);
    }

    /**
     * @param string $string
     * @return string
     */
    public function spaceSeparator($string)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1 \\2', '\\1 \\2'), str_replace(' ', '.', $string)));
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public function getName()
    {
        return 'backend_extension';
    }
}