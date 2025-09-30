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
        Schema::table('case_updates', function (Blueprint $table) {
            // Add metadata column if it doesn't exist
            if (!Schema::hasColumn('case_updates', 'metadata')) {
                $table->json('metadata')->nullable()->after('update_type');
            }

            // Add content column if it doesn't exist
            if (!Schema::hasColumn('case_updates', 'content')) {
                $table->text('content')->nullable()->after('metadata');
            }

            // Make description nullable if it exists and is required
            if (Schema::hasColumn('case_updates', 'description')) {
                $table->text('description')->nullable()->change();
            }

            // Add other useful columns if they don't exist
            if (!Schema::hasColumn('case_updates', 'is_internal')) {
                $table->boolean('is_internal')->default(false)->after('content');
            }

            if (!Schema::hasColumn('case_updates', 'priority')) {
                $table->string('priority')->nullable()->after('is_internal');
            }

            if (!Schema::hasColumn('case_updates', 'status')) {
                $table->string('status')->nullable()->after('priority');
            }

            // Ensure update_type exists and has a default
            if (!Schema::hasColumn('case_updates', 'update_type')) {
                $table->string('update_type')->default('other')->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_updates', function (Blueprint $table) {
            // Remove the columns we added
            $columnsToRemove = ['metadata', 'content', 'is_internal', 'priority', 'status'];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('case_updates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
