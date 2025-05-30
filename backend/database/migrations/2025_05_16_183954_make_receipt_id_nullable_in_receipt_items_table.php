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
        Schema::table('receipt_items', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['receipt_id']);
            
            // Change the column to be nullable
            $table->unsignedBigInteger('receipt_id')->nullable()->change();
            
            // Add the foreign key constraint back, but with onDelete('set null')
            $table->foreign('receipt_id')
                  ->references('id')
                  ->on('receipts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_items', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['receipt_id']);
            
            // Change the column back to non-nullable
            $table->unsignedBigInteger('receipt_id')->nullable(false)->change();
            
            // Add the foreign key constraint back with the original settings
            $table->foreign('receipt_id')
                  ->references('id')
                  ->on('receipts');
        });
    }
};
