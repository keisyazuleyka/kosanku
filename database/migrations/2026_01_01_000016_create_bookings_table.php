<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->unsignedTinyInteger('duration_months');
            $table->date('end_date');
            $table->unsignedInteger('monthly_price_snapshot');
            $table->unsignedInteger('deposit_snapshot');
            $table->unsignedInteger('total_estimate');
            $table->unsignedInteger('rooms_requested')->default(1);
            $table->string('status', 20)->default('pending')->index();
            $table->text('tenant_notes')->nullable();
            $table->text('owner_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->boolean('inventory_held')->default(false);
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
