<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    protected $table = 'PRMCLI';
    protected $connection = 'sqlsrv';
    protected $primaryKey = 'PerCod';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'PerCod',
        'PylCod',
        'CliNop',
        'CliSec',
        'CliEsv',
        'ManCod',
        'VivLote',
        'VivBlo',
        'CliCMor',

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];
    // protected $with = ['beneficiario', 'proyecto'];


    //  public function beneficiario()
    //   {
    //      return $this->belongsTo(Bamper::class, 'PerCod', 'PerCod');
    //   }

    //  public function proyecto()
    //  {
    //     return $this->belongsTo(Proyecto::class, 'PylCod', 'PylCod');
    //  }

    // public function resolucion()
    //   {
    //      return $this->belongsTo(Resolucion::class, 'PerCod' ,'PerCod')->where('CliTMov', 1)->orderBy('CliNop');

    //   }

    //   public function ctacte()
    //   {
    //      return $this->belongsTo(Ctacte::class, 'PerCod', 'VivPer' );
    //      ;
    //   }

    //   public function contrato()
    //   {
    //      return $this->belongsTo(Resolucion::class, 'PerCod', 'PerCod')->where('CliElaCon', '=', 'S');
    //      ;
    //   }


    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/beneficiarios/'.$this->getKey());
    }
}
