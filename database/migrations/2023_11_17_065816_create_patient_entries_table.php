<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('patient_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->onDelete('NO ACTION')->onUpdate('NO ACTION');
        $table->dateTime('date');
        $table->tinyInteger('new_patient');
        $table->integer('nursing_class');
        $table->foreignId('service_id')->constrained()->onDelete('SET NULL')->onUpdate('SET NULL');
        $table->foreignId('payment_id')->constrained()->onDelete('SET NULL')->onUpdate('SET NULL');
        $table->foreignId('room_id')->constrained()->onDelete('SET NULL')->onUpdate('SET NULL');
        $table->dateTime('out_date')->nullable();
        $table->string('way_out')->nullable();
        $table->string('dpjb')->nullable();
        $table->enum('status_patient', ['entry', 'exit'])->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_entries');
    }
};
