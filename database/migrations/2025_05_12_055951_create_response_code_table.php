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
            $table->string('resp_code')->unique();
            $table->string('resp_desc');
            $table->enum('resp_type',\Rezky\LaravelResponseFormatter\Models\ResponseRemark::getAvailableResponseTypes());
            $table->string('resp_group')->comment(\Illuminate\Support\Arr::join(\Rezky\LaravelResponseFormatter\Models\ResponseRemark::getAvailableResponseGroup(),','));
            $table->enum('http_code',array_keys(\Illuminate\Http\Response::$statusTexts));
            $table->string('const_name')->nullable()->unique();
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
