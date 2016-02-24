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

}