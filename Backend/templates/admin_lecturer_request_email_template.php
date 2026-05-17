<?php
namespace Backend\Templates;

class AdminLecturerRequestEmailTemplate {
    public function build($data) {
        $lecturerName = htmlspecialchars((string)($data['lecturer_name'] ?? 'Lecturer'), ENT_QUOTES, 'UTF-8');
        $subjectName = htmlspecialchars((string)($data['subject'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $subjectCode = htmlspecialchars((string)($data['subject_id'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $year = htmlspecialchars((string)($data['year'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $groupName = htmlspecialchars((string)($data['group_name'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $day = htmlspecialchars((string)($data['column_heading'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $timeSlot = htmlspecialchars((string)($data['time_slot_label'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars((string)($data['date'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $requestMessage = nl2br(htmlspecialchars((string)($data['lecturer_request'] ?? '-'), ENT_QUOTES, 'UTF-8'));

        return [
            'subject' => sprintf('New Lecturer Request: %s on %s', $data['subject_id'] ?? 'Subject', $data['date'] ?? 'date'),
            'html' => "
                <div style=\"font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;\">
                    <h2 style=\"margin-bottom: 12px; color: #111827;\">New Lecturer Request</h2>
                    <p>A lecturer has submitted a new lecture request and it is waiting for admin review.</p>
                    <table style=\"border-collapse: collapse; width: 100%; max-width: 640px;\">
                        <tr><td style=\"padding: 8px; font-weight: 700; width: 180px;\">Lecturer</td><td style=\"padding: 8px;\">{$lecturerName}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Subject</td><td style=\"padding: 8px;\">{$subjectName}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Subject Code</td><td style=\"padding: 8px;\">{$subjectCode}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Year</td><td style=\"padding: 8px;\">{$year}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Group</td><td style=\"padding: 8px;\">{$groupName}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Day</td><td style=\"padding: 8px;\">{$day}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Time Slot</td><td style=\"padding: 8px;\">{$timeSlot}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Date</td><td style=\"padding: 8px;\">{$date}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700; vertical-align: top;\">Request</td><td style=\"padding: 8px;\">{$requestMessage}</td></tr>
                    </table>
                </div>
            ",
            'text' => "New Lecturer Request\n"
                . "Lecturer: " . ($data['lecturer_name'] ?? '-') . "\n"
                . "Subject: " . ($data['subject'] ?? '-') . "\n"
                . "Subject Code: " . ($data['subject_id'] ?? '-') . "\n"
                . "Year: " . ($data['year'] ?? '-') . "\n"
                . "Group: " . ($data['group_name'] ?? '-') . "\n"
                . "Day: " . ($data['column_heading'] ?? '-') . "\n"
                . "Time Slot: " . ($data['time_slot_label'] ?? '-') . "\n"
                . "Date: " . ($data['date'] ?? '-') . "\n"
                . "Request: " . ($data['lecturer_request'] ?? '-') . "\n",
        ];
    }
}
?>
