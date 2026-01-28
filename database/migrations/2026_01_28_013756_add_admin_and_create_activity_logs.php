<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah kolom is_admin di tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('email');
            }
        });

        // 2. Buat tabel Activity Logs (Untuk pantau user lagi buka apa)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('url');      // Halaman yang dibuka
            $table->string('method');   // GET/POST
            $table->string('ip');       // IP Address
            $table->string('agent')->nullable(); // Browser/Device
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
        Schema::dropIfExists('activity_logs');
    }
};