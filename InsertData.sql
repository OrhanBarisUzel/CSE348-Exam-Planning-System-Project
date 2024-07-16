CREATE DATABASE examsystemplanning;

USE examsystemplanning;

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    role VARCHAR(50),
    departmentID INT,
    FacultyID INT,
    assistantID INT,
    username VARCHAR(50),
    password VARCHAR(255)
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    FacultyID INT
);

CREATE TABLE faculties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    departmentID INT,
    FacultyID INT,
    timeSlot VARCHAR(255)
);

CREATE TABLE Exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    courseID INT,
    examName VARCHAR(100),
    examDate DATE,
    examTime TIME,
    numClasses INT
);

CREATE TABLE Schedule (
    scheduleID INT AUTO_INCREMENT PRIMARY KEY,
    assistantID INT,  
    monday VARCHAR(255),
    tuesday VARCHAR(255),
    wednesday VARCHAR(255),
    thursday VARCHAR(255),
    friday VARCHAR(255),
    timeSlot VARCHAR(50),
    FOREIGN KEY (assistantID) REFERENCES employees(id) 
);

CREATE TABLE AssistantScore (
    id INT AUTO_INCREMENT PRIMARY KEY,
    examID INT,
    assistantID INT,
    score INT DEFAULT 0
);

INSERT INTO faculties (name) VALUES 
('Engineering'), 
('Science'),
('Economics'),
('Law'),
('Medical');

INSERT INTO departments (name, FacultyID) VALUES 
('Computer Engineering', 1), 
('Electrical Engineering', 1),
('Physics', 2),
('Microeconomics', 3),
('Criminal Law', 4);  

INSERT INTO employees (name, role, departmentID, FacultyID, assistantID, username, password) VALUES
('Gulsah Gokhan Gokcek', 'assistant', 1, 1, 1, 'gulsah', '123'),
('Burcu Selcuk', 'assistant', 1, 1, 2, 'burcu', '123'),
('Ahmet Yilmaz', 'secretary', 1, 1, 0, 'ahmet', '123'),
('Yucel Uzel', 'dean', 1, 1, 0, 'yucel', '123'),
('Mert Ertas', 'Head of department', 1, 1, 0, 'mert','123'),
('Zeynep Aslan', 'secretary', 1, 1, 0, 'zeynep','123'),
('Selin Avci', 'Head of secretary', 1, 1, 0, 'selin','123'),
('Can Bebe', 'assistant', 1, 1, 3, 'can', '123');

INSERT INTO courses (name, departmentID, FacultyID, timeSlot) VALUES 
('CSE101', 1, 1, 'Monday: 9.30 - 11.00'),
('CSE114', 1, 1, 'Monday: 12.00 - 13.30'),
('CSE221', 1, 1, 'Wednesday: 11.30- 14.00'),
('CSE311', 1, 1, 'Tuesday: 13.00- 14.00');

