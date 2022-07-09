<?php

namespace App\Validator;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class LessThanOrEqualValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof LessThanOrEqual) {
            throw new UnexpectedTypeException($constraint, LessThanOrEqual::class);
        }

        if (!($value instanceof DateTime)) {
            throw new UnexpectedTypeException($value, 'datetime');
        }

        $value = new Carbon($value);

        if ($constraint->comparison === 'today') {

            if (!$value->lte(Carbon::now())) {

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->setParameter('{{ comparison }}', $constraint->comparison)
                    ->addViolation();
            }
        } else {
            if (!$value->lte(Carbon::now()->sub(CarbonInterval::years(18)))) {

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->setParameter('{{ comparison }}', $constraint->comparison)
                    ->addViolation();
            }
        }
    }
}