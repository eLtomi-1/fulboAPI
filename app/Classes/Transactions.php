<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;

trait Transactions
{
    public static function bloquearTablaWrite($Tabla){
        DB::raw('LOCK TABLE '. $Tabla  .' WRITE');
    }

    public static function bloquearTablaRead($Tabla){
        DB::raw('LOCK TABLE '. $Tabla  .' READ');
    }

    public static function commitearYDesbloquearTablas(){
        DB::commit();
        DB::raw('UNLOCK TABLES');
    }

    public static function rollbackearTablas(){
        DB::rollBack();
    }

    public static function iniciarTransaccion(){
        DB::beginTransaction();
    }
}
