<?php

    /*
    |--------------------------------------------------------------------------
    | SYSTEM
    |--------------------------------------------------------------------------
    */
    use \Client\ClientType;
    use \Dmkt\Solicitud;

    Route::group( array( 'before' => 'developer' , 'namespace' => 'Seat' , 'prefix' => 'migrate' ) , function()
    {
        Route::get( 'entries' , 'MigrateSeatController@migrateEntries');
    });

    Route::get( "logs", [
        "before" => "developer",
        "uses" => "\Rap2hpoutre\LaravelLogViewer\LogViewerController@index"
    ]);

    Route::post( 'solicitud-deposit-db' , 'Deposit\DepositController@adminSolicitudDeposit' );

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    Route::get(  '/'      , array( 'uses' => 'Dmkt\LoginController@showLogin' ) );
    Route::get(  'login'  , array( 'uses' => 'Dmkt\LoginController@showLogin' ) );
    Route::post( 'login'  , array( 'uses' => 'Dmkt\LoginController@doLogin' ) );
    Route::get(  'logout' , array( 'uses' => 'Dmkt\LoginController@doLogout' ) );

    /*
    |--------------------------------------------------------------------------
    | SUPERVISOR
    |--------------------------------------------------------------------------
    */

    Route::group( array( 'before' => 'sup' ) , function()
    {
        Route::group( [ 'prefix' => 'report/sup' , 'namespace' => 'Report' ] , function()
        {
            Route::get( 'view/{type}' , 'Funds@show' );
            Route::post( 'data' , 'Funds@source' );
            Route::get( 'export-{type}-{category}' , 'Funds@export' );
      
        });
    });

    /*
    |--------------------------------------------------------------------------
    | GERENTE COMERCIAL
    |--------------------------------------------------------------------------
    */

    Route::group( array( 'before' => 'gercom' ) , function()
    {
        Route::get( 'fondoHistorial' , 'Fondo\FondoMkt@getFondoHistorial' );
        Route::post( 'fondo-subcategoria-history' , 'Fondo\FondoMkt@getFondoSubCategoryHistory' );
        Route::get( 'export-fondoHistorial-{start}-{end}-{subCategory}' , 'Fondo\FondoMkt@exportFondoHistorial' );
    });

    Route::group( array( 'before' => 'gercom_cont' ) , function()
    {
        Route::post( 'gercom-mass-approv' ,'Dmkt\SolicitudeController@massApprovedSolicitudes');
    });


    /*
    |--------------------------------------------------------------------------
    | MANTENIMIENTO
    |--------------------------------------------------------------------------
    */

    Route::group( [ 'before' => 'maintenance_roles' ] , function()
    {
        Route::group( [ 'namespace' => 'Maintenance' ] , function()
        {
            Route::get(  'maintenance/view/{type}'    , 'TableController@getView' );
            Route::get( 'maintenance-export/{type}'   , 'TableController@export' );

            Route::post( 'get-cell-maintenance-info'  , 'TableController@getMaintenanceCellData' );
            Route::post( 'update-maintenance-info'    , 'TableController@updateMaintenanceData' );
            Route::post( 'save-maintenance-info'      , 'TableController@saveMaintenanceData' );
            Route::post( 'add-maintenance-info'       , 'TableController@addMaintenanceData' );
            Route::post( 'maintenance-enable'         , 'TableController@enableRecord');
            Route::post( 'maintenance-disable'        , 'TableController@disableRecord');

        });
    });

    /*
    |--------------------------------------------------------------------------
    | CONTABILIDAD
    |--------------------------------------------------------------------------
    */

    Route::group( array( 'before' => 'cont' ), function ()
    {

        Route::post( 'list-documents', 'Movements\MoveController@searchDocs' );
        Route::post( 'search-responsibles' , 'Source\Seeker@responsibleSource' );
        Route::group( [ 'namespace' => 'Expense' ] , function()
        {
            Route::get( 'edit-expense-cont', 'ExpenseController@editExpense');
            Route::post( 'maintenance/cont-document-manage' , 'ExpenseController@manageDocument');
            Route::post( 'get-document-detail' , 'ExpenseController@getDocument');
            Route::post( 'update-document' , 'ExpenseController@updateDocument');
            Route::post( 'end-expense-record' , 'ExpenseController@endExpenseRecord' );
        });

        Route::group( [ 'namespace' => 'Deposit' ] , function()
        {
            Route::post( 'modal-liquidation' , 'DepositController@modalLiquidation' );
            Route::post( 'confirm-liquidation' , 'DepositController@confirmLiquidation' );
        });

        Route::group( [ 'namespace' => 'Dmkt' ] , function()
        {
            Route::get( 'solicitudsToDeposit' , 'SolicitudeController@solicitudsToDeposit' );
            Route::get( 'maintenance/finddocument', 'SolicitudeController@findDocument' );
            Route::post( 'get-account', 'SolicitudeController@getCuentaContHandler' );
            Route::post( 'revisar-solicitud', 'SolicitudeController@checkSolicitud' );
            Route::post( 'massive-solicitud-revision' , 'SolicitudeController@massiveSolicitudsRevision' );
            Route::get( 'list-documents-type', 'FondoController@listDocuments' );
            Route::get( 'maintenance/documenttype', 'FondoController@listDocuments' );
        });

        Route::group( [ 'namespace' => 'Seat' ] , function()
        {
            Route::get( 'generar-asiento-gasto/{token}' , 'Generate@viewGenerateEntryExpense' );
            Route::post( 'generar-asiento-anticipo' , 'Generate@generateAdvanceEntry' );
            Route::post( 'guardar-asiento-gasto', 'Generate@saveEntryExpense');
        });

        Route::group( [ 'namespace' => 'Export' ] , function()
        {
            Route::get( 'revision-export' , 'ExportController@revisionExport');
            Route::get( 'advance-entry-export' , 'ExportController@advanceEntryExport' );
            Route::get( 'regularization-entry-export' , 'ExportController@regularizationEntryExport' );
            Route::get( 'export/solicitudToDeposit-pdf' , 'ExportController@exportSolicitudToDepositPDF' );
            Route::get( 'export/solicitudToDeposit-excel' , 'ExportController@exportSolicitudToDepositExcel' );
        });

        Route::group( [ 'prefix' => 'report/cont' , 'namespace' => 'Report' ] , function()
        {
            Route::get( 'view/{type}' , 'Accounting@show' );
            Route::post( 'data' , 'Accounting@source' );
            Route::post( 'export' , 'Accounting@export' );
            Route::get( 'export/{type}-{title}' , 'Accounting@download' );
        });

    });

    /*
    |--------------------------------------------------------------------------
    | TESORERIA
    |--------------------------------------------------------------------------
    */

    Route::group( array( 'before' => 'tes' ), function()
    {
        Route::post( 'solicitud-deposit' , 'Deposit\DepositController@solicitudDeposit' );
        Route::post( 'massive-solicitud-deposit' , 'Deposit\DepositController@massiveSolicitudDeposit' );
        Route::get( 'deposit-export' , 'Deposit\DepositController@depositExport' );
        Route::post( 'modal-extorno' , 'Deposit\DepositController@modalExtorno' );
        Route::post( 'confirm-extorno' , 'Deposit\DepositController@confirmExtorno' );
    });

    /*
    |--------------------------------------------------------------------------
    | ASISTENTE GERENCIA
    |--------------------------------------------------------------------------
    */

    Route::group( array('before' => 'ager' ) , function()
    {
        Route::get('solicitude/institution', 'Dmkt\SolicitudeController@showSolicitudeInstitution');
        Route::post('registrar-fondo','Dmkt\FondoController@registerInstitutionalApplication');
        Route::get('exportfondos/{date}','Dmkt\FondoController@exportExcelFondos');
        Route::get('endfondos/{date}','Dmkt\FondoController@endfondos');
        Route::post('search-rep', 'Source\Seeker@repSource');
        Route::post('info-rep', 'Source\Seeker@repInfo');
        Route::get('list-fondos/{date}','Dmkt\FondoController@listInstitutionalSolicitud');
        Route::post('get-sol-inst' , 'Dmkt\FondoController@getSolInst');
        Route::post('search-institution', 'Source\Seeker@institutionSource');
    });

    /*
    |--------------------------------------------------------------------------
    | SUPERVISOR , GERENTE DE PRODUCTO - PROMOCION - COMERCIAL - GENERAL
    |--------------------------------------------------------------------------
    */

    Route::group(array('before' => 'sup_gerprod_gerprom_gercom_gerger'), function()
    {
        Route::post( 'search-users' , 'Source\Seeker@userSource');
        Route::post( 'confirm-temporal-user' , 'User\UserController@assignTemporalUser');
        Route::get( 'remove-temporal-user' , 'User\UserController@removeTemporalUser');
        Route::post('aceptar-solicitud', 'Dmkt\SolicitudeController@acceptedSolicitude');
        Route::post( 'agregar-familia-fondo', 'Dmkt\SolicitudeController@addFamilyFundSolicitud');


    });

    /*
    |--------------------------------------------------------------------------
    | REPRESENTANTE , SUPERVISOR , GERENTE DE PRODUCTO - PROMOCION - COMERCIAL - GENERAL , CONTABILIDAD
    |--------------------------------------------------------------------------
    */

    Route::group( array( 'before' => 'rm_sup_gerprod_gerprom_gercom_gerger_cont' ) , function()
    {
        Route::get('solicitude/statement', 'Movements\MoveController@getStatement');
    });

    /*
    |--------------------------------------------------------------------------
    | REPRESENTANTE , CONTABILIDAD , TESORERIA
    |--------------------------------------------------------------------------
    */

    Route::group( array( 'before' => 'rm_sup_cont_tes' ), function ()
    {
        Route::post( 'register-devolution-data' , 'Devolution\DevolutionController@registerDevolutionData' );
        Route::post( 'get-devolution-info' , 'Devolution\DevolutionController@getDevolutionInfo' );
        Route::post( 'get-inmediate-devolution-register-info' , 'Devolution\DevolutionController@getInmediateDevolutionRegisterInfo' );
        Route::post( 'get-inmediate-devolution-confirmation-info' , 'Devolution\DevolutionController@getInmediateDevolutionConfirmationInfo' );
        Route::post( 'get-payroll-info' , 'Devolution\DevolutionController@getPayrollInfo');
        Route::post( 'register-inmediate-devolution', 'Devolution\DevolutionController@registerInmediateDevolution' );
        Route::post( 'confirm-payroll-discount' , 'Devolution\DevolutionController@confirmPayrollDiscount' );
    });

    Route::group( array( 'before' => 'rm_sup' ), function ()
    {
        Route::post( 'end-expense', 'Expense\ExpenseController@finishExpense');
        Route::post( 'do-inmediate-devolution' , 'Devolution\DevolutionController@doInmediateDevolution' );
        Route::post( 'createEvent', 'Dmkt\SolicitudeController@createEventHandler');
        Route::post( 'testUploadImgSave', 'Dmkt\SolicitudeController@uploadImgSave');

    });

    Route::group( array( 'before' => 'rm_sup_cont' ), function ()
    {
        Route::post( 'get-expenses' , 'Expense\ExpenseController@getExpenses');
        Route::post( 'edit-expense', 'Expense\ExpenseController@editExpense');
        Route::post( 'delete-expense', 'Expense\ExpenseController@deleteExpense');
        Route::post( 'register-expense', 'Expense\ExpenseController@registerExpense');
        Route::get( 'a/{token}', 'Expense\ExpenseController@reportExpense');
        Route::get( 'report-fondo/{token}','Expense\ExpenseController@reportExpenseFondo');
    });

    Route::group( array( 'before' => 'rm_sup_gerprod_ager' ), function ()
    {
        Route::get( 'nueva-solicitud', 'Dmkt\SolicitudeController@newSolicitude');
        Route::get( 'get-investments-activities' , 'Dmkt\SolicitudeController@getInvestmentsActivities');
        Route::post( 'registrar-solicitud', 'Dmkt\SolicitudeController@registerSolicitud');
        Route::get( 'editar-solicitud/{id}', 'Dmkt\SolicitudeController@editSolicitud');
        Route::post( 'filtro_cliente' , 'Dmkt\Client@getInvestmentActivity');
        Route::post( 'filtro-inversion' , 'Dmkt\Client@getActivities');
         

    });

    Route::group( array( 'before' => 'rm_sup_gerprod_gerprom_gercom_gerger_ager_cont' ) , function()
    {
        Route::post('cancelar-solicitud', 'Dmkt\SolicitudeController@cancelSolicitud');
    });

    Route::group( array( 'before' => 'rm_sup_gerprod_gerprom_gercom_gerger_ager' ) , function()
    {
        #Route::post( 'carga-Listado-Clientes', 'Source\Seeker@clientSource');
        Route::post( 'search-client', 'Source\Seeker@clientSource');
        Route::post( 'get-client-view' , 'Source\Seeker@getClientView');
    });

   Route::get('buscar-clientes-autocomplete', 'Source\Seeker@clientSource');

    Route::group( array( 'before' => 'estudio' ) , function()
    {
        Route::group( [ 'namespace' => 'Synchro' ] , function()
        {
            Route::get( 'view-sup-rep' , 'SynchroController@viewSupRep' );
            Route::post( 'update-sup-rep' , 'SynchroController@updateSupRep' );
        });
        Route::group( [ 'namespace' => 'PPTO' ] , function()
        {
            Route::get( 'view-ppto'    , 'PPTOController@view' );
            Route::post( 'upload-ppto' , 'PPTOController@upload' );
            Route::post( 'upload-ppto-sup' , 'PPTOController@uploadCategoryFamilyUser' );
            Route::post( 'load-ppto' , 'PPTOController@loadPPTO' );
            Route::post( 'update-ppto-row' , 'PPTOController@update' );
            Route::post( 'ppto-status' , 'PPTOController@status' );
            Route::post( 'ppto-enable' , 'PPTOController@enable' );
            Route::post( 'ppto-disable' , 'PPTOController@disable' );
            Route::post( 'ppto-versions' , 'PPTOController@getVersions' );
            Route::get( 'ppto-export-{type}-{year}-{category}-{version}' , 'PPTOController@export' );
        });

        Route::group( [ 'namespace' => 'Fondo' ] , function()
        {
            Route::get( 'view-fondo-category-maintenance' , 'Maintenance@subCategoryView' );
        });
    });

    Route::group( array( 'before' => 'sys_user' ) , function()
    {
        Route::get( 'show_user' , 'Dmkt\SolicitudeController@showUser' );
    });

    Route::group( array('before' => 'process_user' ) , function ()
    {
        Route::post( 'buscar-solicitudes'          , 'Dmkt\SolicitudeController@searchDmkt' );
        Route::get(  'listar-solicitudes/{estado}' , 'Dmkt\SolicitudeController@listSolicitude' );
        Route::get(  'getclients'                  , 'Dmkt\SolicitudeController@getClients' );
        Route::post( 'list-account-state'          , 'Movements\MoveController@searchMove' );
        Route::get(  'ver-solicitud/{token}'       , 'Dmkt\SolicitudeController@viewSolicitude' );
        Route::get(  'show-fondo/{token}'          , 'Expense\ExpenseController@showFondo' );
        Route::post( 'list-table'                  , 'Movements\MoveController@getTable' );
        Route::post( 'detail-solicitud'            , 'Movements\MoveController@getSolicitudDetail' );
        Route::get(  'getleyenda'                  , 'BaseController@getLeyenda' );
        Route::get(  'list-solicituds'             , 'Movements\MoveController@getSolicituds' );

        /*
        |--------------------------------------------------------------------------
        | TIME LINE
        |--------------------------------------------------------------------------
        */

        Route::get( 'timeline-modal/{id}' , 'TimeLine\Controller@getTimeLine');

        /*
        |--------------------------------------------------------------------------
        | Alert
        |--------------------------------------------------------------------------
        */
        Route::get( 'alerts' , 'Alert\AlertController@show' );
        Route::post( 'alerts' , 'Alert\AlertController@showAlerts' );

        /*
        |--------------------------------------------------------------------------
        | idkc: REPORT
        |--------------------------------------------------------------------------
        */
        // REPORT MAIN PAGE
        Route::get('reports', 'Report\ReportController@mainHandler');
        // GENERATE REPORT TABLE VIEW
        Route::get('reports/generate_html/{id_reporte}/{fromDate}/{toDate}', 'Report\ReportController@reportViewHandler');
        // CREATE EXCEL
        Route::post('reports/export/generate','Report\ReportController@reportExcelHandler');
        // LIST DATASET
        Route::get('reports/getQuerys', 'Report\ReportController@listDatasetHandler');
        // LIST COLUMNS OF DATASET
        Route::get('reports/getColumnsDataSet/{queryId}', 'Report\ReportController@listColumnsDatasetHandler');
        // SAVE NEW REPORT
        Route::post('reports/save', 'Report\ReportController@saveReportHandler');
        // LIST REPORTS OF USERS
        Route::get('reports/getUserReports', 'Report\ReportController@listReportsUserHandler');
        // SEND MAIL
        Route::post('mail_send','PostMan@sendEmailHandler');

        // DOWNLOAD REPORT EXCEL
        Route::get('reports/export/download/{userId}/{reportId}/{fromDate}/{toDate}', 'Report\ReportController@downloadReportExcelHandler');

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */
        Route::get('eventos','Dmkt\SolicitudeController@album');
        Route::post('eventos/list','Dmkt\SolicitudeController@getEventList');
        Route::post('photos', 'Dmkt\SolicitudeController@photos');

    });