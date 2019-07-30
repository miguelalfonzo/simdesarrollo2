<?php

namespace Maintenance;

use \BaseController;
use \Input;
use \View;
use \Excel;
use \DB;
use \Auth;
use \Carbon\Carbon;

//MODELOS
use \Fondo\Fondo;
use \Expense\Proof;
use \Expense\MarkProofAccounts;
use \Expense\Mark;
use \Dmkt\Account;
use \Expense\PlanCta;
use \Exception;
use \Dmkt\InvestmentType;
use \Dmkt\InvestmentActivity;
use \Dmkt\Activity;
use \Client\ClientType;
use \Parameter\Parameter;
use \Fondo\FondoSupervisor;
use \Fondo\FondoGerProd;
use \Fondo\FondoInstitucional;
use \Fondo\FondoSubCategoria;
use \Fondo\FondoCategoria;
use \Fondo\FondoMktType;
use \System\FondoMktHistory;
use \Policy\ApprovalInstanceType;
use \Fondo\FondoMkt;
use \Expense\BagoMarcaGasto;
use \Parameter\SolicitudExclution;
use \Dmkt\SpecialAccount;


class TableController extends BaseController
{

  private function getModel( $type )
  {
    switch( $type ):
      case 'Fondo_Contable':
        return array( 'model' => new Fondo , 'id' => MANTENIMIENTO_FONDO , 'key' => 'nombre' );
      case 'Cuenta_Gasto_Marca':
        return array( 'model' => new MarkProofAccounts , 'id' => 1 );
      case 'Parametro':
        return array( 'model' => new Parameter , 'id' => 2 );
      case 'Fondo_Supervisor':
        return array( 'model' => new FondoSupervisor , 'id' => 3 );
      case 'Fondo_Gerente_Producto':
        return array( 'model' => new FondoGerProd , 'id' => 4 );
      case 'Fondo_Institucion':
        return array( 'model' => new FondoInstitucional , 'id' => 5 );
      case 'Tipo_Inversion':
        return array( 'model' => new InvestmentType , 'id' => 7 , 'key' => 'nombre' );
      case 'Tipo_Actividad':
        return array( 'model' => new Activity , 'id' => 8 , 'key' => 'nombre' );
      case 'Inversion_Actividad':
        return array( 'model' => new InvestmentActivity , 'id' => 9 );
      case 'Fondo_Subcategoria':
        return array( 'model' => new FondoSubCategoria , 'id' => 10 );
      case 'Tipo_Cliente':
        return array( 'model' => new ClientType , 'key' => 'descripcion' );
      case 'Tipo_Instancia_Aprobacion':
        return array( 'model' => new ApprovalInstanceType , 'key' => 'descripcion' );
      case 'Documento':
        return array( 'model' => new Proof , 'key' => 'codigo' );
      case 'Fondo_Categoria':
        return array( 'model' => new FondoCategoria , 'key' => 'descripcion' );
      case 'Fondo_Subcategoria_Tipo':
        return array( 'model' => new FondoMktType , 'key' => 'descripcion' );
      case 'Solicitud_Exclusion':
        return array( 'model' => new SolicitudExclution , 'id' => 11 );
      case 'Cuenta_Especial':
        return array( 'model' => new SpecialAccount , 'id' => 12 );
    endswitch;
  }

  public function export( $type )
  {
    $vData       = $this->getModel( $type );
    $model  	 = $vData[ 'model' ];
    $id          = $vData[ 'id' ];

    $records = $model::orderWithTrashed();

    $maintenance = Maintenance::find( $id );
    $columns = json_decode( $maintenance->formula );
    $data = array(
      'records' => $records ,
      'columns' => $columns ,
      'titulo'  => 'Mantenimiento de ' . $maintenance->descripcion ,
      'type'    => $type ,
      'options' => false
    );
    $now = Carbon::now();
    Excel::create( $maintenance->descripcion . ' ' . $now->format( 'YmdHi' ) , function( $excel ) use( $data )
    {
        $excel->sheet( 'Data' , function( $sheet ) use ( $data )
        {
            $sheet->loadView( 'Maintenance.export' , $data );
          });
    })->store( 'xls' , storage_path( 'maintenance' ) )->export( 'xls' );
  }

  public function getMaintenanceCellData()
  {
    try
    {
      $inputs = Input::all();
      $vData  = $this->getModel( $inputs[ 'type'] );
      $data   = array( 'datos' => $vData[ 'model']::all() , 'val' => $inputs[ 'val' ] , 'key' => $vData[ 'key' ] );
      return $this->setRpta( View::make( 'Maintenance.td' , $data )->render() );
    }
    catch( Exception $e )
    {
      return $this->internalException( $e , __FUNCTION__ );
    }
  }

  public function getView( $type )
  {
    $vData       = $this->getModel( $type );
    $model  	 = $vData[ 'model' ];
    $id          = $vData[ 'id' ];

    $records = $model::orderWithTrashed();

    $maintenance = Maintenance::find( $id );
    $columns     = json_decode( $maintenance->formula );

    $title = 'Mantenimiento de ' . $maintenance->descripcion;
    if( ! in_array( Auth::user()->type , [ GER_COM , CONT , ESTUD ] ) )
    {
      $maintenance->opciones = false;
      $title = $maintenance->descripcion;
    }

    return View::make( 'Maintenance.view' ,
      array(
        'records'  => $records ,
        'columns'  => $columns ,
        'titulo'   => $title ,
        'type'     => $type ,
        'add'	   => $maintenance->agregar_formula ,
        'disabled' => $maintenance->deshabilitar ,
        'export'   => $maintenance->exportar ,
        'options'  => $maintenance->opciones
      )
    );
  }

  private function updateFondoMkt( $inputs )
  {
    DB::beginTransaction();
    $middleRpta = $this->updateGeneric( $inputs );
    $data   = $middleRpta[ data ];
    $middleRpta = $this->validateFondoSaldoNeto( $data[ 'newRecord' ] );
    if ( $middleRpta[ status ] == ok )
    {
      $this->setFondoMktHistory( $data , $inputs[ 'type' ] );
      DB::commit();
    }
    else
    {
      DB::rollback();
    }
    return $middleRpta;
  }

  private function registerAccount( $inputs )
  {
    $bagoAccount = PlanCta::find( $inputs->num_cuenta );
    if( is_null( $bagoAccount ) )
    {
      return $this->warningException( 'La cuenta no esta registrada en el Plan de Cuenta' , __FUNCTION__ , __LINE__ , __FILE__ );
    }
    else
    {
      $account = Account::getAccount( $inputs->num_cuenta );
      if( is_null( $account ) )
      {
        $account               = new Account;
        $account->id           = $account->lastId() + 1 ;
        $account->num_cuenta   = $inputs->num_cuenta;
        $account->idtipocuenta = 1;
        $account->idtipomoneda = SOLES;
        $account->save();
      }
      return $this->setRpta();
    }
  }

  private function saveFondoContable( $inputs )
  {
    DB::beginTransaction();
    $middleRpta = $this->saveMaintenance( $inputs );
    if ( $middleRpta[ status ] == ok )
    {
      $middleRpta = $this->registerAccount( $middleRpta[ data ] );
      if ( $middleRpta[ status ] == ok )
      {
        DB::commit();
      }
      else
      {
        DB::rollback();
      }
    }
    else
    {
      DB::rollback();
    }
    return $middleRpta;

  }

  private function updateFondoContable( $inputs )
  {
    DB::beginTransaction();
    $middleRpta = $this->updateGeneric( $inputs );
    if ( $middleRpta[ status ] === ok )
    {
      $middleRpta = $this->registerAccount( $middleRpta[ data ][ 'newRecord' ] );
      if ( $middleRpta[ status ] === ok )
      {
        DB::commit();
      }
      else
      {
        DB::rollback();
      }
    }
    else
    {
      DB::rollback();
    }
    return $middleRpta;
  }

  public function updateMaintenanceData()
  {
    try
    {
      $inputs = Input::all();
      if( ! in_array( Auth::user()->type , [ GER_COM , CONT , ESTUD ] ) )
      {
        return $this->warningException( 'No esta autorizado para modificar la informacion' , __FUNCTION__ , __LINE__ , __FILE__ );
      }

      switch( $inputs[ 'type' ] ):
        case 'Fondo_Gerente_Producto':
          return $this->updateFondoMkt( $inputs );
        case 'Fondo_Institucion':
          return $this->updateFondoMkt( $inputs );
        case 'Fondo_Supervisor':
          return $this->updateFondoMkt( $inputs );
        case 'Fondo_Contable':
          return $this->updateFondoContable( $inputs );
        case 'Cuenta_Gasto_Marca':
          return $this->updateCuentaGastoMarca( $inputs );
      endswitch;
      $this->updateGeneric( $inputs );
      return $this->setRpta();
    }
    catch ( Exception $e )
    {
      DB::rollback();
      return $this->internalException( $e , __FUNCTION__ );
    }
  }

  private function updateGeneric( $val )
  {
    $model  = $this->getModel( $val[ 'type' ] )[ 'model' ];
    $record = $model::withTrashed()->find( $val['id'] );
    $oldRecord = json_decode( $record->toJson() );
    foreach ( $val[data] as $key => $data )
      $record->$key = $data ;
    $record->save();
    return $this->setRpta( array( 'oldRecord' => $oldRecord , 'newRecord' => $record ) );
  }

  private function validateFondoSaldoNeto( $fondo )
  {
    if ( $fondo->saldo < $fondo->retencion )
      return $this->warningException( 'No puede asignar un saldo menor al saldo reservado por las solicitudes' , __FUNCTION__ , __LINE__ , __FILE__ );
    else
      return $this->setRpta();
  }

  private function setFondoMktHistory( $fondos , $type )
  {
    //REGISTRO DEL MOVIMIENTO DE SALDOS EN EL HISTORIAL DE FONDOS
    $fondoMktHistory                          = new FondoMktHistory;
    $fondoMktHistory->id                      = $fondoMktHistory->nextId();
    $fondoMktHistory->id_to_fondo             = $fondos[ 'newRecord' ]->id ;
    $fondoMktHistory->to_old_saldo            = $fondos[ 'oldRecord' ]->saldo;
    $fondoMktHistory->to_new_saldo            = $fondos[ 'newRecord' ]->saldo;
    $fondoMktHistory->old_retencion           = $fondos[ 'oldRecord' ]->retencion;
    $fondoMktHistory->new_retencion           = $fondos[ 'newRecord' ]->retencion;
    $fondoMktHistory->id_fondo_history_reason = FONDO_AJUSTE;
    $fondoMktHistory->id_tipo_to_fondo        = $this->getFondoType( $type );
    $fondoMktHistory->save();

    //CREACION DE ARRAY CON LOS SALDOS Y RETENCIONES ANTES Y DEPUES DE LA ACTUALIZACION
    $data  =   array(
            'oldSaldo'     => $fondos[ 'oldRecord' ]->saldo ,
            'newSaldo'     => $fondos[ 'newRecord' ]->saldo ,
            'oldRetencion' => $fondos[ 'oldRecord' ]->retencion ,
            'newRetencion' => $fondos[ 'newRecord' ]->retencion );

    //LLAMANDO A LA FUNCION DE ACTUALIZACION DEL HISTORIAL DEL FONDO POR PERIODO
    $fondoMkt = new FondoMkt;
    $fondoMkt->setPeriodHistoryData( $fondos[ 'newRecord' ]->subcategoria_id , $data );
  }

  private function getFondoType( $type )
  {
    if ( $type == 'Fondo_Supervisor' )
      return SUP;
    elseif ( $type == 'Fondo_Gerente_Producto' )
      return GER_PROD;
    elseif( $type == 'Fondo_Institucion' )
      return 'I';
  }

  private function saveMaintenance( $inputs )
  {
    $vData = $this->getModel( $inputs[ 'type' ] );
    $record = $vData[ 'model' ];
    $record->id = $record->nextId();
    foreach( $inputs[ data ] as $column => $data )
      $record->$column = $data;
    $record->save();
    return $this->setRpta( $record );
  }

  public function saveMaintenanceData()
  {
    try
    {
      $inputs = Input::all();
      switch( $inputs[ 'type' ] ):
        case 'Fondo_Contable':
          return $this->saveFondoContable( $inputs );
        case 'Cuenta_Gasto_Marca':
          return $this->saveCuentaGastoMarca( $inputs );
      endswitch;
      return $this->saveMaintenance( $inputs );
    }
    catch( Exception $e )
    {
      DB::rollback();
      return $this->internalException( $e , __FUNCTION__ );
    }
  }

  public function addMaintenanceData()
  {
    $inputs = Input::all();
    return $this->addRow( $inputs );
  }

  private function addRow( $inputs )
  {
    $id = $this->getModel( $inputs[ 'type' ] )[ 'id' ];
    $addFormulaJson = Maintenance::find( $id )->agregar_formula;
    $addFormula = json_decode( $addFormulaJson );
    foreach( $addFormula as $row )
    {
      if( isset( $row->model ) )
      {
        $vData = $this->getModel( $row->model );
        $data = $vData[ 'model' ]->getAddData();
        $row->data = $data;
      }
    }
    $data =
    [
      'records' => $addFormula,
      'type'    => $inputs[ 'type' ]
    ];
    return $this->setRpta( View::make( 'Maintenance.tr' , $data )->render() );
  }

  private function addActividad()
  {
    $data = array( 'tipo_cliente' => ClientType::all() );
    return $this->setRpta( View::make( 'Maintenance.Activity.tr')->with( $data )->render() );
  }

  private function getDailySeatRelation()
  {
    $records = MarkProofAccounts::all();
    $columns = Maintenance::find( 1 );
    $columns = json_decode( $columns->formula );
    return $this->setRpta( View::make( 'Maintenance.table' )->with( array( 'records' => $records , 'columns' => $columns , 'type' => 'cuentasMarca' ) )->render() );
  }

  private function addcuentasMarca()
  {
    $data = array( 'Tipos_Documento' => Proof::all() );
    return $this->setRpta( View::make( 'Maintenance.Cuentasmarca.tr' , $data )->render() );
  }

  private function addInversion()
  {
    $data = array( 'Fondos_Contable' => Fondo::all() , 'Tipo_Instancias_Aprobacion' => ApprovalInstanceType::all() );
    return $this->setRpta( View::make( 'Maintenance.Investment.tr' , $data )->render() );
  }

  private function addInversionActividad()
  {
    $data = array( 'actividades' => Activity::withTrashed()->orderBy( 'nombre' )->get() , 'inversiones' => InvestmentType::withTrashed()->orderBy( 'nombre' )->get() );
    return $this->setRpta( View::make( 'Maintenance.InvestmentActivity.tr')->with( $data )->render() );
  }

  public function enableRecord()
  {
    try
    {
      $inputs = Input::all(); //Input::all trae todos los inputs enviados en el post
      $vData  = $this->getModel( $inputs[ 'type' ] ); //llama a la funcion get model de la clase
      $rowBDTable = $vData[ 'model' ]::withTrashed()->find( $inputs[ 'id' ] ); // obtiene todo el
      $rowBDTable->restore();// convierte el campo delete_at en null
      $rowBDTable->deleted_by = null; // convierte el campo delete_by a null
      $rowBDTable->save(); // guardar cambios...  el restore lo tiene incluido
      return $this->setRpta();
    }
    catch( Exception $e )
    {
      return $this->internalException();
    }
  }

  public function disableRecord()
  {
    try
    {
      $inputs = Input::all();
      $vData  = $this->getModel( $inputs[ 'type' ] );
      $vData[ 'model' ]::find( $inputs[ 'id' ] )->delete();
      return $this->setRpta();
    }
    catch( Exception $e )
    {
      return $this->internalException();
    }
  }

  private function processMark( $markNumber )
  {
    $markRow = Mark::where( 'codigo' , $markNumber )->first();
    if ( is_null( $markRow ) )
    {
      $bagoMark = BagoMarcaGasto::getRegister( $markNumber );
      if( is_null( $bagoMark ) )
      {
        return $this->warningException( 'La marca no esta registrada en el sistema contable' , __FUNCTION__ , __LINE__ , __FILE__ );
      }
      else
      {
        $markModel = Mark::getMark( $markNumber );
        if( is_null( $markModel ) )
        {
          $tipoMarca;
          if ( substr( $markNumber , 0 , 1 )    == 4 )
          {
            $tipoMarca = 1;
          }
          elseif ( substr( $markNumber , 0 , 1 ) == 6 )
          {
            $tipoMarca = 2;
          }
          $mark                = new Mark;
          $mark->id            = $mark->lastId() + 1;
          $mark->codigo        = $markNumber;
          $mark->id_tipo_marca = $tipoMarca;
          $mark->save();
        }
        return $this->setRpta();
      }
    }
    else
    {
      return $this->setRpta();
    }
  }


  private function processAccount( $accountNumber )
  {
    $account = Account::getFirstExpenseAccount( $accountNumber );
    if ( is_null( $account ) )
    {
        $account = new Account;
        $account->id = $account->lastId() + 1 ;
        $account->num_cuenta = $accountNumber;
        $account->idtipocuenta = 4;
        $account->idtipomoneda = SOLES;
        $account->save();
        return $this->setRpta();
    }
    else
    {
      return $this->setRpta();
    }
  }

  private function updateCuentaGastoMarca( $inputs )
  {
    DB::beginTransaction();
    $middleRpta = $this->validateAdvanceAccount( $inputs[ data ][ 'num_cuenta_fondo' ] );
    if ( $middleRpta[status] === ok )
    {
      $middleRpta = $this->processAccount( $inputs[ data ][ 'num_cuenta_gasto' ] );
      if ( $middleRpta[status] === ok )
      {
        $middleRpta = $this->processMark( $inputs[ data ][ 'marca_codigo' ] );
        if ( $middleRpta[status] === ok )
        {
          $this->updateGeneric( $inputs );
          DB::commit();
          return $middleRpta;
        }
      }
    }
    DB::rollback();
    return $middleRpta;
  }

  private function saveCuentaGastoMarca( $inputs )
  {
    DB::beginTransaction();
    $middleRpta = $this->validateAdvanceAccount( $inputs[ data ][ 'num_cuenta_fondo' ] );
    if ( $middleRpta[status] === ok )
    {
      $middleRpta = $this->processAccount( $inputs[ data ][ 'num_cuenta_gasto' ] );
      if ( $middleRpta[status] === ok )
      {
        $middleRpta = $this->processMark( $inputs[ data ][ 'marca_codigo' ] );
        if ( $middleRpta[status] === ok )
        {
          $this->saveMaintenance( $inputs );
          DB::commit();
          return $middleRpta;
        }
      }
    }
    DB::rollback();
    return $middleRpta;
  }

  private function validateAdvanceAccount( $accountNumber )
  {
    $fund = Fondo::getContableFund( $accountNumber );
    if( is_null( $fund ) )
    {
      return $this->warningException( 'La cuenta de anticipo no esta registrada en el sistema' , __FUNCTION__ , __LINE__ , __FILE__ );
    }
    else
    {
      return $this->setRpta();
    }
  }

}