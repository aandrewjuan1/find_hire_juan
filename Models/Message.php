<?php
require_once("./Core/Database.php");

class Message extends Database {

    public function getMessages($userId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare(
                "SELECT Messages.*, Users.Email AS sender_email 
                 FROM Messages 
                 JOIN Users ON Messages.SenderID = Users.UserID
                 WHERE Messages.ReceiverID = :userId 
                 ORDER BY Messages.SentAt DESC"
            );
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching messages: " . $e->getMessage());
            return [];
        }
    }
    
    public function sendMessage($senderId, $receiverId, $content) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("INSERT INTO Messages (SenderID, ReceiverID, Content, SentAt) 
                                    VALUES (:senderId, :receiverId, :content, NOW())");
            $stmt->execute([
                ':senderId' => $senderId,
                ':receiverId' => $receiverId,
                ':content' => $content,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error sending message: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteAllMessages($applicantId, $hrId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare(
                "DELETE FROM Messages 
                 WHERE (SenderID = :applicantId AND ReceiverID = :hrId) 
                    OR (SenderID = :hrId AND ReceiverID = :applicantId)"
            );
            $stmt->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
            $stmt->bindParam(':hrId', $hrId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting messages: " . $e->getMessage());
            return false;
        }
    }

    public function getConversationBetweenUserAndApplicant($userId, $applicantId) {
        try {
            $dbh = $this->connect();
            
            // SQL query to fetch the conversation between HR and applicant
            $stmt = $dbh->prepare("SELECT * FROM messages WHERE 
                (SenderID = :userId AND ReceiverID = :applicantId) OR 
                (SenderID = :applicantId AND ReceiverID = :userId)
                ORDER BY SentAt ASC");
            
            // Bind parameters
            $stmt->execute([
                ':userId' => $userId,
                ':applicantId' => $applicantId
            ]);
            
            // Return the fetched messages as an array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching conversation: " . $e->getMessage());
            return [];
        }
    }
    

    public function getConversationBetweenUserAndHR($applicantId, $hrId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare(
                "SELECT Messages.*, Users.Email AS sender_email 
                 FROM Messages 
                 JOIN Users ON Messages.SenderID = Users.UserID
                 WHERE (Messages.SenderID = :applicantId AND Messages.ReceiverID = :hrId) 
                    OR (Messages.SenderID = :hrId AND Messages.ReceiverID = :applicantId)
                 ORDER BY Messages.SentAt ASC"
            );
            $stmt->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
            $stmt->bindParam(':hrId', $hrId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching conversation with HR: " . $e->getMessage());
            return [];
        }
    }

    public function getConversationWithHR($applicantId) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare(
                "SELECT Messages.*, Users.Email AS sender_email 
                 FROM Messages 
                 JOIN Users ON Messages.SenderID = Users.UserID
                 WHERE Messages.SenderID = :applicantId OR Messages.ReceiverID = :applicantId
                 ORDER BY Messages.SentAt ASC"
            );
            $stmt->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching conversation: " . $e->getMessage());
            return [];
        }
    }

    public function getUserIdByEmail($email) {
        try {
            $dbh = $this->connect();
            $stmt = $dbh->prepare("SELECT UserID FROM Users WHERE Email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['UserID'] ?? null;
        } catch (PDOException $e) {
            error_log("Error fetching user ID by email: " . $e->getMessage());
            return null;
        }
    }
}
?>
