<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sso_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('sso_clients')->onDelete('cascade');
            $table->timestamp('expires_at');
            $table->boolean('revoked')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sso_access_tokens');
    }
};
