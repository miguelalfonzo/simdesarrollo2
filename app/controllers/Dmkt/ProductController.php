<?php

namespace Dmkt;

use \BaseController;
use \Input;
use \Exception;
use \Auth;
class ProductController extends BaseController
{

    public function deleteSolicitudProduct()
    {
    	$inputs = Input::all();
    	$this->removeSolicitudProduct( $inputs[ 'id_solicitud_producto' ] );
    }

    public function removeSolicitudProduct( $idSolicitudProducto )
    {
		SolicitudProduct::find( $idSolicitudProducto )->delete();    	
    }

    public function addSolicitudProduct()
    {
    	$inpurs = Input::all();
    	$this->newSolicitudProduct( $inputs );
    }

    public function newSolicitudProduct( $inputs )
    {   $userId= Auth::user()->id;

		$solProduct               = new SolicitudProduct;
		#$solProduct->id           = $solProduct->lastId() + 1;
        $solProductId           = $solProduct->lastId() + 1;
		#$solProduct->id_solicitud = $inputs[ 'id_solicitud' ];
        $solProductIdSol = $inputs[ 'id_solicitud' ];
		#$solProduct->id_producto  = $inputs[ 'id_producto' ];
        $solProductIdPro  = $inputs[ 'id_producto' ];
		#$solProduct->save();
        
        SolicitudProduct::salvar($solProductId,$solProductIdSol,$solProductIdPro,$userId);
        #return $solProduct->id;
        return $solProductId;
    }

    public function unsetSolicitudProducts( $solicitudId , $productsId )
    {
        $solicitud = Solicitud::find( $solicitudId );
        $solicitudProducts = $solicitud->products;
        $aux = 0;

        foreach ( $solicitudProducts as $solicitudProduct )
        {
            if ( ! is_null( $solicitudProduct->id_fondo_marketing ) && ! is_null( $solicitudProduct->id_tipo_fondo_marketing ) )
            {
                $aux = 1;
                break;
            }
        }
        if ( $aux === 1 )
        {
            $solicitudController = new SolicitudeController;
            $middleRpta = $solicitudController->renovateBalance( $solicitud );
            if( $middleRpta[ status ] !== ok )
            {
                return $middleRpta;
            }
        }

        SolicitudProduct::whereIn( 'id' , $solicitudProducts->lists( 'id' ) )->delete();
        
        $solicitudProductsId = array();
        foreach( $productsId as $productId )
        {
            $data = array(
                'id_solicitud' => $solicitudId ,
                'id_producto'  => $productId );
            $solicitudProductsId[] = $this->newSolicitudProduct( $data );
            
        }
        return $this->setRpta( $solicitudProductsId );
    } 

}