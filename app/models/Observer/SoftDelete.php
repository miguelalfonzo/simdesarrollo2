<?php

namespace Observer;

use \Eloquent;
use \Auth;

class SoftDelete 
{
	public function creating( Eloquent $model )
    {
        $model->created_by = Auth::user()->id;
        $model->updated_by = Auth::user()->id;
    }

    public function updating( Eloquent $model )
    {
        $model->updated_by = Auth::user()->id;
    }

    public function deleting( Eloquent $model )
    {
    	$model->deleted_by = Auth::user()->id;
        $model->save();
    }
}