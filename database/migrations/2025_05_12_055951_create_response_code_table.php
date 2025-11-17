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

        Schema::create('response_remarks', function (Blueprint $table) {
            $table->id();
            $table->string('resp_code')->unique()->comment('Kode respon unik yang akan dikirim di API.');
            $table->string('resp_desc')->comment('Deskripsi dari kode respon.');
            $table->string('resp_type')->index()->comment('Tipe respon, cth: SUCCESS, FAILED, VALIDATION_ERROR.');

            $table->string('resp_group')->index()->comment('Grup fungsional dari respon, cth: AUTH, PAYMENT, USER.');
            $table->unsignedSmallInteger('http_code')->default(\Illuminate\Http\Response::HTTP_OK)->comment('Kode status HTTP yang sesuai.');

            $table->string('const_name')->nullable()->unique()->comment('Nama konstanta/enum case yang akan digenerasi, cth: CODE_SUCCESS.');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_remarks');
    }
};
