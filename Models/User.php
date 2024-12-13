<?php

require_once("./Core/Database.php");

class User extends Database {

    public function register($username, $email, $password, $repeatPassword, $roleID) {
        $errors = [];
    
        // Validate input
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required.";
        }
        if (empty($password) || strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if ($password !== $repeatPassword) {
            $errors[] = "Passwords do not match.";
        }
    
        // Return errors if any
        if (!empty($errors)) {
            return ["success" => false, "errors" => $errors];
        }
    
        try {
            $dbh = $this->connect();
    
            // Check if username or email already exists
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE Username = :username OR Email = :email");
            $stmt->execute([':username' => $username, ':email' => $email]);
    
            if ($stmt->rowCount() > 0) {
                return ["success" => false, "errors" => ["Username or email already exists."]];
            }
    
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
            // Insert new user
            $stmt = $dbh->prepare("INSERT INTO Users (Username, PasswordHash, Email, RoleID) VALUES (:username, :password, :email, :roleID)");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':email' => $email,
                ':roleID' => $roleID
            ]);
    
            return ["success" => true, "message" => "User registered successfully."];
        } catch (PDOException $e) {
            return ["success" => false, "errors" => ["Database error: " . $e->getMessage()]];
        }
    }

    public function getApplicants() {
        try {
            $dbh = $this->connect();
            
            // SQL query to fetch users with RoleID = 1 (applicants)
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE RoleID = 1");
            $stmt->execute();
            
            // Return the fetched applicants as an array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching applicants: " . $e->getMessage());
            return [];
        }
    }

    public function getHRUsers() {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE RoleID = 2");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching HR users: " . $e->getMessage());
            return [];
        }
    }

    public function getHRs() {
        try {
            $dbh = $this->connect();
            
            // SQL query to fetch users with RoleID = 2 (HR users)
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE RoleID = 2");
            $stmt->execute();
            
            // Return the fetched HR users as an array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching HR users: " . $e->getMessage());
            return [];
        }
    }
    

    // Log in an existing user
    public function login($usernameOrEmail, $password) {
        $errors = [];

        // Validate input
        if (empty($usernameOrEmail)) {
            $errors[] = "Username or email is required.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }

        // Return errors if any
        if (!empty($errors)) {
            return ["success" => false, "errors" => $errors];
        }

        try {
            $dbh = $this->connect();

            // Find user by username or email
            $stmt = $dbh->prepare("SELECT * FROM Users WHERE Username = :usernameOrEmail OR Email = :usernameOrEmail");
            $stmt->execute([':usernameOrEmail' => $usernameOrEmail]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['PasswordHash'])) {
                return ["success" => false, "errors" => ["Invalid credentials."]];
            }

            // Start session and set user data
            session_start();
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['RoleID'];

            return ["success" => true, "message" => "Logged in successfully."];
        } catch (PDOException $e) {
            return ["success" => false, "errors" => ["Database error: " . $e->getMessage()]];
        }
    }
}
