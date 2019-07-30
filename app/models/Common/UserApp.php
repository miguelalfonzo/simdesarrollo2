<?php
/**
 * Created by PhpStorm.
 * User: Developer
 * Date: 05/11/2014
 * Time: 03:21 PM
 */
namespace Common;
use \Eloquent;
class UserApp extends Eloquent{


    protected $table = TB_USER_APP;
    protected $primaryKey = 'id';

}