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
        Schema::table('receipts', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['vendor_id']);
            
            // Change the column back to non-nullable
            $table->unsignedBigInteger('vendor_id')->nullable(false)->change();
            
            // Add the foreign key constraint back with the original settings
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            //
        });
    }
};
