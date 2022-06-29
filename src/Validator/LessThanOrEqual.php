<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LessThanOrEqual extends Constraint
{
    //TODO modifier le message plus proprement
    public $message = 'La valeur "{{ string }}" ne doit pas dépasser {{ comparison }}';
    public $comparison = 'today'; // If the constraint has configuration options, define them as public properties

    /* public function getRequiredOptions(): array
     {
         return ['mode'];
     }*/
}