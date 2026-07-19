<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postings', function (Blueprint $table) {
            $table->id();
            $table->string('source');               // larajobs|hn|wwr|remoteok|manual
            $table->string('external_id');          // stable ID within source (url, guid, comment id)
            $table->string('company')->nullable();
            $table->string('title');
            $table->string('url', 2048);
            $table->string('location')->nullable();
            $table->boolean('remote')->default(false);
            $table->string('salary')->nullable();
            $table->text('description');
            $table->json('tags')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['source', 'external_id']);
            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postings');
    }
};
