<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiario', function (Blueprint $table) {
            $table->string('PerCod');
            $table->string('PylCod');
            $table->string('CliNop');
            $table->string('CliSec');
            $table->string('CliEsv');
            $table->string('ManCod');
            $table->string('VivLote');
            $table->string('VivBlo');
            $table->string('CliCMor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiario');
    }
}
