<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpouseNameToFamilyMembersTable extends Migration
{
    public function up()
    {
        Schema::table('family_members', function (Blueprint $table) {
            // Menambahkan kolom spouse_name setelah kolom birth_date
            $table->string('spouse_name')->nullable()->after('birth_date');
        });
    }

    public function down()
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn('spouse_name');
        });
    }
}