<?php

const IMAGE_PATH            = 'img/reembolso/';
const WIDTH                 = 800;
const HEIGHT                = 600;
const data                  = 'Data';
const ok                    = 'Ok';
const warning               = 'Warning';
const error                 = 'Error';
const danger                = 'Danger';
const desc_error            = 'Hubo un error al procesar la informacion';
const status                = 'Status';
const description           = 'Description';
const DB                    = 'Base de Datos';
const CUENTA_BAGO           = 'B';

const SOLES   = 1;
const DOLARES = 2;

// ASIENTOS
const ASIENTO_GASTO_BASE                      = 'D';
const ASIENTO_GASTO_DEPOSITO                  = 'C';
const TIPO_ASIENTO_ANTICIPO                   = 'A';
const TIPO_ASIENTO_GASTO                      = 'G';
const ERROR_NOT_FOUND_MATCH_ACCOUNT_MKT_CNT   = 'No se encontro cuenta relacionada';
const ASIENTO_GASTO_IVA_BASE                  = 'N6';
const ASIENTO_GASTO_IVA_IGV                   = 'I6';
const ASIENTO_GASTO_COD_PROV_IGV              = '90000';
const ASIENTO_GASTO_COD_PROV                  = '';
const ASIENTO_GASTO_COD_IGV                   = '80';
const ASIENTO_GASTO_COD                       = '';
const MESSAGE_NOT_FOUND_MATCH_ACCOUNT_MKT_CNT = 'Verificar relación entre las cuentas de Marketing y Contabilidad';
const ERROR_NOT_FOUND_MARCA                   = 'No se encontro Marca';
const MESSAGE_NOT_FOUND_MARCA                 = 'Verificar relación entre las Cuentas de Contabilidad y las Marcas';
const CUENTA_REPARO_COMPRAS                   = 6411000;//DEBE
const CUENTA_REPARO_GOBIERNO                  = 4011100;//HABER o DEBE EN CASO DEL IGV
const CUENTA_RETENCION_DEBE                   = 1681000;
const CUENTA_RETENCION_HABER                  = 4011400;
const CUENTA_DETRACCION_HABER                 = 4212300;
const CUENTA_RENTA_4TA_HABER                  = 4017200;
const CUENTA_HABER_REEMBOLSO                  = 4699700;
const CUENTA_RECIBO_HONORARIO                 = 4241000;
const ERROR_INVALID_ACCOUNT_MKT               = 'Error de Cuenta';
const MSG_INVALID_ACCOUNT_MKT                 = 'Verificar campo de cuenta';

//ESTADOS ADICIONALES
const ESTADO_DERIVADO                         = 'DERIVADO';
const ESTADO_RETENCION                        = 'RETENCION';
const ESTADO_DEPOSITADO                       = 'DEPOSITADO';
const ESTADO_ACEPTADO                         = 'POR APROBAR';

//ID USUARIOS
const USER_CONTABILIDAD                       = 43;
const USER_TESORERIA                          = 42;

// EMAIL DE PRUEBAS
const POSTMAN_USER_NAME_1                     = 'Jhonattan Ortiz';
const SOPORTE_EMAIL_1                         = 'jortiz@esinergy.com';
const POSTMAN_USER_NAME_2                     = 'Soporte';
const SOPORTE_EMAIL_2                         = 'pelimsoporte@bagoperu.com.pe';
const POSTMAN_USER_NAME_3                     = 'Elias Ruiz';
const SOPORTE_EMAIL_3                         = 'elruiz@bagoperu.com.pe';


//ESTADO RANGOS
const R_PENDIENTE                             = 1;
const R_APROBADO                              = 2;
const R_REVISADO                              = 3;
const R_GASTO                                 = 4;
const R_FINALIZADO                            = 5;
const R_NO_AUTORIZADO                         = 6;
const R_TODOS                                 = 10;

//CODIGO USUARIOS
const REP_MED                                 = 'R';
const SUP                                     = 'S';
const GER_PROD                                = 'P';
const GER_PROM                                = 'GP';
const GER_COM                                 = 'G';
const GER_GER                                 = 'GG';
const CONT                                    = 'C';
const TESORERIA                               = 'T';
const ASIS_GER                                = 'AG';
const ESTUD                                   = 'E';

//ID DE TIPO DE SOLICITUDES
const SOL_REP                                 = 1;
const SOL_INST                                = 2;
const REEMBOLSO                               = 3;

//ESTADOS
const ACTIVE                                  = 1;
const BLOCKED                                 = 2;
const INACTIVE                                = 3;

//TIPO DE PAGOS
const PAGO_CHEQUE                             = 2;
const PAGO_DEPOSITO                           = 3;

//CUENTAS
const FONDO                                   = 1;
const BANCO                                   = 2;
const RETENCION                               = 3;
const GASTO                                   = 4;

/*
|--------------------------------------------------------------------------
| TESTING
|--------------------------------------------------------------------------
*/

//CODIGO DEL SISTEMA
const SISTEMA_SIM      = 2;

//TIPO DE CLIENTE
const CLT_MED  = 1;
const CLT_FAR  = 2;
const CLT_INST = 3;
const CLT_BOD  = 4;
const CLT_DIST = 5;

/*
|--------------------------------------------------------------------------
| REPORT
|--------------------------------------------------------------------------
*/

const REPORT_TIME_LIMIT                         = 0; // idkc : 0 = ilimitado | 5*60 = 5 minutos
const REPORT_EXPORT_DIRECTORY                   = '/files/';

const REPORT_MESSAGE_DATASET_NOT_FOUND          = 'Lo sentimos. No se encontro DataSet configurados para generar reportes.';
const REPORT_MESSAGE_DATASET_NOT_FOUND_DATA     = 'Lo Sentimos. No se encuentra informacion del DataSet.';
const REPORT_MESSAGE_EXCEPTION                  = 'Oops! Hubo un inconveniente al procesar la información.';
const REPORT_MESSAGE_CREATE                     = 'Oops! No se encontro información de reporte para registrar.';
const REPORT_MESSAGE_EXPORT_GENERATE            = 'Oops! Hubo un inconveniente al procesar la informacion. Por Favor genera el reporte nuevamente';
const REPORT_MESSAGE_USER_REPORT_DATA_NOT_FOUND = 'No tiene reportes asignados';
const REPORT_DATA_NOT_FOUND                     = 'Lo sentimos, no se encontro información disponible';

/*
|--------------------------------------------------------------------------
| TABLA PARAMETRO -> ID
|--------------------------------------------------------------------------
*/

const ALERTA_TIEMPO_ESPERA_POR_DOCUMENTO = 1;
const ALERTA_TIEMPO_REGISTRO_GASTO       = 2;
const ALERTA_INSTITUCION_CLIENTE         = 3;

const FONDO_SUBCATEGORIA_GERPROD         = 'P';
const FONDO_SUBCATEGORIA_SUPERVISOR      = 'S';
const FONDO_SUBCATEGORIA_INSTITUCION     = 'I';
/*
|--------------------------------------------------------------------------
| EVENT 
|--------------------------------------------------------------------------
*/
const DATOS_INVALIDOS           = 'Datos Invalidos: Complete todos los campos.';
const CREADO_SATISFACTORIAMENTE = 'Creado satisfactoriamente.';
const DB_NOT_INSERT             = 'Lo sentimos, Hubo un problema a la hora de guardar en la base de datos.';
const FILESTORAGE_DIR           = 'uploads/';
const APP_ID                    = 2;

const TITULO_INSTITUCIONAL      = 'FONDO INSTITUCIONAL';
const MONTO_DESCUENTO_PLANILLA  = 50;

const FONDO_AJUSTE               = 1;
const FONDO_RETENCION            = 2;
const FONDO_LIBERACION           = 3;
const FONDO_DEPOSITO             = 4;
const FONDO_DEVOLUCION_PLANILLA  = 5;
const FONDO_TRANSFERENCIA        = 6;
const FONDO_DEVOLUCION_TESORERIA = 7;

const TIPO_FAMILIA              = 129;
const TIPO_GERPROD              = 128;

const INVERSION_MKT             = 'M';
const INVERSION_INSTITUCIONAL   = 'I';
const INVERSION_PROV            = 'P';

const FONDO_CUENTA              = 'PRUEBA FONDO CUENTA';

define('TIMELINECESE' , serialize( array(
    '1' => array(
        'status_id' => 30,
        'user_type_id' => 'U',
        'title' => 'Termino por Cese',
        'info' => 'CONTABILIDAD',
        'cond' => true
    ) 
)));


define("TIMELINEHARD", serialize(array(
//    '0' => array(
//        'title' => 'Inicio Fondo Institucional',
//        'info' => '',
//        'cond_sol_type' => SOL_INST
//
//    ),
    '1' => array(
        'status_id' => 3,
        'user_type_id' => 'C',
        'title' => 'Validación Cont',
        'info' => 'CONTABILIDAD',
        'cond_sub_motivo' => SOL_INST
    ),
    '2' => array(
        'status_id' => 1,
        'user_type_id' => 'AG',
        'title' => 'Habilitación Fondo Inst',
        'info' => 'ASISTENTE GERENCIA',
        'cond_add_motivo' => SOL_INST
    ),
    '3' => array(
        'status_id' => 13,
        'user_type_id' => 'T',
        'title' => 'Deposito del Anticipo',
        'info' => 'TESORERÍA',
        'cond_sub_motivo' => REEMBOLSO
    ),
    '4' => array(
        'status_id' => 4,
        'user_type_id' => 'C',
        'title' => 'Asiento de Anticipo',
        'info' => 'CONTABILIDAD',
        'cond_sub_motivo' => REEMBOLSO ,
        'cond_cese' => true 
    ),
    '5' => array(
        'status_id' => 12,
        'user_type_id' => 'R',
        'title' => 'Descargo',
        'info' => 'Responsable del Gasto' ,
        'cond' => true,
        'cond_cese' => true

    ),
    '6' => array(
        'status_id' => 6,
        'user_type_id' => 'C',
        'title' => 'Reg. de Gastos',
        'info' => 'CONTABILIDAD',
        'cond' => true,
        'cond_cese' => true

    ),
    '7' => array(
        'status_id' => 5,
        'user_type_id' => 'C',
        'title' => 'Asiento de Diario',
        'info' => 'CONTABILIDAD',
        'cond' => true ,
        'cond_cese' => true

    ),
    '8' => array(
        'status_id' => 20,
        'user_type_id' => CONT,
        'title' => 'Deposito del Reembolso',
        'info' => 'TESORERIA',
        'cond_add_motivo' => REEMBOLSO,
        'cond_cese' => true
    
    ),
    '9' => array(
        'status_id' => 21,
        'user_type_id' => CONT,
        'title' => 'Asiento del Reembolso',
        'info'  => 'CONTABILIDAD',
        'cond_add_motivo' => REEMBOLSO,
        'cond_cese' => true
    )
)));

const MANTENIMIENTO_FONDO = 6;


/*
|--------------------------------------------------------------------------
| TABLAS
|--------------------------------------------------------------------------
*/
if ( App::environment( 'production' ) && PRODUCCION )
{
    define( 'TB_USUARIOS'         , 'USR.USERS' );
    define( 'TB_TIPO_USUARIO'     , 'USR.TIPO_USUARIO' );
    define( 'TB_USER_APP'         , 'USR.USER_APP' );
    define( 'TB_PERSONAL'         , 'USR.PERSONAL' );
    define( 'TB_TIPO_PERSONAL'    , 'USR.TIPO_PERSONAL' );
    define( 'TB_BAGO_COD_ASIENTO' , 'B3O.CXP_CORREL_ASIENTOS' );
    define( 'TB_BAGO_ASIENTO'     , 'B3O.PENDIEN_DIARIO' );
    define( 'TB_MARCAS_BAGO'      , 'TDP.MARCAS' );
}
else
{
    define( 'TB_USUARIOS'         , 'USRP.USERS' );
    define( 'TB_TIPO_USUARIO'     , 'USRP.TIPO_USUARIO' );
    define( 'TB_USER_APP'         , 'USRP.USER_APP' );
    define( 'TB_PERSONAL'         , 'USRP.PERSONAL' );
    define( 'TB_TIPO_PERSONAL'    , 'USRP.TIPO_PERSONAL' );
    define( 'TB_BAGO_COD_ASIENTO' , 'B3P.CXP_CORREL_ASIENTOS' );
    define( 'TB_BAGO_ASIENTO'     , 'B3P.PENDIEN_DIARIO' );
    define( 'TB_MARCAS_BAGO'      , 'TDPP.MARCAS' );

}

const TB_USUARIO_BAGO                  = 'D1J.USUARIO';
const TB_EVENTO_FOTO                   = 'FILE_STORAGE';
const TB_CLIENTE_TIPO                  = 'TIPO_CLIENTE';
const TB_DISTRIMED_CLIENTES            = 'VTADIS.CLIENTES';
const TB_DOCTOR                        = 'FICPE.PERSONAFIS';
const TB_INSTITUCIONES                 = 'FICPE.PERSONAJUR';
const TB_FARMACIA                      = 'FICPEF.PERSONAJUR';
const TB_DEPOSITO                      = 'DEPOSITO';
//const TB_DOCUMENTO                     = 'FILE_STORAGE';
const TB_ESTADO_SUB                    = 'SUB_ESTADO';
const TB_ESTADO                        = 'ESTADO';
const TB_TIPO_MONEDA                   = 'TIPO_MONEDA';
const TB_TIPO_PAGO                     = 'TIPO_PAGO';
const TB_CUENTA                        = 'CUENTA';
const TB_TIPO_ACTIVIDAD                = 'TIPO_ACTIVIDAD';
const TB_CUENTA_RM                     = 'VAR.BENEFICIARIOS_CTA_BANC';
const TB_INVERSION_ACTIVIDAD           = 'INVERSION_ACTIVIDAD';
const TB_TIPO_INVERSION                = 'TIPO_INVERSION';
const TB_PERIODO                       = 'PERIODO';
const TB_MOTIVO                        = 'MOTIVO';
const TB_SOLICITUD                     = 'SOLICITUD';
const TB_SOLICITUD_CLIENTE             = 'SOLICITUD_CLIENTE';
const TB_SOLICITUD_DETALLE             = 'SOLICITUD_DETALLE';
const TB_SOLICITUD_GERENTE             = 'SOLICITUD_GERENTE';
const TB_SOLICITUD_PRODUCTO            = 'SOLICITUD_PRODUCTO';
const TB_SOLICITUD_TIPO                = 'SOLICITUD_TIPO';
const TB_EVENTO                        = 'EVENT';
const TB_TIPO_CUENTA                   = 'CUENTA_TIPO';
const TB_TIPO_DE_CAMBIO                = 'B3O.CXP_TC';
const TB_ASIENTO                       = 'ASIENTO';
const TB_GASTO                         = 'GASTO';
const TB_GASTO_ITEM                    = 'GASTO_ITEM';
const TB_TIPO_GASTO                    = 'TIPO_GASTO';
const TB_MARCA                         = 'MARCA';
const TB_CUENTA_GASTO_MARCA            = 'CUENTA_GASTO_MARCA';
const TB_PLAN_CUENTA                   = 'B3O.PLANCTA';
const TB_MARCA_DOCUMENTO               = 'DOCUMENTO';
const TB_TIPO_COMPROBANTE              = 'TIPO_COMPROBANTE';
const TB_TIPO_REGIMEN                  = 'TIPO_REGIMEN';
const TB_VTA_TABLAS                    = 'VTA.TABLAS';
const TB_FONDO_CONTABLE                = 'FONDO_CONTABLE';
const TB_FONDO_CATEGORIA               = 'FONDO_CATEGORIA';
const TB_FONDO_GERENTE_PRODUCTO        = 'FONDO_GERENTE_PRODUCTO';
const TB_FONDO_INSTITUCION             = 'FONDO_INSTITUCION';
const TB_FONDO_CATEGORIA_SUB           = 'FONDO_SUBCATEGORIA';
const TB_FONDO_SUPERVISOR              = 'FONDO_SUPERVISOR';
const TB_MANTENIMIENTO                 = 'MANTENIMIENTO';
const TB_PARAMETRO                     = 'PARAMETRO';
const TB_POLITICA_APROBACION           = 'POLITICA_APROBACION';
//const TB_INVERSION_POLITICA_APROBACION = 'INVERSION_POLITICA_APROBACION';//no tiene modelo
const TB_REPORTE_QUERY                 = 'REPORTE_QUERY';
const TB_REPORTE_FORMULA               = 'REPORTE_FORMULA';
const TB_REPORTE_USUARIO               = 'REPORTE_USUARIO';
const TB_FONDO_MARKETING_HISTORIAL     = 'FONDO_MARKETING_HISTORIA';
const TB_SOLICITUD_HISTORIAL           = 'SOLICITUD_HISTORIAL';
const TB_FLUJO_TIEMPO_ESTIMADO         = 'TIEMPO_ESTIMADO_FLUJO';
const TB_LINSUPVIS                     = 'FICPE.LINSUPVIS';
const TB_SUPERVISOR                    = 'FICPE.SUPERVISOR';
const TB_USER_TEMPORAL                 = 'USER_TEMPORAL';
const TB_VISITADOR                     = 'FICPE.VISITADOR';
const TB_DEVOLUCION                    = 'DEVOLUCION';
const TB_TIPO_DEVOLUCION               = 'TIPO_DEVOLUCION';
const TB_ESTADO_DEVOLUCION             = 'ESTADO_DEVOLUCION';

const DEVOLUCION_INMEDIATA             = 1;
const DEVOLUCION_PLANILLA              = 2;
const DEVOLUCION_LIQUIDACION           = 3;

const DEVOLUCION_POR_REALIZAR          = 1;
const DEVOLUCION_POR_VALIDAR           = 2;
const DEVOLUCION_CONFIRMADA            = 3;


const PENDIENTE             = 1;
const ACEPTADO              = 2;
const APROBADO              = 3;
const DEPOSITADO            = 4;
const REGISTRADO            = 5;
const ENTREGADO             = 6;
const GENERADO              = 7;
const CANCELADO             = 8;
const RECHAZADO             = 9;
const TODOS                 = 10;
const DERIVADO              = 11;
const GASTO_HABILITADO      = 12;
const DEPOSITO_HABILITADO   = 13;
const DEVOLUCION            = 14;

const CUENTA_SOLES = 1041100;

//TIPO REGIMEN
const REGIMEN_RETENCION  = 1;
const REGIMEN_DETRACCION = 2;

//IDS TIPO DE DOCUMENTO
const DOC_RECIBO_HONORARIO = 2;
const DOC_NO_SUSTENTABLE = 7;

//TIPOS DE INVERSIONES
const INV_MICROMKT = 17;
