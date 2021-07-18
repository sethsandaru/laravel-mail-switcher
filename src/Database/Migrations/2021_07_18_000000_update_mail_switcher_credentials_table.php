<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMailSwitcherCredentialsTable extends Migration
{

    public function up() {
        Schema::table('mail_switcher_credentials', function (Blueprint $table) {
            $table->string('driver')->default('smtp');
        });
    }

    public function down() {
        Schema::table('mail_switcher_credentials', function (Blueprint $table) {
            $table->dropColumn('driver');
        });
    }
}
