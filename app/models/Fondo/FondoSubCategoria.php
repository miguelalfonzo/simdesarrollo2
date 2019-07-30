<?php

namespace Fondo;

use \Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Auth;
use \Fondo\FondoSupervisor;
use \Fondo\FondoGerProd;
use \Fondo\FondoInstitucional;
use Illuminate\Database\Eloquent\Collection;
use DB;
class FondoSubCategoria extends Eloquent
{

	use SoftDeletingTrait;

	protected $table      = TB_FONDO_CATEGORIA_SUB;
	protected $primaryKey = 'id';
	protected $fillable   = array('id','descripcion', 'fondos_categorias_id');

	public function nextId()
	{
	    $nextId = $this->withTrashed()
	    	->select( 'id' )
	    	->orderBy( 'id' , 'desc' )
	    	->first();
        if ( is_null( $nextId ) )
            return 1;
        else
            return $nextId->id + 1;
    }

    protected static function FondoSP()
    {
       $userTipe = Auth::user()->type;
        $row = \DB::transaction(function($conn) use ($userTipe){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_FONDO_SUBCATEGORIA(:userTipe, :data); END;');
                $stmt->bindParam(':userTipe', $userTipe, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }

	protected static function order()
	{
		return FondoSubCategoria::orderBy( 'descripcion' , 'ASC' )->get();
	}

	public function categoria()
	{
		return $this->belongsTo( 'Fondo\FondoCategoria' , 'id_fondo_categoria' );
	}

	protected function fondoMktType()
	{
		return $this->hasOne( 'Fondo\FondoMktType' , 'codigo' , 'tipo' );
	}
	
	protected function fromSupFund()
	{
		return $this->hasMany( 'Fondo\FondoSupervisor' , 'subcategoria_id' );
	}

	protected function fromGerProdFund()
	{
		return $this->hasMany( 'Fondo\FondoGerProd' , 'subcategoria_id' );
	}

	protected function fromInstitutionFund()
	{
		return $this->hasMany( 'Fondo\FondoInstitucional' , 'subcategoria_id' );
	}

	public function fund()
	{
		if ( trim( $this->tipo ) == SUP )
		{
			return $this->hasMany( 'Fondo\FondoSupervisor' , 'subcategoria_id' );
		}
		elseif ( in_array( trim( $this->tipo ) , array( GER_PROD , GER_PROM ) ) )
		{
			return $this->hasMany( 'Fondo\FondoGerProd' , 'subcategoria_id' );
		}
		elseif ( trim( $this->tipo ) == 'I' )
		{
			return $this->hasMany( 'Fondo\FondoInstitucional' , 'subcategoria_id' );
		}
	}

	// protected function getRolFunds( $typeCode )
	// {
	// 	return FondoSubCategoria::where( 'trim( tipo )' , $typeCode )->get();
	// }


	protected static function getRolFundsSP( $typeCode )
    {
        $tipo = $typeCode;
        $row = \DB::transaction(function($conn) use ($tipo){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_ROL_FUNDS(:tipo, :data); END;');
                $stmt->bindParam(':tipo', $tipo, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }



	protected static function orderWithTrashed()
	{
		return FondoSubCategoria::withTrashed()
			->where( 'trim( tipo )' , '<>' , 'O' )
			->orderBy( 'id_fondo_categoria' , 'ASC' )
			->orderBy( 'position' , 'ASC' )
			->get();
	}

	public function getFondos( $year )
	{
		if( $this->tipo == 'S ' )
		{
			$data = FondoSupervisor::where( 'anio' , $year )->where( 'subcategoria_id' , $this->id )->get();
			return $data;
		} 
		elseif( $this->tipo == 'P ' || $this->tipo == 'GP' )
		{
			$data = FondoGerProd::where( 'anio' , $year )->where( 'subcategoria_id' , $this->id )->get();
			return $data;
		}
		elseif( $this->tipo == 'I ' )
		{
			$data = FondoInstitucional::where( 'anio' , $year )->where( 'subcategoria_id' , $this->id )->get();
			return $data;
		}
		else
		{
			return null;
		}
		//return $this->{ $this->relacion }->where( 'anio' , $year );
	}
	
}