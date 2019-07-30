<?php

namespace Expense;

use \Eloquent;
use \stdClass;
use \Carbon\Carbon;

class Entry extends Eloquent
{
    protected $table= 'ASIENTO';
    protected $primaryKey = 'id';
    protected $dates = ['fec_origen'] ;
    
    public function nextId()
    {
        $lastId = Entry::orderBy( 'id' , 'DESC' )->first();
        if( is_null( $lastId ) )
        {
            return 1;
        }
        else
        {
            return $lastId->id + 1;
        }
    }

    protected function account()
    {
        return $this->hasOne( 'Dmkt\Account' , 'num_cuenta' , 'num_cuenta');
    }

    protected function bagoAccount()
    {
        return $this->hasOne( 'Expense\PlanCta' , 'ctactaextern' , 'num_cuenta' );
    }

    public function insertAdvanceEntry( stdClass $entry , $solicitud_id )
    {
        $this->id           = $this->nextId();
        $this->num_cuenta   = $entry->account_number;
        $this->fec_origen   = Carbon::createFromFormat( 'd/m/Y' , $entry->origin );
        $this->d_c          = $entry->d_c;
        $this->importe      = $entry->import;
        $this->leyenda      = $entry->caption;
        $this->id_solicitud = $solicitud_id;
        $this->tipo_asiento = TIPO_ASIENTO_ANTICIPO;
        $this->save();
    }

    public function insertRegularizationEntry( $entry , $solicitud_id )
    {
        $this->id           = $this->nextId();
        $this->num_cuenta   = $entry->numero_cuenta;
        $this->cc           = $entry->codigo_sunat;
        $this->fec_origen   = Carbon::createFromFormat( 'd/m/Y' , $entry->fec_origen );
        $this->iva          = $entry->iva;
        $this->cod_pro      = $entry->cod_prov;
        $this->nom_prov     = $entry->nombre_proveedor;
        $this->cod          = $entry->cod;
        $this->ruc          = $entry->ruc;
        $this->prefijo      = $entry->prefijo;
        $this->cbte_prov    = $entry->cbte_proveedor;
        $this->d_c          = $entry->dc;
        $this->importe      = $entry->importe;
        $this->leyenda_fj   = $entry->leyenda;
        $this->leyenda      = $entry->leyenda_variable;
        $this->tipo_resp    = $entry->tipo_responsable;
        $this->nro_origen   = $entry->nro_origen;
        $this->id_solicitud = $solicitud_id;
        $this->tipo_asiento = TIPO_ASIENTO_GASTO;
        $this->save();
    }
}