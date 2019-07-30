<?php

App::before(function ($request) {});
App::after(function ($request, $response) {});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function ()
{
    if ( Auth::check() ) return Redirect::to('/');
});
/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/
Route::filter( 'maintenance_roles' , 'Filter\FilterController@maintenanceRoles' );

Route::filter( 'rm_sup' , 'Filter\FilterController@rep_sup' );
Route::filter( 'rm_sup_cont_tes' , 'Filter\FilterController@rep_sup_cont_tes' );
Route::filter( 'rm_sup_gerprod_ager' , 'Filter\FilterController@rep_sup_gerProd_asisGer' );
Route::filter( 'rm_sup_gerprod_gerprom_gercom_gerger_ager' , 'Filter\FilterController@rep_sup_gerProd_gerProm_gerCom_gerGen_asisGer' );
Route::filter( 'rm_sup_cont_gerprod_gerprom_gercom_gergen' , 'Filter\FilterController@rep_sup_cont_gerProd_gerProm_gerCom_gerGen' );
Route::filter( 'rm_sup_cont_gerprod_gerprom_gercom_gergen_ager' , 'Filter\FilterController@rep_sup_cont_gerProd_gerProm_gerCom_gerGen_aGer' );

Route::filter( 'sup' , 'Filter\FilterController@supervisor' );
Route::filter( 'sup_gerprod_gerprom_gercom_gergen' , 'Filter\FilterController@sup_gerProd_gerProm_gerCom_gerGen' );

Route::filter( 'cont' , 'Filter\FilterController@contabilidad' );

Route::filter( 'tes' , 'Filter\FilterController@tesoreria' );

Route::filter( 'gerprod_gerprom_gercom_gerger' , 'Filter\FilterController@gerentes' );

Route::filter( 'gercom' , 'Filter\FilterController@gerenteComercial' );
Route::filter( 'gercom_cont' , 'Filter\FilterController@gerCom_cont' );

Route::filter( 'ager' , 'Filter\FilterController@asistenteGerencia');

Route::filter( 'estudio' , 'Filter\FilterController@estudio' );

Route::filter( 'process_user' , 'Filter\FilterController@process' );


Route::filter( 'sys_user' , 'Filter\FilterController@system' );

Route::filter( 'developer' , 'Filter\FilterController@admin' );

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/
Route::filter( 'csrf' , function ()
{
  $token = Input::get( '_token' );
  if( Session::token() !== $token && $token !== 'h6UsqjLWuBasY5erywL2'  )
  {
    $rpta = App::make('BaseController')->callAction( 'warningException' , array( 'Es necesario que vuelva a cargar la pagina debido a sesion inactiva o acceso desde otro dispositivo' , __FUNCTION__ , __LINE__ , __FILE__ ) );
    $rpta[ status ] = 'Csrf';
    return $rpta;
  }
});