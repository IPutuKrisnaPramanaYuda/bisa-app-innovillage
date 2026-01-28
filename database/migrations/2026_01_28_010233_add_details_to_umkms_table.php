<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('umkms', function (Blueprint $table) {
            // 1. Cek Kolom Image
            if (!Schema::hasColumn('umkms', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            
            // 2. Cek Kolom Address (Ini yang bikin error tadi)
            if (!Schema::hasColumn('umkms', 'address')) {
                $table->text('address')->nullable()->after('image');
            }
            
            // 3. Cek Kolom Phone
            if (!Schema::hasColumn('umkms', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
        });
    }

    public function down()
    {
        Schema::table('umkms', function (Blueprint $table) {
            // Hapus hanya jika ada
            $columns = [];
            if (Schema::hasColumn('umkms', 'image')) $columns[] = 'image';
            if (Schema::hasColumn('umkms', 'address')) $columns[] = 'address';
            if (Schema::hasColumn('umkms', 'phone')) $columns[] = 'phone';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};