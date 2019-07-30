<?php

namespace Validator;

use \Log;

class ValidatorController extends \Illuminate\Validation\Validator 
{

	public function validateEach( $attribute , $value , $parameters )
    {
        $ruleName = array_shift($parameters);
        $rule = $ruleName.(count($parameters) > 0 ? ':'.implode(',', $parameters) : '');

        foreach ( $value as $arrayKey => $arrayValue )
            $this->validate($attribute.'.'.$arrayKey, $rule);
        // Always return true, since the errors occur for individual elements.
        return true;
    }

    public function validateSumequal( $attribute , $value , $parameters )
    {
        $total = 0;
        
        foreach ( $value as $arrayKey => $arrayValue )
            $total += $arrayValue;
        
        $rule = 'same:'.$parameters[0];

        $newAttribute = $attribute.'_total';

        $this->data[ $newAttribute ] = (string) $total;
        $this->validate( $newAttribute , 'same:monto' );
        return true;
    }

    protected function getAttribute($attribute)
    {
        // Get the second to last segment in singular form for arrays.
        // For example, `group.names.0` becomes `name`.
        if (str_contains($attribute, '.'))
        {
            $segments = explode('.', $attribute);
            $attribute = str_singular($segments[count($segments) - 2]) . ' ' . ( $segments[1] + 1 );
        }

        return parent::getAttribute($attribute);
    }
}