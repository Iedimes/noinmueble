<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bamper extends Model
{
    protected $table = 'BAMPER';
    protected $connection = 'sqlsrv';
    public $incrementing = false;
    protected $primaryKey = 'PerCod';
    public $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'PerCod',
        'PerNom',
        'PerFchNac',

    ];





    protected $dates = [
        'PerFchNac',
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/bampers/'.$this->getKey());
    }
}
