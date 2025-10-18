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
        Schema::create('prendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_vestimenta_id');
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('talla');
            $table->string('color');
            $table->decimal('precio_alquiler', 8, 2);
            $table->integer('stock')->default(0);
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('marca')->nullable();
            $table->string('genero')->nullable();
            $table->timestamps();
    
            $table->foreign('categoria_vestimenta_id')
                ->references('id')->on('categoria_vestimentas')
                ->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('prendas');
    }
    
};