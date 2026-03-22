USE timetable_system;

CREATE TABLE IF NOT EXISTS lecturer_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecturer_id INT NOT NULL,
    subject_id VARCHAR(200) NOT NULL,
    year_id INT NOT NULL,
    timetable_time_slot_id INT NOT NULL,
    timetable_column_heading_id INT NOT NULL,
    lecturer_request TEXT NOT NULL,
    send_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lecturer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES practical_subjects(subject_cord) ON DELETE CASCADE,
    FOREIGN KEY (year_id) REFERENCES years(id) ON DELETE CASCADE,
    FOREIGN KEY (timetable_time_slot_id) REFERENCES timetable_time_slots(id) ON DELETE CASCADE,
    FOREIGN KEY (timetable_column_heading_id) REFERENCES timetable_column_headings(id) ON DELETE CASCADE
);
