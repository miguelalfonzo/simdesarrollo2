<?php

namespace Maintenance;

use \Eloquent;

class Maintenance extends Eloquent
{
    protected $table= TB_MANTENIMIENTO;
    protected $primaryKey = 'id';
   
}