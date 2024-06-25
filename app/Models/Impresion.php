<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Impresion extends Model implements Auditable
{
    protected $fillable = [
        'ci',
        'fecha_impresion',


    ];

    protected $dates = [
         'created_at',
         'updated_at',

     ];

    use AuditableTrait;

    protected $guarded = [];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/impresions/'.$this->getKey());
    }
}
