<?php

namespace Expense;

use \Eloquent;

class AccountType extends Eloquent
{
    protected $table= TB_TIPO_CUENTA;
    protected $primaryKey = 'id';
   
}