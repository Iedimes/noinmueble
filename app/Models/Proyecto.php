<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'IVMPYL';
    protected $connection = 'sqlsrv';
    protected $primaryKey = 'PylCod';
    public $keyType = 'string';
    public $incrementing = false;



}


