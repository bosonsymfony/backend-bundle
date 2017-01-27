<?php

namespace UCI\Boson\BackendBundle\Validator\Constraints;

use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BundleNamespaceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $namespace = $value;

        try {
            Validators::validateBundleNamespace($namespace);
        } catch (\Exception $e) {
            $this->context->addViolation($e->getMessage());
            return false;
        }
        return true;
    }
}