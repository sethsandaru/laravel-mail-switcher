<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use SethPhat\MailSwitcher\Models\MailCredential;

class CreateMailSwitcherCredentialsTable extends Migration
{
    public function up(): void
    {
        Schema::create('mail_switcher_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');
            $table->string('server');
            $table->string('port');
            $table->string('encryption');

            $table->integer('threshold');
            $table->integer('current_threshold')->default(0);
            $table->enum('threshold_type', [
                'daily',
                'weekly',
                'monthly',
            ])->default(MailCredential::THRESHOLD_TYPE_MONTHLY);
            $table->timestamp('threshold_start')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_switcher_credentials');
    }
}
