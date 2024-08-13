<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date')->comment('開始日時');
            $table->dateTime('end_date')->comment('終了日時');
            $table->string('event_name')->comment('イベント名');
            $table->string('location')->nullable()->comment('場所');
            $table->string('link')->nullable()->comment('リンク');
            $table->string('memo')->nullable()->comment('メモ');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
