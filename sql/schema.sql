CREATE TABLE Roles (
    RoleID INT PRIMARY KEY AUTO_INCREMENT,
    RoleName VARCHAR(50) NOT NULL UNIQUE 
);

CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL, 
    Email VARCHAR(100) NOT NULL UNIQUE,
    RoleID INT NOT NULL,
    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
);

CREATE TABLE JobPosts (
    JobPostID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(100) NOT NULL,
    Description TEXT NOT NULL,
    CreatedBy INT NOT NULL, 
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CreatedBy) REFERENCES Users(UserID)
);

CREATE TABLE Applications (
    ApplicationID INT PRIMARY KEY AUTO_INCREMENT,
    JobPostID INT NOT NULL,
    ApplicantID INT NOT NULL, 
    CoverLetter TEXT NOT NULL, 
    ResumePath VARCHAR(255) NOT NULL, 
    Status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
    UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (JobPostID) REFERENCES JobPosts(JobPostID),
    FOREIGN KEY (ApplicantID) REFERENCES Users(UserID)
);

CREATE TABLE Messages (
    MessageID INT PRIMARY KEY AUTO_INCREMENT,
    SenderID INT NOT NULL, 
    ReceiverID INT NOT NULL, 
    Content TEXT NOT NULL, 
    SentAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SenderID) REFERENCES Users(UserID),
    FOREIGN KEY (ReceiverID) REFERENCES Users(UserID)
);

-- Insert Roles Data
INSERT INTO Roles (RoleName) VALUES 
('Applicant'), 
('HR');

-- Insert Users Data
INSERT INTO Users (Username, PasswordHash, Email, RoleID) VALUES
('john_doe', 'hashed_password_1', 'john.doe@example.com', 1),  -- Applicant
('jane_smith', 'hashed_password_2', 'jane.smith@example.com', 1),  -- Applicant
('hr_manager', 'hashed_password_3', 'hr.manager@example.com', 2);  -- HR

-- Insert JobPosts Data
INSERT INTO JobPosts (Title, Description, CreatedBy) VALUES
('Software Engineer', 'We are looking for a software engineer with experience in web development.', 3),  -- Created by HR Manager
('Data Analyst', 'Seeking a data analyst to help with processing large datasets and reports.', 3);  -- Created by HR Manager

-- Insert Applications Data
INSERT INTO Applications (JobPostID, ApplicantID, CoverLetter, ResumePath, Status) VALUES
(1, 1, 'I am passionate about software development and eager to contribute to your team.', '/resumes/john_doe_resume.pdf', 'Pending'),
(2, 2, 'I have a strong background in data analysis and enjoy working with large datasets.', '/resumes/jane_smith_resume.pdf', 'Accepted');

-- Insert Messages Data
INSERT INTO Messages (SenderID, ReceiverID, Content) VALUES
(1, 3, 'Hi, I am interested in the Software Engineer position. Could you provide more details about the role?'),  -- From Applicant to HR
(2, 3, 'I wanted to follow up on my application for the Data Analyst position. Looking forward to hearing back.');  -- From Applicant to HR


