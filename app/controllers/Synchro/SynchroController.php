<?php

namespace Synchro;

use \BaseController;
use \Users\Personal;
use \View;

class SynchroController extends BaseController
{
	public function viewSupRep()
    {
    	$rpta = Personal::viewSupRep();
    	return View::make( 'synchro.supRep.view' , [ 'rpta' => $rpta ] );    
    }


    public function updateSupRep()
    {
    	return Personal::updateSupRep();
    }

}
