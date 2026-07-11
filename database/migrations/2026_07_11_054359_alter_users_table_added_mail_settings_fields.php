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
            $table->string('mail_mailer')->nullable()->after('remember_token');
            $table->string('mail_scheme')->nullable()->after('mail_mailer');
            $table->string('mail_host')->nullable()->after('mail_scheme');
            $table->string('mail_username')->nullable()->after('mail_host');
            $table->string('mail_password')->nullable()->unique()->after('mail_username');
            $table->string('mail_from_address')->nullable()->after('mail_password');
            $table->string('mail_from_name')->nullable()->after('mail_From_address');
        });
    }

    /**
     * Reverse the migrations.
    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    } 
     */
};
