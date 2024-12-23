<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use behzadsp\MailTracker\MailTracker;

class CreateSentEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(MailTracker::sentEmailModel()->getConnectionName())->create('sent_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('sender_id')->nullable();
            $table->foreignId('template_id')->nullable();
            $table->foreignId('scenario_id')->nullable();
            $table->nullableMorphs('emailable');
            $table->char('hash', 32)->unique();
            $table->text('headers')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->integer('opens')->nullable();
            $table->integer('clicks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(MailTracker::sentEmailModel()->getConnectionName())->drop('sent_emails');
    }
}
