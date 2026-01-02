<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Cek dulu biar gak error kalau sudah ada
            if (!Schema::hasColumn('chat_sessions', 'session_id')) {
                $table->string('session_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('chat_sessions', 'topic_title')) {
                $table->string('topic_title')->nullable()->after('session_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'topic_title']);
        });
    }
};