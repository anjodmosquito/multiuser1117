<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medicine_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('lprice', 10, 2);
            $table->decimal('mprice', 10, 2);
            $table->decimal('hprice', 10, 2);
            $table->integer('quantity_added');
            $table->integer('total_quantity');
            $table->string('dosage');
            $table->date('expdate');
            $table->string('action_type'); // 'add' or 'update'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicine_histories');
    }
}; 