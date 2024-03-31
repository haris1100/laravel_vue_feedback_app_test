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
        Schema::create('feedback_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedback_id');
            $table->unsignedBigInteger('commented_by');
            $table->text('comments');

            $table->timestamps();
            $table->foreign('commented_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')->on('feedback')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_comments');
    }
};
