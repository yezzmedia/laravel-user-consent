<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_decisions', function (Blueprint $table): void {
            $table->id();
            $table->string('category_slug');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_token', 64)->nullable()->index();
            $table->boolean('granted');
            $table->timestamp('consented_at');
            $table->unsignedInteger('version');
            $table->timestamps();

            $table->index(['user_id', 'category_slug']);
            $table->index(['guest_token', 'category_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_decisions');
    }
};
