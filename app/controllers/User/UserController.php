<?php

namespace User;

use \BaseController;
use \Input;
use \Exception;
use \Users\TemporalUser;
use \Auth;
use \yajra\Pdo\Oci8\Exceptions\Oci8Exception;

class UserController extends BaseController
{

	public function assignTemporalUser()
	{
		try
		{
			$inputs = Input::all();
			if ( $inputs['iduser'] == Auth::user()->id )
				return $this->warningException( 'No puede asignarse a si mismo como usuario temporal' , __FUNCTION__ , __LINE__ , __FILE__ );
			$tempUser = TemporalUser::getAssignment( Auth::user()->id );
			if ( is_null( $tempUser ) )
			{
				$tempUser = new TemporalUser;
				$tempUser->id = $tempUser->lastId() + 1;
				$tempUser->id_user = $inputs[ 'iduser' ];
				$tempUser->id_user_temp = Auth::user()->id;
				$tempUser->save();
				return $this->setRpta();
			}
			else
				return $this->warningException( 'No puede realizar la asignacion porque ya ha realizado una.' , __FUNCTION__ , __LINE__ , __FILE__ );
		}
		catch ( Oci8Exception $e )
        {
            return $this->internalException( $e , __FUNCTION__ , DB );
        }
		catch ( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}

	}

	public function removeTemporalUSer()
	{
		try
		{
			$tempUser = TemporalUser::getAssignment( Auth::user()->id );
			if ( is_null( $tempUser ) )
				return $this->warningException( 'No tiene asignacion para eliminar' , __FUNCTION__ , __LINE__ , __FILE__ );
			else
			{
				$tempUser->delete();
				return $this->setRpta();
			}
		}
		catch ( Oci8Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ , DB );
		}
		catch ( Exception $e )
		{
			return $this->internalException( $e , __FUNCTION__ );
		}
	}
}