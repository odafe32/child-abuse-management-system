<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            // Essential offender information
            $table->string('offender_name')->nullable();
            $table->string('offender_relationship')->nullable(); // parent, relative, stranger, etc.
            $table->text('offender_description')->nullable();
            $table->boolean('offender_known')->default(false); // known vs unknown offender
        });
    }

    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn([
                'offender_name',
                'offender_relationship',
                'offender_description',
                'offender_known'
            ]);
        });
    }
};
