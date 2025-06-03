USE school;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE grades;
TRUNCATE TABLE class_lesson_teachers;
TRUNCATE TABLE class_students;
TRUNCATE TABLE lessons;
TRUNCATE TABLE classes;
TRUNCATE TABLE users;
TRUNCATE TABLE mail;
TRUNCATE TABLE detentions;
TRUNCATE TABLE diaries;
SET FOREIGN_KEY_CHECKS = 1;

-- Insert users

-- Insert classes
INSERT INTO classes (class_name) VALUES
('Class A'), ('Class B');

-- Assign students to classes
INSERT INTO class_students (class_id, user_id) VALUES
(1, 1), -- Dan, Eve in Class A; Frank in Class B

-- Insert lessons
INSERT INTO lessons (lesson_name, description) VALUES
('Math', 'Basic and advanced math'), 
('English', 'Reading, writing, speaking'), 
('Biology', 'Introduction to Biology');

-- Assign teachers to lessons in classes
INSERT INTO class_lesson_teachers (class_id, lesson_id, teacher_id) VALUES
(1, 1, 2), -- Math to Class A by Bob
(1, 2, 3), -- English to Class A by Clara
(2, 1, 2), -- Math to Class B by Bob
(2, 3, 2); -- Biology to Class B by Bob

-- Insert grades (for multiple months)
INSERT INTO grades (student_id, class_id, lesson_id, teacher_id, grade_value, grade_date, comments) VALUES
(4, 1, 1, 2, 85.5, '2025-03-15', 'Good job'),  -- Dan Math March
(4, 1, 2, 3, 91.0, '2025-04-02', 'Excellent'), -- Dan English April
(5, 1, 1, 2, 73.0, '2025-03-22', 'Needs work'), -- Eve Math March
(5, 1, 2, 3, 89.0, '2025-04-10', 'Well done'), -- Eve English April
(6, 2, 1, 2, 78.5, '2025-03-12', 'Nice effort'), -- Frank Math March
(6, 2, 3, 2, 83.0, '2025-05-01', 'Interesting answer'); -- Frank Biology May

-- Insert some diaries
INSERT INTO diaries (class_id, lesson_id, teacher_id, diary_date, slot_number) VALUES
(1, 1, 2, '2025-03-15', 1),
(1, 2, 3, '2025-04-02', 2),
(2, 3, 2, '2025-05-01', 3);

-- Insert a few detentions
INSERT INTO detentions (student_id, teacher_id, reason, detention_date) VALUES
(4, 2, 'Late homework', '2025-03-17'),
(6, 2, 'Talking during class', '2025-05-03');

-- Internal messages (mail)
INSERT INTO mail (sender_id, receiver_id, subject, body) VALUES
(2, 4, 'Homework Reminder', 'Please submit your math homework by Friday.'),
(3, 5, 'English Essay', 'Don’t forget the essay due next week.');

-- Done
