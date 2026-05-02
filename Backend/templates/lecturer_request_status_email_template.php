<?php
namespace Backend\Templates;

class LecturerRequestStatusEmailTemplate {
    public function build($data) {
        $lecturerName = htmlspecialchars((string)($data['lecturer_name'] ?? 'Lecturer'), ENT_QUOTES, 'UTF-8');
        $subjectName = htmlspecialchars((string)($data['subject'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $subjectCode = htmlspecialchars((string)($data['subject_id'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $year = htmlspecialchars((string)($data['year'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $groupName = htmlspecialchars((string)($data['group_name'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $day = htmlspecialchars((string)($data['column_heading'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $timeSlot = htmlspecialchars((string)($data['time_slot_label'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars((string)($data['date'] ?? '-'), ENT_QUOTES, 'UTF-8');
        $labName = htmlspecialchars((string)($data['lab_name'] ?? 'Not assigned'), ENT_QUOTES, 'UTF-8');
        $requestMessage = nl2br(htmlspecialchars((string)($data['lecturer_request'] ?? '-'), ENT_QUOTES, 'UTF-8'));
        $adminMessage = nl2br(htmlspecialchars((string)($data['admin_message'] ?? '-'), ENT_QUOTES, 'UTF-8'));
        $isConfirmed = ($data['action'] ?? '') === 'confirmed';
        $title = $isConfirmed ? 'Lecture Request Confirmed' : 'Lecture Request Canceled';
        $statusText = strtoupper((string)($data['action'] ?? 'updated'));
        $statusColor = $isConfirmed ? '#166534' : '#991b1b';
        $statusBg = $isConfirmed ? '#dcfce7' : '#fee2e2';
        $labRow = $isConfirmed
            ? "<tr><td style=\"padding: 8px; font-weight: 700;\">Lab</td><td style=\"padding: 8px;\">{$labName}</td></tr>"
            : '';
        $adminMessageRow = !$isConfirmed
            ? "<tr><td style=\"padding: 8px; font-weight: 700; vertical-align: top;\">Cancel Reason</td><td style=\"padding: 8px;\">{$adminMessage}</td></tr>"
            : '';

        return [
            'subject' => sprintf('Lecture Request %s: %s', ucfirst((string)($data['action'] ?? 'updated')), $data['subject_id'] ?? 'Subject'),
            'html' => "
                <div style=\"font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;\">
                    <h2 style=\"margin-bottom: 12px; color: #111827;\">{$title}</h2>
                    <p>Hello {$lecturerName}, your lecture request has been updated by an administrator.</p>
                    <p>
                        <span style=\"display: inline-block; padding: 6px 12px; border-radius: 999px; background: {$statusBg}; color: {$statusColor}; font-weight: 700;\">
                            {$statusText}
                        </span>
                    </p>
                    <table style=\"border-collapse: collapse; width: 100%; max-width: 640px;\">
                        <tr><td style=\"padding: 8px; font-weight: 700; width: 180px;\">Subject</td><td style=\"padding: 8px;\">{$subjectName}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Subject Code</td><td style=\"padding: 8px;\">{$subjectCode}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Year</td><td style=\"padding: 8px;\">{$year}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Group</td><td style=\"padding: 8px;\">{$groupName}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Day</td><td style=\"padding: 8px;\">{$day}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Time Slot</td><td style=\"padding: 8px;\">{$timeSlot}</td></tr>
                        <tr><td style=\"padding: 8px; font-weight: 700;\">Date</td><td style=\"padding: 8px;\">{$date}</td></tr>
                        {$labRow}
                        {$adminMessageRow}
                        <tr><td style=\"padding: 8px; font-weight: 700; vertical-align: top;\">Request</td><td style=\"padding: 8px;\">{$requestMessage}</td></tr>
                    </table>
                </div>
            ",
            'text' => "{$title}\n"
                . "Status: " . strtoupper((string)($data['action'] ?? 'updated')) . "\n"
                . "Subject: " . ($data['subject'] ?? '-') . "\n"
                . "Subject Code: " . ($data['subject_id'] ?? '-') . "\n"
                . "Year: " . ($data['year'] ?? '-') . "\n"
                . "Group: " . ($data['group_name'] ?? '-') . "\n"
                . "Day: " . ($data['column_heading'] ?? '-') . "\n"
                . "Time Slot: " . ($data['time_slot_label'] ?? '-') . "\n"
                . "Date: " . ($data['date'] ?? '-') . "\n"
                . ($isConfirmed ? "Lab: " . ($data['lab_name'] ?? 'Not assigned') . "\n" : '')
                . (!$isConfirmed ? "Cancel Reason: " . ($data['admin_message'] ?? '-') . "\n" : '')
                . "Request: " . ($data['lecturer_request'] ?? '-') . "\n",
        ];
    }
}
?>
