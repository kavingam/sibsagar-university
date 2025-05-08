
-- OKKK
CREATE TABLE subject_info (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    department_name VARCHAR(100) NOT NULL,
    course INT NOT NULL,
    semester INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    subject_code VARCHAR(50) NOT NULL
    -- UNIQUE(subject_code)
);



CREATE TABLE subject_info (
    department_id INT NOT NULL,
    semester INT NOT NULL,
    course INT NOT NULL,
    subject_title VARCHAR(255) NOT NULL,
    subject_code VARCHAR(50) NOT NULL UNIQUE
);

ALTER TABLE subject_info ADD UNIQUE(subject_code);