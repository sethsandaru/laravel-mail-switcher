<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailSwitcherCredentialsTable extends Migration
{

    public function up() {
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
                'monthly'
            ]);

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('mail_switcher_credentials');
    }
}
