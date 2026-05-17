<?php

function generatePassword(): string {
    return rtrim(strtr(base64_encode(random_bytes(12)), '+/', '-_'), '=');
}

$adminPw    = (string)($_ENV['SEED_ADMIN_PASSWORD']    ?? '');
$lecturerPw = (string)($_ENV['SEED_LECTURER_PASSWORD'] ?? '');

if ($adminPw === '')    { $adminPw    = generatePassword(); }
if ($lecturerPw === '') { $lecturerPw = generatePassword(); }

return [
    [
        'initials'          => 'A.K.',
        'initials_stand_for'=> 'Akmina',
        'first_name'        => 'Lahiru',
        'last_name'         => 'Akmina',
        'honorifics'        => 'Mr',
        'nic'               => '199912345678',
        'email'             => 'admin@laboratory.local',
        'mobile_number'     => '0712345678',
        'password'          => $adminPw,
        'role'              => 'admin',
        'created_by'        => 'seed-script',
        'updated_by'        => 'seed-script',
    ],
    [
        'initials'          => 'S.P.',
        'initials_stand_for'=> 'Sample Person',
        'first_name'        => 'Sample',
        'last_name'         => 'Lecturer',
        'honorifics'        => 'Ms',
        'nic'               => '200012345678',
        'email'             => 'lecturer@laboratory.local',
        'mobile_number'     => '0771234567',
        'password'          => $lecturerPw,
        'role'              => 'lecturer',
        'created_by'        => 'seed-script',
        'updated_by'        => 'seed-script',
    ],
];
