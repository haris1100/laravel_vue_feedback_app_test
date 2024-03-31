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

        $categories = new \App\Http\Controllers\Worker();

        Schema::create('feedback', function (Blueprint $table) use ($categories) {
            $table->id();
            $table->string('title',100);
            $table->text('description');
            $table->enum('category',$categories->allowed_categories);
            $table->integer('comments_count')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
