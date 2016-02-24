<?php

namespace UCI\Boson\BackendBundle\Validator\Constraints;

use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BundleNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $bundle = $value;
        try {
            Validators::validateBundleName($bundle);
        } catch (\Exception $e) {
            $this->context->addViolation($e->getMessage());
            return false;
        }
        return true;
    }
}