<?php

namespace Expense;

use \Eloquent;

class PlanCta extends Eloquent
{
    protected $table= TB_PLAN_CUENTA;
    protected $primaryKey = 'ctactaextern';
 
    protected function account()
    {
    	return $this->hasOne('Dmkt\Account' , 'num_cuenta');
    }

}