<?php

namespace UCI\Boson\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UCI\Boson\BackendBundle\Validator\Constraints as BackendAssert;

class Bundle
{
    /**
     * @BackendAssert\BundleName()
     */
    private $name;

    /**
     * @BackendAssert\Format()
     */
    private $format;

    /**
     * @BackendAssert\BundleNamespace()
     */
    private $namespace;

    /**
     * @var boolean
     */
    private $structure;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Bundle
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return Bundle
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     *
     * @return Bundle
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return boolean
     */
    public function isStructure()
    {
        return $this->structure;
    }

    /**
     * @param boolean $structure
     *
     * @return Bundle
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }


}
