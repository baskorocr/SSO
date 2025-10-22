<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sso_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client_id')->unique();
            $table->string('client_secret');
            $table->text('redirect_uris'); // JSON array of allowed redirect URIs
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sso_clients');
    }
};
