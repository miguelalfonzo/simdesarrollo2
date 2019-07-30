<?php

namespace System;

use \Eloquent;
use \DB;

class FondoMktHistory extends Eloquent
{
	
    protected $table = TB_FONDO_MARKETING_HISTORIAL;
    protected $primaryKey = 'id';
    
    public function nextId()
    {
        $lastId = FondoMktHistory::orderBy( 'id' , 'desc' )->first();
        if( is_null( $lastId ) )
            return 1;
        else
            return $lastId->id + 1;
    }

    protected static function order()
    {
        return FondoMktHistory::orderBy( 'updated_at' , 'DESC' , 'id' , 'DESC' )->get();
    }

    protected function solicitud()
    {
        return $this->belongsTo( '\Dmkt\Solicitud' , 'id_solicitud' );
    }

    protected function fromFund()
    {
        if ( $this->id_tipo_to_fondo == 'I' )
            return $this->belongsTo( '\Fondo\FondoInstitucional' , 'id_from_fondo' );
        elseif ( $this->id_tipo_to_fondo == GER_PROD )
            return $this->belongsTo( '\Fondo\FondoGerProd' , 'id_from_fondo' );
        elseif ( $this->id_tipo_to_fondo == SUP )
            return $this->belongsTo( '\Fondo\FondoSupervisor' , 'id_from_fondo' );
    }

    public function toFund()
    {
        if ( $this->id_tipo_to_fondo == 'I' )
            return $this->belongsTo( '\Fondo\FondoInstitucional' , 'id_to_fondo' );
        elseif ( $this->id_tipo_to_fondo == GER_PROD )
            return $this->belongsTo( '\Fondo\FondoGerProd' , 'id_to_fondo' );
        elseif ( $this->id_tipo_to_fondo == SUP )
            return $this->belongsTo( '\Fondo\FondoSupervisor' , 'id_to_fondo' );
    }

    public function fromSupFund()
    {
        return $this->belongsTo( '\Fondo\FondoSupervisor' , 'id_to_fondo' );    
    }

    public function fromGerProdFund()
    {
        return $this->belongsTo( '\Fondo\FondoGerProd' , 'id_to_fondo' );    
    }

    public function fromInstitutionFund()
    {
        return $this->belongsTo( '\Fondo\FondoInstitucional' , 'id_to_fondo' );    
    }

    protected function createdByPersonal()
    {
        return $this->hasOne( '\Users\Personal' , 'user_id' , 'created_by' );
    }  

    protected function updatedBy()
    {
        return $this->belongsTo( 'User' , 'updated_by' );
    }

    protected function fondoMktHistoryReason()
    {
        return $this->belongsTo( '\Fondo\FondoMktHistoryReason' , 'id_fondo_history_reason' );
    }

    protected function getFundFirstRegister( $fundId , $type )
    {
        return FondoMktHistory::where( 'id_to_fondo' , $fundId )
            ->where( 'id_tipo_to_fondo' , $type )
            ->orderBy( 'created_at' , 'ASC' )
            ->orderBy( 'id' , 'ASC' )
            ->first();    
    }

    public function getSubCategoryData( $subCategoryId , $subCategoryType , $toFund , $start , $end )
    {
        return $this->select( [ 'sum( to_old_saldo - to_new_saldo ) diff_saldo' , 'sum( old_retencion - new_retencion ) diff_retencion' ] )
            ->where( 'id_tipo_to_fondo' , trim( $subCategoryType ) )
            ->whereRaw( "created_at between to_date( '$start' , 'YYYYMMDD' ) and to_date( '$end 235959' , 'YYYYMMDD HH24MISS' )" )
            ->whereHas( $toFund , function( $query ) use ( $subCategoryId )
            {
                $query->where( 'subcategoria_id' , $subCategoryId );
            })->first();
    }

    public function getSubCategoryBalanceData( $subCategoryId , $subCategoryType , $toFund , $start , $end )
    {
        return $this->select( 
                [ 
                    'id' ,
                    'id_fondo_history_reason' ,
                    'id_solicitud' ,
                    'id_tipo_to_fondo' ,
                    'id_to_fondo' ,
                    'to_old_saldo' ,
                    'to_new_saldo' ,
                    'created_at' ,
                    'created_by' ,
                    "to_char( created_at , 'YYYYMM' ) periodo"
                ] 
            )->where( 'id_tipo_to_fondo' , trim( $subCategoryType ) )
            ->whereIn( 'id_fondo_history_reason' , [ FONDO_AJUSTE , FONDO_DEPOSITO , FONDO_DEVOLUCION_PLANILLA , FONDO_DEVOLUCION_TESORERIA , 8 ] )
            ->whereRaw( "created_at between to_date( '$start' , 'YYYYMMDD' ) and to_date( '$end 235959' , 'YYYYMMDD HH24MISS' )" )
            ->whereHas( $toFund , function( $query ) use ( $subCategoryId )
            {
                $query->where( 'subcategoria_id' , $subCategoryId );
                    
            })->get();
    }
}
