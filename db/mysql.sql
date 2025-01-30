-- Database Name 
sibsagar_university
-- student table
student
CREATE TABLE student (
    id INT AUTO_INCREMENT PRIMARY KEY,         
    roll_no VARCHAR(50) NOT NULL UNIQUE,  -- Unique constraint
    name VARCHAR(255) NOT NULL,                
    department INT NOT NULL,                   
    semester INT NOT NULL,                     
    course INT NOT NULL                        
);
CREATE TABLE student (
    roll_no VARCHAR(50) PRIMARY KEY,  -- roll_no is now the primary key
    name VARCHAR(255) NOT NULL,                
    department INT NOT NULL,                   
    semester INT NOT NULL,                     
    course INT NOT NULL                        
);

-- CREATE DATABASE department_db;

-- USE department_db;

CREATE TABLE departments (
    department_id VARCHAR(50) PRIMARY KEY,  -- department_id is the primary key
    department_name VARCHAR(100) NOT NULL   -- department_name is required
);
