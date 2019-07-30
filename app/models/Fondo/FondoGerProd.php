<?php

namespace Fondo;

use \Eloquent;
use \Carbon\Carbon;
use \Auth;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection;
class FondoGerProd extends Eloquent
{

	use SoftDeletingTrait;

	protected $table      = TB_FONDO_GERENTE_PRODUCTO;
	protected $primaryKey = 'id';
	protected $fillable   = array('id','fondos_subcategoria_id','marca_id');

	protected function getSaldoDisponibleAttribute()
	{
		return round( $this->saldo - $this->retencion , 2 , PHP_ROUND_HALF_DOWN );
	}

	public function fondosSubCategorias()
	{
		return $this->belongsTo('Maintenance\FondosSubCategorias', 'fondos_subcategoria_id' );
	}

	public function marca()
	{
		return $this->belongsTo('Dmkt\Marca', 'marca_id' );
	}

	public function subCategoria()
	{
		return $this->hasOne( 'Fondo\FondoSubCategoria' , 'id' , 'subcategoria_id' );
	}

	protected static function order()
	{
		return FondoGerProd::orderBy( 'updated_at' , 'desc' )->get();
	}

	protected static function orderWithTrashed()
	{
		$now = Carbon::now();
		$fundDataSql = FondoGerProd::select( [ 'id' , 'subcategoria_id' , 'marca_id' , 'saldo' , 'retencion' ] )
			->where( 'subcategoria_id' , '<>' , 31 )
			->where( 'anio' , '=' , $now->format( 'Y' ) )
			->orderBy( 'updated_at' , 'DESC' )->withTrashed();
		if( Auth::user()->type != GER_COM )
		{
			if( Auth::user()->type == GER_PROD )
			{
				//CATEGORIAS ASIGNADAS AL GERENTE DE PRODUCTO
				$gerProdSubCategoryIds = [ 1 , 2 , 3 , 4 , 6 , 7 , 8 , 9 , 13 , 14 , 15 , 16 , 17 , 18 , 19 , 20 , 21 , 22 , 23 , 24 , 25 , 26 , 27 , 28 , 29 , 30 ];
				$fundDataSql->whereIn( 'subcategoria_id' , $gerProdSubCategoryIds );
			}
			elseif( Auth::user()->type == GER_PROM )
			{
				//CATEGORIAS ASIGNADAS AL GERENTE DE PROMOCION QUE NO PERTENCE A LA TABLA DE SUPERVISOR O INSTITUCIONAL
				$gerPromSubCategoryIds = [ 5 , 10 , 12 , 32 ];
				$fundDataSql->whereIn( 'subcategoria_id' , $gerPromSubCategoryIds );	
			}
			else
			{
				//OTROS USUARIOS ASIGNA UNA CATEGORIA INEXISTENTE
				$fundDataSql->where( 'subcategoria_id' , 0 );
			}
		}
		return $fundDataSql->get();
	}



	protected static function getConsultaMerge($productoId){

        $pId = $productoId; 
        
        $row = \DB::transaction(function($conn) use ($pId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_SUB_FONDO_PRO_MERGE(:pId, :data); END;');
                $stmt->bindParam(':pId', $pId, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row); 
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
		return $this->SubCategoria->descripcion . ' | ' . $this->marca->descripcion;
	}

	protected function getMiddleNameAttribute()
	{
		return $this->marca->descripcion;
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



  
}