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
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Case Details
            $table->string('case_number')->unique();
            $table->enum('abuse_type', [
                'physical',
                'sexual',
                'emotional',
                'neglect',
                'psychological',
                'financial',
                'other'
            ]);
            $table->text('description');
            $table->date('date_reported');
            $table->string('location');
            $table->enum('status', [
                'reported',
                'under_investigation',
                'assigned_to_police',
                'in_progress',
                'resolved',
                'closed',
                'transferred'
            ])->default('reported');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Child Details
            $table->string('child_name');
            $table->date('child_dob')->nullable();
            $table->integer('child_age')->nullable();
            $table->enum('child_gender', ['male', 'female', 'other']);
            $table->text('child_address');
            $table->string('child_school')->nullable();
            $table->string('child_class')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('injuries_description')->nullable();

            // Reporter/Guardian Details
            $table->string('reporter_name');
            $table->string('reporter_relationship');
            $table->string('reporter_phone');
            $table->text('reporter_address');
            $table->string('reporter_email')->nullable();

            // System Fields
            $table->uuid('social_worker_id');
            $table->uuid('police_officer_id')->nullable();
            $table->timestamp('date_entered')->useCurrent();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
            $table->text('investigation_notes')->nullable();
            $table->text('closure_notes')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('social_worker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('police_officer_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('case_number');
            $table->index('status');
            $table->index('abuse_type');
            $table->index('priority');
            $table->index('date_reported');
            $table->index('social_worker_id');
            $table->index('police_officer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
