<?php

namespace Dmkt;
use \Eloquent;
use Illuminate\Database\Eloquent\Collection;
class Account extends Eloquent
{
    protected $table = TB_CUENTA;
    protected $primaryKey = 'id';
    
    public function lastId()
    {
        $lastId = Account::orderBy('id', 'desc')->first();
        if( $lastId == null )
            return 0;
        else
            return $lastId->id;
    }

    protected function bagoAccount()
    {
        return $this->belongsTo('Expense\PlanCta' , 'num_cuenta' , 'ctactaextern' );
    }

    public function typeAccount()
    {
        return $this->hasOne('Expense\AccountType','id','idtipocuenta');
    }

    protected function fondo()
    {
        return $this->belongsTo('Fondo\Fondo' , 'id' , 'idcuenta');
    }


    protected function typeMoney()
    {
        return $this->hasOne('Common\TypeMoney','id','idtipomoneda');
    }

    public static function banks()
    {
        $banks = Account::whereHas('typeAccount' , function( $q )
        {
            $q->where('nombre','BANCOS');
        })->get();
        return $banks;
    }

    public static function banksSP()
    {
       $row = \DB::transaction(function($conn){
            
                $pdo = $conn->getPdo();
                $stmt = $pdo->prepare('BEGIN SP_LISTAR_BANCOS(:data); END;');
                $stmt->bindParam(':data', $lista, \PDO::PARAM_STMT);
                $stmt->execute();       
                oci_execute($lista, OCI_DEFAULT);
                oci_fetch_all($lista, $array, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC );
                oci_free_cursor($lista);
                return $array;

        });

        return new Collection($row);
    }

    protected function fondoRelations()
    {
        return $this->hasMany( 'Expense\MarkProofAccounts' , 'idcuentafondo' , 'id');
    }

    protected static function getExpenseAccount( $cuenta_mkt )
    {
        return Account::leftJoin(TB_CUENTA_GASTO_MARCA.' b' , 'b.num_cuenta_fondo' , '=' , 'cuenta.num_cuenta' )
        ->leftJoin(TB_CUENTA.' c' , 'c.num_cuenta' , '=' , 'b.num_cuenta_gasto' )->select('c.*')->where('cuenta.num_cuenta' , $cuenta_mkt )->get();
    }

    protected static function getAccount( $cuenta )
    {
        return Account::where( 'num_cuenta' , $cuenta )->first();
    }

    protected static function getFirstExpenseAccount( $accountNumber )
    {
        return Account::where( 'num_cuenta' , $accountNumber )->where( 'idtipocuenta' , 4 )->first();
    }

}