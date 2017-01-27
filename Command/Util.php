<?php
/**
 * Created by PhpStorm.
 * User: rosi
 * Date: 20/02/16
 * Time: 19:51
 */

namespace UCI\Boson\BackendBundle\Command;


class Util
{
    /**
     * @param string $bundle
     * @return string
     */
    static public function createAppName($bundle)
    {
        return preg_replace('/bundle$/', '', strtolower($bundle));
    }

    static function transformTitle($string)
    {
        $vowels = array("a", "e", "i", "o", "u");
        $last = substr($string, -1);
        $ended = (in_array($last, $vowels)) ? "s" : "es";

        return $string . $ended;
    }

}