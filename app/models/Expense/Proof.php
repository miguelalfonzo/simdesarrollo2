<?php

namespace Expense;

use \Eloquent;

class Proof extends Eloquent
{
    protected $table= TB_MARCA_DOCUMENTO;
    protected $primaryKey = 'id';   

    public function getAddData()
    {
      return $this->select( [ 'id' , 'codigo descripcion' ] )
        ->get();
    }
}