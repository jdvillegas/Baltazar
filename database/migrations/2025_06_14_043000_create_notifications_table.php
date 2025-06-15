<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create the notifications table if it doesn't exist
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message');
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        } else {
            // If table exists, just add missing columns
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'title')) {
                    $table->string('title')->after('id');
                }
                if (!Schema::hasColumn('notifications', 'message')) {
                    $table->text('message')->after('title');
                }
                if (!Schema::hasColumn('notifications', 'sender_id')) {
                    $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade')->after('message');
                }
            });
        }

        Schema::create('notification_user', function (Blueprint $table) {
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->primary(['notification_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
    }
};
