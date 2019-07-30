<?php
/**
 * Created by PhpStorm.
 * User: Developer
 * Date: 06/10/14
 * Time: 11:21 AM
 */
namespace Common;
use \Eloquent;

class TypeUser extends Eloquent{

    protected $table = TB_TIPO_USUARIO;
    protected $primaryKey = 'codigo';

    // protected static function dmkt()
    // {
    // 	return TypeUser::whereIn('codigo' , array( SUP , GER_PROD , ASIS_GER ) )->get();
    // }


}