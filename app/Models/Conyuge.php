<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conyuge extends Model
{
    protected $table = 'IVMSOL';
    protected $connection = 'sqlsrv';
    protected $primaryKey = 'SolPerCod';
    public $keyType = 'string';
    public $incrementing = false;
}
