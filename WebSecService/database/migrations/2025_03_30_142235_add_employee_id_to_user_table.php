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
        Schema::table('users', function (Blueprint $table) {
            //because id in user is bigint the foreign key should be bigint
            $table->unsignedBigInteger('employee_id')->nullable()->default(null)->after('id'); // Default NULL
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('set null'); // Foreign key

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            
            $table->dropColumn('employee_id');

        });
    }
};
