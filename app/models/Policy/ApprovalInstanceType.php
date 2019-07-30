<?php

namespace Policy;

use \Eloquent;

class ApprovalInstanceType extends Eloquent 
{
    protected $table = 'TIPO_INSTANCIA_APROBACION';
    protected $primaryKey = 'id';

    public function approvalPolicies()
    {
        return $this->hasMany( '\Policy\ApprovalPolicy' , 'id_tipo_instancia_aprobacion' );
    }

    public function approvalPolicyOrder( $order )
    {
    	return $this->hasOne( '\Policy\ApprovalPolicy' , 'id_tipo_instancia_aprobacion' )->where( 'orden' , $order )->first();
    }

    public function approvalPolicyTypesOrder( $type , $order )
    {
    	return $this->hasOne( '\Policy\ApprovalPolicy' , 'id_tipo_instancia_aprobacion' )->whereIn( 'tipo_usuario' , $type )->where( 'orden' , $order )->first();
    }

    public function approvalPolicyType( $type )
    {
        return $this->hasOne( '\Policy\ApprovalPolicy' , 'id_tipo_instancia_aprobacion' )->where( 'tipo_usuario' , $type )->first();

    }

    public function getAddData()
    {
        return $this->select( [ 'id' , 'descripcion' ] )
            ->orderBy( 'descripcion' , 'ASC' )
            ->get();
    }

}
