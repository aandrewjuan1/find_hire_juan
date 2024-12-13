<?php
require_once("./Core/Database.php");

class JobPost extends Database {
    // Store a new job post
    public function store($title, $description, $createdBy) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("INSERT INTO JobPosts (Title, Description, CreatedBy) VALUES (:title, :description, :createdBy)");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':createdBy' => $createdBy
            ]);
            return ["success" => true, "message" => "Job post created successfully."];
        } catch (PDOException $e) {
            return ["success" => false, "errors" => ["Database error: " . $e->getMessage()]];
        }
    }

    // Fetch all job posts
    public function getAll() {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->query("SELECT jp.JobPostID, jp.Title, jp.Description, jp.CreatedAt, u.Username as CreatedBy 
                                FROM JobPosts jp
                                JOIN Users u ON jp.CreatedBy = u.UserID
                                ORDER BY jp.CreatedAt DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["success" => false, "errors" => ["Database error: " . $e->getMessage()]];
        }
    }

    public function deleteJobPost($jobPostId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("DELETE FROM JobPosts WHERE JobPostID = :jobPostId");
            $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true; // Success
            } else {
                error_log("SQL Execution failed for JobPostID: " . $jobPostId);
                return false; // Failure
            }
        } catch (PDOException $e) {
            error_log("Error deleting job post: " . $e->getMessage());
            return false; // Failure
        }
    }
    

    // Fetch details of a specific job post by ID
    public function getJobPostDetails($jobPostId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT * FROM JobPosts WHERE JobPostID = :jobPostId");
            $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching job post details: " . $e->getMessage());
            return null;
        }
    }

    // Apply for a job post
    public function applyJob($jobPostId, $applicantId, $coverLetter, $resumeFilePath) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("INSERT INTO Applications (JobPostID, ApplicantID, CoverLetter, ResumePath) 
                                   VALUES (:jobPostId, :applicantId, :coverLetter, :resumeFilePath)");
            $stmt->execute([
                ':jobPostId' => $jobPostId,
                ':applicantId' => $applicantId,
                ':coverLetter' => $coverLetter,
                ':resumeFilePath' => $resumeFilePath,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error applying for job: " . $e->getMessage());
            return false;
        }
    }

    public function getJobPostById($jobPostId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT Title FROM JobPosts WHERE JobPostID = :jobPostId");
            $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching job post: " . $e->getMessage());
            return ['Title' => 'Unknown'];
        }
    }
    

}
