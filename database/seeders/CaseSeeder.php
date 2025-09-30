<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CaseModel;
use App\Models\CaseUpdate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for assignment
        $socialWorkers = User::where('role', User::ROLE_SOCIAL_WORKER)->get();
        $policeOfficers = User::where('role', User::ROLE_POLICE_OFFICER)->get();

        if ($socialWorkers->isEmpty()) {
            $this->command->warn('No social workers found. Please run UserSeeder first.');
            return;
        }

        $cases = [
            [
                'abuse_type' => 'physical',
                'description' => 'Child reported with visible bruises on arms and legs. Neighbor witnessed physical altercation between parent and child.',
                'date_reported' => now()->subDays(15),
                'location' => '123 Main Street, Downtown',
                'status' => 'under_investigation',
                'priority' => 'high',
                'child_name' => 'Sarah Johnson',
                'child_dob' => now()->subYears(8)->subMonths(3),
                'child_gender' => 'female',
                'child_address' => '123 Main Street, Downtown, City 12345',
                'child_school' => 'Downtown Elementary School',
                'child_class' => '3rd Grade',
                'medical_conditions' => null,
                'injuries_description' => 'Multiple bruises on both arms, small cut on left cheek, appears to be healing.',
                'reporter_name' => 'Mary Williams',
                'reporter_relationship' => 'Neighbor',
                'reporter_phone' => '+1234567890',
                'reporter_address' => '125 Main Street, Downtown, City 12345',
                'reporter_email' => 'mary.williams@email.com',
                // Offender Information
                'offender_known' => true,
                'offender_name' => 'Mark Johnson',
                'offender_relationship' => 'parent',
                'offender_description' => 'Father, approximately 35 years old, history of alcohol abuse. Known to have anger management issues.',
            ],
            [
                'abuse_type' => 'neglect',
                'description' => 'Child frequently arrives at school without proper clothing, appears malnourished, and often falls asleep in class.',
                'date_reported' => now()->subDays(8),
                'location' => '456 Oak Avenue, Westside',
                'status' => 'assigned_to_police',
                'priority' => 'medium',
                'child_name' => 'Michael Chen',
                'child_dob' => null,
                'child_age' => 7,
                'child_gender' => 'male',
                'child_address' => '456 Oak Avenue, Westside, City 12345',
                'child_school' => 'Westside Primary School',
                'child_class' => '2nd Grade',
                'medical_conditions' => 'Mild asthma',
                'injuries_description' => null,
                'reporter_name' => 'Jennifer Martinez',
                'reporter_relationship' => 'Teacher',
                'reporter_phone' => '+1234567891',
                'reporter_address' => 'Westside Primary School, 789 School Lane, City 12345',
                'reporter_email' => 'j.martinez@westside.edu',
                // Offender Information
                'offender_known' => true,
                'offender_name' => 'Linda Chen',
                'offender_relationship' => 'parent',
                'offender_description' => 'Mother, struggling with substance abuse. Often leaves child unattended for long periods.',
            ],
            [
                'abuse_type' => 'emotional',
                'description' => 'Child exhibits signs of severe emotional distress, reports constant verbal abuse and threats from guardian.',
                'date_reported' => now()->subDays(3),
                'location' => '789 Pine Street, Eastside',
                'status' => 'reported',
                'priority' => 'critical',
                'child_name' => 'Emma Rodriguez',
                'child_dob' => now()->subYears(12)->subMonths(7),
                'child_gender' => 'female',
                'child_address' => '789 Pine Street, Eastside, City 12345',
                'child_school' => 'Eastside Middle School',
                'child_class' => '7th Grade',
                'medical_conditions' => 'Anxiety disorder, currently receiving counseling',
                'injuries_description' => null,
                'reporter_name' => 'Dr. Patricia Thompson',
                'reporter_relationship' => 'School Counselor',
                'reporter_phone' => '+1234567892',
                'reporter_address' => 'Eastside Middle School, 321 Education Blvd, City 12345',
                'reporter_email' => 'p.thompson@eastside.edu',
                // Offender Information
                'offender_known' => true,
                'offender_name' => 'Carlos Martinez',
                'offender_relationship' => 'step_parent',
                'offender_description' => 'Step-father, moved in 6 months ago. Child reports constant yelling and threats of physical harm.',
            ],
            [
                'abuse_type' => 'sexual',
                'description' => 'Child disclosed inappropriate touching by a family member. Requires immediate intervention and medical examination.',
                'date_reported' => now()->subDays(1),
                'location' => '321 Maple Drive, Northside',
                'status' => 'in_progress',
                'priority' => 'critical',
                'child_name' => 'David Wilson',
                'child_dob' => now()->subYears(10)->subMonths(2),
                'child_gender' => 'male',
                'child_address' => '321 Maple Drive, Northside, City 12345',
                'child_school' => 'Northside Elementary',
                'child_class' => '5th Grade',
                'medical_conditions' => null,
                'injuries_description' => 'Medical examination pending',
                'reporter_name' => 'Lisa Anderson',
                'reporter_relationship' => 'Aunt',
                'reporter_phone' => '+1234567893',
                'reporter_address' => '654 Cedar Lane, Northside, City 12345',
                'reporter_email' => 'lisa.anderson@email.com',
                // Offender Information
                'offender_known' => true,
                'offender_name' => 'Robert Wilson',
                'offender_relationship' => 'relative',
                'offender_description' => 'Uncle, lives in the same household. Child disclosed inappropriate touching during family visits.',
            ],
            [
                'abuse_type' => 'physical',
                'description' => 'Case resolved after family counseling and monitoring. Child is now in safe environment with relatives.',
                'date_reported' => now()->subDays(45),
                'location' => '987 Elm Street, Southside',
                'status' => 'resolved',
                'priority' => 'medium',
                'child_name' => 'Ashley Brown',
                'child_dob' => now()->subYears(6)->subMonths(9),
                'child_gender' => 'female',
                'child_address' => '987 Elm Street, Southside, City 12345',
                'child_school' => 'Southside Elementary',
                'child_class' => '1st Grade',
                'medical_conditions' => null,
                'injuries_description' => 'Minor bruising, fully healed',
                'reporter_name' => 'Robert Davis',
                'reporter_relationship' => 'Family Friend',
                'reporter_phone' => '+1234567894',
                'reporter_address' => '123 Friend Street, Southside, City 12345',
                'reporter_email' => null,
                // Offender Information
                'offender_known' => true,
                'offender_name' => 'James Brown',
                'offender_relationship' => 'parent',
                'offender_description' => 'Father, completed anger management program. Case resolved through family counseling and monitoring.',
            ],
            [
                'abuse_type' => 'neglect',
                'description' => 'Child left alone for extended periods. Parent struggling with substance abuse issues.',
                'date_reported' => now()->subDays(22),
                'location' => '555 Birch Road, Central',
                'status' => 'under_investigation',
                'priority' => 'high',
                'child_name' => 'Tyler Garcia',
                'child_dob' => null,
                'child_age' => 9,
                'child_gender' => 'male',
                'child_address' => '555 Birch Road, Central, City 12345',
                'child_school' => 'Central Elementary',
                'child_class' => '4th Grade',
                'medical_conditions' => 'ADHD, requires daily medication',
                'injuries_description' => null,
                'reporter_name' => 'Sandra Miller',
                'reporter_relationship' => 'Grandmother',
                'reporter_phone' => '+1234567895',
                'reporter_address' => '777 Senior Lane, Central, City 12345',
                'reporter_email' => 'sandra.miller@email.com',
                // Offender Information
                'offender_known' => false,
                'offender_name' => null,
                'offender_relationship' => null,
                'offender_description' => 'Parent identity unknown. Child found alone multiple times. Investigating current guardianship situation.',
            ],
            [
                'abuse_type' => 'physical',
                'description' => 'Child attacked by unknown individual at playground. Witnesses saw stranger approach and assault child.',
                'date_reported' => now()->subDays(5),
                'location' => 'Central Park Playground',
                'status' => 'assigned_to_police',
                'priority' => 'critical',
                'child_name' => 'Sophie Martinez',
                'child_dob' => now()->subYears(9)->subMonths(1),
                'child_gender' => 'female',
                'child_address' => '888 Park Avenue, Central, City 12345',
                'child_school' => 'Central Elementary',
                'child_class' => '4th Grade',
                'medical_conditions' => null,
                'injuries_description' => 'Bruising on face and arms, treated at hospital emergency room.',
                'reporter_name' => 'Maria Santos',
                'reporter_relationship' => 'Witness/Bystander',
                'reporter_phone' => '+1234567896',
                'reporter_address' => '999 Park Street, Central, City 12345',
                'reporter_email' => 'maria.santos@email.com',
                // Offender Information
                'offender_known' => false,
                'offender_name' => null,
                'offender_relationship' => 'stranger',
                'offender_description' => 'Unknown male, approximately 30-40 years old, medium build, wearing dark clothing. Fled scene immediately after incident.',
            ],
            [
                'abuse_type' => 'sexual',
                'description' => 'Child disclosed inappropriate behavior by teacher during after-school tutoring sessions.',
                'date_reported' => now()->subDays(12),
                'location' => 'Westside Middle School',
                'status' => 'in_progress',
                'priority' => 'critical',
                'child_name' => 'Alex Thompson',
                'child_dob' => now()->subYears(13)->subMonths(8),
                'child_gender' => 'male',
                'child_address' => '444 School District Road, Westside, City 12345',
                'child_school' => 'Westside Middle School',
                'child_class' => '8th Grade',
                'medical_conditions' => null,
                'injuries_description' => 'Psychological trauma, referred for counseling.',
                'reporter_name' => 'Janet Thompson',
                'reporter_relationship' => 'Parent',
                'reporter_phone' => '+1234567897',
                'reporter_address' => '444 School District Road, Westside, City 12345',
                'reporter_email' => 'janet.thompson@email.com',
                // Offender Information
                'offender_known' => true,
                'offender_name' => 'Mr. Richard Stevens',
                'offender_relationship' => 'teacher',
                'offender_description' => 'Math teacher, 45 years old, has been with school district for 8 years. Currently suspended pending investigation.',
            ],
        ];

        foreach ($cases as $index => $caseData) {
            // Assign to a random social worker
            $socialWorker = $socialWorkers->random();
            $caseData['social_worker_id'] = $socialWorker->id;

            // Assign police officer for some cases
            if (in_array($caseData['status'], ['assigned_to_police', 'in_progress']) && $policeOfficers->isNotEmpty()) {
                $caseData['police_officer_id'] = $policeOfficers->random()->id;
            }

            // Set closure information for resolved cases
            if ($caseData['status'] === 'resolved') {
                $caseData['closure_notes'] = 'Case successfully resolved through family intervention and ongoing monitoring. Child is now in a safe environment.';
                $caseData['closed_at'] = now()->subDays(5);
            }

            // Create the case
            $case = CaseModel::create($caseData);

            // Create initial case update
            CaseUpdate::create([
                'case_id' => $case->id,
                'user_id' => $socialWorker->id,
                'update_type' => 'case_created',
                'description' => 'Case created and registered in the system.',
            ]);

            // Add additional updates based on status
            $this->addCaseUpdates($case, $socialWorker, $policeOfficers);
        }

        $this->command->info('Created ' . count($cases) . ' test cases with offender information and updates.');
    }

    /**
     * Add realistic case updates based on case status
     */
    private function addCaseUpdates(CaseModel $case, User $socialWorker, $policeOfficers): void
    {
        $updates = [];

        switch ($case->status) {
            case 'under_investigation':
                $updates[] = [
                    'update_type' => 'status_changed',
                    'description' => 'Status changed from "reported" to "under_investigation". Initial assessment completed.',
                    'created_at' => $case->created_at->addHours(2),
                ];
                $updates[] = [
                    'update_type' => 'note_added',
                    'description' => 'Conducted initial interview with reporter. Gathering additional evidence and offender information.',
                    'created_at' => $case->created_at->addDays(1),
                ];
                break;

            case 'assigned_to_police':
                $updates[] = [
                    'update_type' => 'status_changed',
                    'description' => 'Status changed from "reported" to "under_investigation".',
                    'created_at' => $case->created_at->addHours(1),
                ];
                if ($policeOfficers->isNotEmpty()) {
                    $officer = $policeOfficers->random();
                    $updates[] = [
                        'update_type' => 'assigned_police',
                        'description' => "Case assigned to Police Officer: {$officer->name}. Requires immediate police intervention for offender investigation.",
                        'created_at' => $case->created_at->addHours(3),
                    ];
                    $updates[] = [
                        'update_type' => 'status_changed',
                        'description' => 'Status changed from "under_investigation" to "assigned_to_police".',
                        'created_at' => $case->created_at->addHours(3)->addMinutes(5),
                    ];
                }
                break;

            case 'in_progress':
                $updates[] = [
                    'update_type' => 'status_changed',
                    'description' => 'Status changed from "reported" to "under_investigation".',
                    'created_at' => $case->created_at->addMinutes(30),
                ];
                if ($policeOfficers->isNotEmpty()) {
                    $officer = $policeOfficers->random();
                    $updates[] = [
                        'update_type' => 'assigned_police',
                        'description' => "Case assigned to Police Officer: {$officer->name}. Critical case requiring immediate attention and offender apprehension.",
                        'created_at' => $case->created_at->addHours(1),
                    ];
                    $updates[] = [
                        'update_type' => 'status_changed',
                        'description' => 'Status changed from "under_investigation" to "assigned_to_police".',
                        'created_at' => $case->created_at->addHours(1)->addMinutes(5),
                    ];
                    $updates[] = [
                        'update_type' => 'status_changed',
                        'description' => 'Status changed from "assigned_to_police" to "in_progress". Active investigation and offender tracking underway.',
                        'created_at' => $case->created_at->addDays(1),
                    ];
                }
                break;

            case 'resolved':
                $updates[] = [
                    'update_type' => 'status_changed',
                    'description' => 'Status changed from "reported" to "under_investigation".',
                    'created_at' => $case->created_at->addHours(2),
                ];
                $updates[] = [
                    'update_type' => 'note_added',
                    'description' => 'Family counseling sessions initiated. Monitoring child welfare and offender compliance closely.',
                    'created_at' => $case->created_at->addDays(5),
                ];
                $updates[] = [
                    'update_type' => 'note_added',
                    'description' => 'Child placed with safe relatives. Offender completed required programs. Regular check-ins scheduled.',
                    'created_at' => $case->created_at->addDays(20),
                ];
                $updates[] = [
                    'update_type' => 'status_changed',
                    'description' => 'Status changed from "under_investigation" to "resolved". Case successfully closed with offender accountability measures in place.',
                    'created_at' => $case->created_at->addDays(40),
                ];
                break;
        }

        // Create the updates
        foreach ($updates as $updateData) {
            CaseUpdate::create([
                'case_id' => $case->id,
                'user_id' => $socialWorker->id,
                'update_type' => $updateData['update_type'],
                'description' => $updateData['description'],
                'created_at' => $updateData['created_at'],
                'updated_at' => $updateData['created_at'],
            ]);
        }
    }
}
