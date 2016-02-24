<?php
/**
 * Created by PhpStorm.
 * User: killer
 * Date: 9/02/16
 * Time: 16:44
 */

namespace UCI\Boson\BackendBundle\Generator;


use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class FormValidatorGenerator extends Generator
{
    private $filesystem;

    /**
     * FormValidatorGenerator constructor.
     * @param $filesystem
     */
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generate(BundleInterface $bundle, $validatorName)
    {
        $dir = $bundle->getPath();

        $parameters = array(
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle->getName(),
            'validator_name' => $validatorName
        );

        $this->setSkeletonDirs(__DIR__ . '/../Resources/skeleton');

        $this->renderFile('validator/Constraint.php.twig', $dir . '/Validator/Constraints/' . $validatorName . '.php', $parameters);
        $this->renderFile('validator/ConstraintValidator.php.twig', $dir . '/Validator/Constraints/' . $validatorName . 'Validator.php', $parameters);

    }


}