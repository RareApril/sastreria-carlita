<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('detalle_reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reserva_id');
            $table->unsignedBigInteger('prenda_id');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_alquiler', 8, 2);
            $table->timestamps();
    
            $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
            $table->foreign('prenda_id')->references('id')->on('prendas')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('detalle_reservas');
    }
    
};
