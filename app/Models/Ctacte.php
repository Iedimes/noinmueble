<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ctacte extends Model
{
    protected $table = 'IVMVIV';
    protected $connection = 'sqlsrv';
    protected $primaryKey = 'PylCod';
    public $keyType = 'string';
    public $incrementing = false;
}
