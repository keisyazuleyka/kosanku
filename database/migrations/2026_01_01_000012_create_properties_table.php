<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type', 20)->index();
            $table->string('city')->index();
            $table->string('district')->nullable();
            $table->text('address');
            $table->unsignedInteger('monthly_price');
            $table->unsignedInteger('deposit')->default(0);
            $table->text('additional_fees')->nullable();
            $table->text('rules')->nullable();
            $table->unsignedInteger('rooms_total')->default(1);
            $table->unsignedInteger('rooms_available')->default(1);
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->text('rejection_reason')->nullable();
            $table->text('revision_request')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->index(['city', 'status']);
            $table->index(['monthly_price', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
