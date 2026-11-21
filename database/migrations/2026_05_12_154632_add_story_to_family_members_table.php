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
        Schema::table('family_members', function (Blueprint $table) {
            // Menambah kolom 'story' tipe TEXT (panjang) yang boleh kosong
            $table->text('story')->after('bio')->nullable();
        });
    }

    public function down()
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn('story');
        });
    }
};
