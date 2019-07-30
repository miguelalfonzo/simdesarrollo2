<?php

use Validator\ValidatorController;

    Validator::resolver( function( $translator , $data , $rules , $messages )
    {
        return new ValidatorController( $translator , $data , $rules , $messages);
    });