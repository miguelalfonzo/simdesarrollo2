<?php

namespace Process;

use \Eloquent;

class ProcessState extends Eloquent
{

	protected $table      = 'ESTADO_PROCESO';
	protected $primaryKey = 'id';

	public static function getPPTOStatusProcess()
	{
		return ProcessState::select( [ 'id' , 'status' ] )->where( 'id' , 1 )->first();
	}

}

