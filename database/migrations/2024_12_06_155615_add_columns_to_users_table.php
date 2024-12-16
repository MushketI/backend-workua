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
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('image')->nullable()->after('phone');
            $table->unsignedBigInteger('location_id')->nullable()->after('image');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['location_id']);
            $table->dropColumn(['role_id', 'phone', 'image', 'location_id']);
        });
    }
};
