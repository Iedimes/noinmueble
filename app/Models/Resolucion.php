<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resolucion extends Model
{
    protected $table = 'PRMCLI1';
    protected $connection = 'sqlsrv';
    protected $primaryKey = 'PerCod';
    public $keyType = 'string';
    public $incrementing = false;
}
