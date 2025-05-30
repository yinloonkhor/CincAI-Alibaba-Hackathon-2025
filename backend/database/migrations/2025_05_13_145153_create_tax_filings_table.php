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
        Schema::create('tax_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('period_id')->constrained('tax_filing_periods')->onDelete('cascade');
            $table->date('filing_date')->nullable();
            $table->decimal('total_income', 12, 2)->nullable();
            $table->decimal('total_expenses', 12, 2)->nullable();
            $table->decimal('total_deductions', 12, 2)->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable();
            $table->string('status')->default('Draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_filings');
    }
};
