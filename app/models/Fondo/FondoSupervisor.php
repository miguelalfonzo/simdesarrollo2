<?php

namespace Fondo;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use \Eloquent;
use \Auth;
use \DB;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class FondoSupervisor extends Eloquent
{

	use SoftDeletingTrait;

	protected $table      = TB_FONDO_SUPERVISOR;
	protected $primaryKey = 'id';

	protected function getSaldoDisponibleAttribute()
	{
		return round( $this->saldo - $this->retencion , 2 , PHP_ROUND_HALF_DOWN );
	}

	public function subCategoria()
	{
		return $this->hasOne( 'Fondo\FondoSubCategoria' , 'id' , 'subcategoria_id' );
	}

	public function marca()
	{
		return $this->belongsTo( 'Dmkt\Marca', 'marca_id' );
	}

	protected function sup()
	{
        return $this->hasOne( 'Users\Personal', 'user_id' , 'supervisor_id' );//No tiene informacion en el campo tipo->where( 'tipo' , '=' , 'S' );
    }

	protected static function order()
	{
		return FondoSupervisor::select( [ 'id' , 'subcategoria_id' , 'marca_id' , 'supervisor_id' , 'saldo' , 'retencion' ] )
				->orderBy( 'updated_at' , 'desc' )->get();
	}

	protected static function orderWithTrashed()
	{
		$now = Carbon::now();
		$fundDataSql = FondoSupervisor::select( [ 'id' , 'subcategoria_id' , 'marca_id' , 'supervisor_id' , 'saldo' , 'retencion' ] )
			->where( 'anio' , '=' , $now->format( 'Y' ) )
			->orderBy( 'updated_at' , 'desc' )->withTrashed();

		if( ! in_array( Auth::user()->type , [ GER_PROM , GER_COM ] ) )
		{
			//OTROS USUARIOS ASIGNA UNA CATEGORIA INEXISTENTE
			$fundDataSql->where( 'subcategoria_id' , 0 );
		}

		return $fundDataSql->get();
	}	

	protected function setSaldoAttribute( $value )
	{
		$this->attributes[ 'saldo' ] = round( $value , 2 , PHP_ROUND_HALF_DOWN );
	}

	protected function setRetencionAttribute( $value )
	{
		$this->attributes[ 'retencion' ] = round( $value , 2 , PHP_ROUND_HALF_DOWN );
	}

	protected function getFullNameAttribute()
	{
		return $this->SubCategoria->descripcion . ' | ' . $this->marca->descripcion . ' | '  . $this->sup->full_name;
	}

	protected function getMiddleNameAttribute()
	{
		return $this->marca->descripcion . ' | '  . $this->sup->full_name;
	}

	protected function getApprovalProductNameAttribute()
	{
		return $this->subCategoria->descripcion . ' | ' . $this->marca->descripcion . ' S/.' . $this->saldo_disponible ;
	}

	public function getDetailNameAttribute()
    {
    	$subCategory = $this->subCategoria;
        return $this->marca->descripcion . ' | ' . $subCategory->categoria->descripcion . ' | ' . $subCategory->descripcion;
    }

	protected static function totalAmount( $subcategory , $supervisorId )
	{
		$model  =	FondoSupervisor::select( 'sum( saldo ) saldo , sum( retencion ) retencion , sum( saldo - retencion ) saldo_disponible , subcategoria_id' )
				  		->where( 'subcategoria_id' , $subcategory )->where( 'supervisor_id' , $supervisorId )
						->groupBy( 'subcategoria_id' )
						->first();
		return $model;
	}

	// protected static function getSupFund( $category )
	// {
	// 	$now = Carbon::now();
	// 	$supFunds = FondoSupervisor::select( 'subcategoria_id , marca_id , round( saldo , 2 ) saldo , retencion , ( saldo - retencion ) saldo_disponible' )
	// 			   ->where( 'supervisor_id' , Auth::user()->id )
	// 			   ->where( 'anio' , '=' , $now->format( 'Y' ) )
	// 			   ->orderBy( 'subcategoria_id' )
	// 			   ->with( 'subcategoria' , 'marca' );

	// 	if( $category != 0 )
	// 	{
	// 		$supFunds = $supFunds->where( 'subcategoria_id' , $category );
	// 	}
		
	// 	return $supFunds->get();
	// }

	public static function getSupFundSP( $category){

		$categorys = $category;
		$userID=Auth::user()->id;
        $row = \DB::transaction(function($conn) use ($categorys,$userID){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_FONDOS_SUP(:userid,:categorys ,:data); END;');
                $stmt->bindParam(':userid', $userID, \PDO::PARAM_STR);
                $stmt->bindParam(':categorys', $categorys, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);

	}

}