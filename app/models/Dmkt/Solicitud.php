<?php

namespace Dmkt;
use \Eloquent;
use \Auth;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Solicitud extends Eloquent
{
    protected $table = TB_SOLICITUD;
    protected $primaryKey = 'id';


    protected static function verificar_politica($type1,$type2,$cantHistory,$solicitudId){

        $row = \DB::transaction(function($conn) use ($type1,$type2,$cantHistory,$solicitudId){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_POLITICA_APROBACION(:type1,:type2,:cantHistory,:solicitudId,:data); END;');
                $stmt->bindParam(':type1', $type1, \PDO::PARAM_STR);
                $stmt->bindParam(':type2', $type2, \PDO::PARAM_STR);
                $stmt->bindParam(':cantHistory', $cantHistory, \PDO::PARAM_STR);
                $stmt->bindParam(':solicitudId', $solicitudId, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);

    }

    protected static function cancelarSolicitud($id,$NewEstado,$solicitudObs,$solicitudEstatus){

        $idUser = Auth::user()->id;

        $row = \DB::transaction(function($conn) use ($id,$NewEstado,$solicitudObs,$solicitudEstatus,$idUser){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_CANCELAR_SOLICITUD(:id,:NewEstado,:solicitudObs,:solicitudEstatus,:idUser); END;');
                $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                $stmt->bindParam(':NewEstado', $NewEstado, \PDO::PARAM_STR);
                $stmt->bindParam(':solicitudObs', $solicitudObs, \PDO::PARAM_STR);
                $stmt->bindParam(':solicitudEstatus', $solicitudEstatus, \PDO::PARAM_STR);
                $stmt->bindParam(':idUser', $idUser, \PDO::PARAM_STR);
                $stmt->execute();       
                // oci_execute($lista, OCI_DEFAULT);
                // oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                // oci_free_cursor($lista);
                // return $array;

        });

        #return new Collection($row);

    }

    protected static function buscarSolicitud($token){
    
        $row = \DB::transaction(function($conn) use ($token){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_BUSCAR_SOLICITUD(:tokenIn, :data); END;');
                $stmt->bindParam(':tokenIn', $token, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);

        // $row = new Collection($row);
        // /**
        //  *  Agrego a la Collection el detalle de la Solicitud. 
        //  */
        // foreach($row as $item){
        //     $detalle = Solicitud::buscarSolicitudDetalle($item['ID_DETALLE']);
        //     $row->push($detalle);
        // }

        // return $row;
    }

    public static function buscarSolicitudDetalle($id_detalle){

        $row = \DB::transaction(function($conn) use ($id_detalle){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_BUSCAR_SOLICITUD_DETALLE(:idSolDet, :data); END;');
                $stmt->bindParam(':idSolDet', $id_detalle, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }

    public static function get_tipo_user($idSol){

        $idSolicitud = $idSol;

        $row = \DB::transaction(function($conn) use ($idSolicitud){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_ADD_FAMILY_FUND_SOL(:idSolicitud, :data); END;');
                $stmt->bindParam(':idSolicitud', $idSolicitud, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }


    protected static function salvar($solicitudId,$idEstado,$status,$anotacion,$userId,$userAssign){

        $row = \DB::transaction(function($conn) use ($solicitudId,$idEstado,$status,$anotacion,$userId,$userAssign){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_APROBAR_SOLICITUD(:solicitudId,:idEstado,:status,:anotacion,:userId, :userAssign); END;');
                $stmt->bindParam(':solicitudId', $solicitudId, \PDO::PARAM_STR);
                $stmt->bindParam(':idEstado', $idEstado, \PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
                $stmt->bindParam(':anotacion', $anotacion, \PDO::PARAM_STR);
                $stmt->bindParam(':userId', $userId, \PDO::PARAM_STR);
                $stmt->bindParam(':userAssign', $userAssign, \PDO::PARAM_STR);
                $stmt->execute();       
                #oci_execute($lista, OCI_DEFAULT);
                #oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                #oci_free_cursor($lista);
                #return $array;

        });

        #return new Collection($row);
    }

    protected function getCreatedAtDateAttribute()
    {
        return $this->created_at->format( 'd/m/Y' );
    }

    protected function getCreatedAtParseAttribute( $attr )
    {
        return Carbon::parse( $attr )->format('Y-m-d H:i');
    }

    protected function getSolicitudCaptionAttribute()
    {
        return substr( ltrim( substr( $this->id , -6 ) , 0 ) . ' ' . $this->assignedTo->personal->seat_name . ' ' . strtoupper( $this->investment->accountFund->nombre ) , 0 , 50 );    
    }

    protected function getDepositCreditCaptionAttribute()
    {
        return substr( $this->detalle->deposit->num_transferencia . ' ' . $this->assignedTo->personal->seat_name , 0 , 50 );
    }

    public function nextId()
    {
        $now = Carbon::now();
        $lastId = Solicitud::orderBy( 'id' , 'DESC' )->first();
        if( is_null( $lastId ) )
        {
            return $now->format( 'Y' ) . '000001';
        }
        else
        {
            if( $now->format( 'Y' ) == substr( $lastId->id , 0 , 4 ) )
            {
                return $lastId->id + 1;
            }
            else
            {
                return $now->format( 'Y' ) . '000001';        
            }
        }
    }

    public function insert( $solicitudId , $detalleId , $title , $activity , $inversion , $description , $solicitudType , $responsible )
    {
        $this->id              = $solicitudId;
        $this->id_detalle      = $detalleId;
        $this->token           = sha1( md5( uniqid( $this->id, true ) ) );
        $this->titulo          = $title;
        $this->id_actividad    = $activity;
        $this->id_inversion    = $inversion;
        $this->descripcion     = $description;
        $this->id_estado       = PENDIENTE;
        $this->idtiposolicitud = $solicitudType;
        $this->status          = ACTIVE;
        $this->id_user_assign  = $responsible;
        $this->save();
    
    }
            
    protected static function solInst( $periodo )
    {
        return Solicitud::orderBy('id','desc')->whereHas('detalle' , function ( $q ) use ( $periodo )
        {
            $q->whereHas( TB_PERIODO , function ( $t ) use ( $periodo )
            {
                $t->where( 'aniomes' , $periodo );
            });
        })->whereNotIn( 'id_estado' , array( CANCELADO , RECHAZADO ) )->get();
    }

    public function state()
    {
        return $this->hasOne( 'Common\State' , 'id' , 'id_estado' );
    }

    public function assignedTo()
    {
        return $this->belongsTo( 'User' , 'id_user_assign' );
    }

    public function personalTo()
    {
        return $this->hasOne( 'Users\Personal' , 'user_id' , 'id_user_assign' );
    }

    public function toAcceptedApprovedHistories()
    {
        return $this->hasMany( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->whereIn( 'status_to' , array( ACEPTADO , APROBADO ) );
    }

    protected function expenseHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , GASTO_HABILITADO );
    }

    protected function registerHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , REGISTRADO );
    }

    protected function toDeliveredHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' )->where( 'status_to' , ENTREGADO );
    }

    protected function toPendingHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , PENDIENTE );
    }

    protected function toDevolutionHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , DEVOLUCION );
    }

    public function approvedHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , APROBADO );
    }

    public function toDepositHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' )->where( 'status_to' , DEPOSITO_HABILITADO );
    }

    public function toAdvanceSeatHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , DEPOSITADO );
    }

    public function toGenerateHistory()
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'status_to' , GENERADO );
    }

    public function orderHistories()
    {
        return $this->hasMany( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->orderBy( 'created_at' , 'ASC' );
    }        

    public function acceptHist()
    {
        return $this->hasOne('System\SolicitudHistory','id_solicitud','id')->where( 'status_to' , ACEPTADO )->orderBy( 'updated_at' , 'DESC' );
    }

    public function fromUserHistory( $userFrom )
    {
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' , 'id' )->where( 'user_from' , $userFrom )->first();
    }

    public function histories(){
        return $this->hasMany('System\SolicitudHistory','id_solicitud');
    }

    public function lastHistory(){
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' )->orderBy( 'updated_at' , 'desc' );
    }

    public function lastApprovedHistory(){
        return $this->hasOne( 'System\SolicitudHistory' , 'id_solicitud' )->whereIn( 'status_to' , [ ACEPTADO , APROBADO ] )->orderBy( 'updated_at' , 'desc' );
    }

    public function detalle()
    {
        return $this->belongsTo( 'Dmkt\SolicitudDetalle' , 'id_detalle' );
    }

    protected function typeSolicitude()
    {
        return $this->hasOne( 'Dmkt\SolicitudType' , 'id' , 'idtiposolicitud' );
    }

    public function products()
    {
        return $this->hasMany( 'Dmkt\SolicitudProduct' , 'id_solicitud' , 'id' );
    }

    public function clients(){
        return $this->hasMany( 'Dmkt\SolicitudClient' , 'id_solicitud' , 'id' )->orderBy( 'id' , 'ASC' );
    }

    public function client()
    {
        return $this->hasOne( 'Dmkt\SolicitudClient' , 'id_solicitud' )->orderBy( 'id' , 'ASC' );
    }

    /*protected function clientEntry()
    {
        return $this->hasOne( 'Dmkt\SolicitudClient' , 'id_solicitud' )->orderByRaw( 'case when id_tipo_cliente when 3 then 0 else 1 end , id' )
    }*/

    public function createdBy()
    {
        return $this->belongsTo( 'User' , 'created_by' );
    }

    public function createdPersonal()
    {
        return $this->hasOne( 'Users\Personal' , 'user_id' , 'created_by' );
    }

    public function updatedBy()
    {
        return $this->belongsTo( 'User' , 'updated_by' );
    }

    public function gerente()
    {
        return $this->hasMany( 'Dmkt\SolicitudGer' , 'id_solicitud' );
    }

    public function managerEdit( $userType )
    {
        return $this->hasMany( 'Dmkt\SolicitudGer' , 'id_solicitud'  , 'id' )->where( 'permiso' , 1 )->where( 'tipo_usuario' , $userType );
    }

    public function activity()
    {
        return $this->hasOne('Dmkt\Activity','id','id_actividad');
    }

    public function activityTrash()
    {
        return $this->hasOne( 'Dmkt\Activity' , 'id' , 'id_actividad' )->withTrashed();
    }

    public function expenses()
    {
        return $this->hasMany( 'Expense\Expense' , 'id_solicitud' , 'id' )->orderBy( 'updated_at' , 'desc');
    }

    protected function lastExpense()
    {
        return $this->hasOne( 'Expense\Expense' , 'id_solicitud' , 'id' )->orderBy( 'updated_at' , 'desc' );
    }

    protected function advanceCreditEntry()
    {
        return $this->hasOne( 'Expense\Entry' , 'id_solicitud' )->where( 'd_c' , ASIENTO_GASTO_BASE )->where( 'tipo_asiento' , TIPO_ASIENTO_ANTICIPO );        
    }

    protected function advanceDepositEntry()
    {
        return $this->hasOne( 'Expense\Entry' , 'id_solicitud' )->where( 'd_c' , ASIENTO_GASTO_DEPOSITO )->where( 'tipo_asiento' , TIPO_ASIENTO_ANTICIPO );        
    }

    protected function dailyEntries()
    {
        return $this->hasMany( 'Expense\Entry' , 'id_solicitud' )->where( 'tipo_asiento' , TIPO_ASIENTO_GASTO )->orderBy( 'id' , 'ASC' );
    }
    
    protected function investment()
    {
        return $this->hasOne( 'Dmkt\InvestmentType' , 'id' , 'id_inversion' );
    }

    public function orderProducts()
    {
        return $this->hasMany( 'Dmkt\SolicitudProduct' , 'id_solicitud' )->orderBy( 'updated_at' , 'DESC' );
    }

    public function devolutions()
    {
        return $this->hasMany( 'Devolution\Devolution' , 'id_solicitud' );
    }

    public function pendingRefund()
    {
        return $this->hasMany( 'Devolution\Devolution' , 'id_solicitud' )
            ->whereIn( 'id_estado_devolucion' , [ DEVOLUCION_POR_REALIZAR , DEVOLUCION_POR_VALIDAR ] );
    }

    public function pendingPayrollRefund()
    {
        return $this->hasMany( 'Devolution\Devolution' , 'id_solicitud' )
            ->where( 'id_tipo_devolucion' , DEVOLUCION_PLANILLA );
    }

    protected function getDepositSolicituds( $year )
    {
        return Solicitud::select( [ 'id' , 'token' , 'id_detalle' , 'id_user_assign' , 'titulo' , 'id_inversion' ] )
            ->where( 'id_estado' , DEPOSITO_HABILITADO )
            ->where( 'extract( year from created_at )' , $year )
            ->with( [ 'detalle' , 'clients' , 'personalTo' ] )
            ->orderBy( 'id' , 'ASC' )->get();
    }

    protected function get_deposito_solicitud_sp( $year )
    {
        
        $row = \DB::transaction(function($conn) use ($year){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_DEPOSITO_SOLICITUD(:year, :data); END;');
                $stmt->bindParam(':year', $year, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }


    // protected function getRevisionSolicituds()
    // {
    //     return Solicitud::select( [ 'id' , 'token' ] )
    //         ->where( 'id_estado' , APROBADO )
    //         ->orderBy( 'id' , 'ASC' )->get();
    // }

    // protected function getDepositSeatSolicituds()
    // {
    //     return Solicitud::select( [ 'id' , 'token' ] )
    //         ->where( 'id_estado' , DEPOSITADO )
    //         ->orderBy( 'id' , 'ASC' )->get();
    // }

    // protected function getRegularizationSeatSolicituds()
    // {
    //     return Solicitud::select( [ 'id' , 'token' ] )
    //         ->where( 'id_estado' , REGISTRADO )
    //         ->orderBy( 'id' , 'ASC' )->get();
    // }

    protected function get_estatus_solicitud_sp( $estatus )
    {
        
        $row = \DB::transaction(function($conn) use ($estatus){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_GET_ESTADO_SOLICITUD(:estatus, :data); END;');
                $stmt->bindParam(':estatus', $estatus, \PDO::PARAM_STR);
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }





    protected static function findByToken( $token )
    {
        return Solicitud::where( 'token' , $token )->first();
    }

    protected static function findByTokens( $tokens )
    {
        return Solicitud::whereIn( 'token' , $tokens )->get();
    }

}