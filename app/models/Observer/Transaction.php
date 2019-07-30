<?php

namespace Observer;

use \Eloquent;
use \Auth;

class Transaction 
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
}
    /*protected $validator;

    protected $rules = [];

    public function __construct(Illuminate\Validation\Factory $validator){
        $this->validator = $validator->make([], $this->rules);
    }

    public function saving(Eloquent $model){
        $this->validator->setData($model->getAttributes());

        $this->setConditionalRules($model);

        if($this->validator->fails()){
            $model->setAttribute('errors', $this->validator->errors());
            return false;
        }
    }*/

    /*public function saved(Eloquent $model){}
    
    public function updated(Eloquent $model){}
    
    public function created(Eloquent $model){}

    public function deleting(Eloquent $model){}

    public function deleted(Eloquent $model){}

    public function restoring(Eloquent $model){}

    public function restored(Eloquent $model){}

    protected function setConditionalRules(Eloquent $model){}*/