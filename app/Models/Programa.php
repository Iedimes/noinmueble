<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $table = 'IVMPRG';
    protected $connection = 'sqlsrv';
    protected $primaryKey = 'PylCod';
    public $keyType = 'string';
    public $incrementing = false;
}
