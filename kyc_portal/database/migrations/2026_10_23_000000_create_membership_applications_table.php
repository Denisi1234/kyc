<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 24)->unique();
            $table->string('full_name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 32)->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->string('residence')->nullable();
            $table->string('preferred_section', 30)->nullable();
            $table->text('membership_information')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('privacy_agreed_at');
            $table->ipAddress('submitted_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
