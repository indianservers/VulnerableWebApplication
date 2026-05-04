<?php
function labChallenges(): array {
    return array(
        array(
            'title' => 'Curious Student',
            'persona' => 'Student',
            'difficulty' => 'Beginner',
            'attack' => 'idor-profile-access',
            'mission' => 'You are logged in as a student and want to know whether another student profile is exposed.',
            'win' => 'Capture another student name, ID number, or family field.'
        ),
        array(
            'title' => 'Careless Content Admin',
            'persona' => 'Admin',
            'difficulty' => 'Intermediate',
            'attack' => 'csrf-admin-delete',
            'mission' => 'You are reviewing whether admin actions can be triggered from links instead of protected forms.',
            'win' => 'Document a state-changing GET action and the missing token.'
        ),
        array(
            'title' => 'Grade Integrity Review',
            'persona' => 'Teacher',
            'difficulty' => 'Intermediate',
            'attack' => 'grade-parameter-tamper',
            'mission' => 'You are testing whether marks can be changed outside the intended grading workflow.',
            'win' => 'Capture before-and-after grade evidence.'
        ),
        array(
            'title' => 'Data Extraction Drill',
            'persona' => 'External Tester',
            'difficulty' => 'Advanced',
            'attack' => 'union-data-extraction',
            'mission' => 'You have found injectable search and want to prove impact without damaging data.',
            'win' => 'Map user-table fields into search result columns.'
        ),
        array(
            'title' => 'Configuration Review Sprint',
            'persona' => 'Security Engineer',
            'difficulty' => 'Advanced',
            'attack' => 'cors-misconfiguration',
            'mission' => 'You are reviewing headers and browser-side trust boundaries for sensitive school data.',
            'win' => 'Write the risky header combination and a strict replacement policy.'
        )
    );
}
?>
