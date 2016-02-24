<?php

namespace UCI\Boson\BackendBundle\Validator\Constraints;

use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FormatValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $format = $value;

        try {
            Validators::validateFormat($format);
        } catch (\Exception $e) {
            $this->context->addViolation($e->getMessage());
            return false;
        }
        return true;
    }
}