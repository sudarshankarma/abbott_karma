<?php
class SupportController extends BaseController
{
    public function __construct()
    {
        parent::__construct('Support');
        $this->checkAuth([ROLE_ADMIN, ROLE_SUPER_ADMIN]);
    }

    // List tickets with unread counts
    public function index()
    {
        $sql = "
            SELECT t.*, a.full_name, a.application_id,
              (
                SELECT COUNT(*) FROM support_messages m
                WHERE m.ticket_id = t.id AND (m.is_read = 0 OR m.is_read IS NULL) AND m.sender_type = 'user'
              ) AS unread_from_user
            FROM support_tickets t
            LEFT JOIN applications a ON t.application_id = a.application_id
            ORDER BY t.updated_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('support/index', [
            'tickets' => $tickets
        ]);
    }

    // View a ticket with real-time chat
    public function view($id)
    {
        $ticket_id = (int)$id;

        // Mark user messages as read when admin opens the ticket
        $this->markMessagesAsRead($ticket_id);

        // Get ticket details
        $ticket = $this->getTicketById($ticket_id);
        if (!$ticket) {
            $this->redirect('support', 'index', 'error=ticket_not_found');
            return;
        }

        // Get combined messages (user messages + admin replies)
        $messages = $this->getTicketMessages($ticket_id);

        $this->render('support/view', [
            'ticket' => $ticket,
            'messages' => $messages
        ]);
    }

    // Document-specific chat
    public function documentChat($app_id = null, $doc_type = null)
    {
        $application_id = $app_id ?? $_GET['app_id'] ?? null;
        $document_type = $doc_type ?? $_GET['doc_type'] ?? 'pan_card';

        // Clean up document type (remove '_card' suffix if duplicated)
        $document_type = str_replace('_card_card', '_card', $document_type);

        if (!$application_id) {
            $this->redirect('applications', 'index', 'error=missing_application');
            return;
        }

        // Get application details
        $application = $this->getApplicationById($application_id);
        if (!$application) {
            $this->redirect('applications', 'index', 'error=application_not_found');
            return;
        }

        // Get document comments
        $comments = $this->getDocumentComments($application_id, $document_type);

        $this->render('support/document_chat', [
            'application' => $application,
            'document_type' => $document_type,
            'comments' => $comments
        ]);
    }

    // AJAX: Send message in support ticket
    // public function sendMessage()
    // {
    //     header('Content-Type: application/json');
        
    //     try {
    //         $input = $_POST;
    //         $text = trim($input['message'] ?? '');
    //         $ticket_id = isset($input['ticket_id']) ? (int)$input['ticket_id'] : null;
    //         $application_id = $input['application_id'] ?? null;
    //         $document_type = $input['document_type'] ?? null;

    //         if ($text === '') {
    //             throw new Exception('Empty message');
    //         }

    //         if ($ticket_id) {
    //             // Support ticket message
    //             $this->sendTicketMessage($ticket_id, $text);
    //            // echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    //         } elseif ($application_id && $document_type) {
    //             // Document comment
    //             $this->sendDocumentComment($application_id, $document_type, $text);
    //            // echo json_encode(['success' => true, 'message' => 'Comment sent successfully']);
    //         } else {
    //             throw new Exception('No ticket or document specified');
    //         }

    //     } catch (Exception $e) {
    //         error_log("Send message error: " . $e->getMessage());
    //         http_response_code(400);
    //         echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    //     }
    // }

    // In SupportController.php - update the sendMessage method
    public function sendMessage()
    {
        header('Content-Type: application/json');
        
        try {
            $input = $_POST;
            $text = trim($input['message'] ?? '');
            $ticket_id = isset($input['ticket_id']) ? (int)$input['ticket_id'] : null;
            $application_id = $input['application_id'] ?? null;
            $document_type = $input['document_type'] ?? null;

            if ($text === '') {
                throw new Exception('Empty message');
            }

            if ($ticket_id) {
                // Support ticket message
                $message_id = $this->sendTicketMessage($ticket_id, $text);
                
                // Get the newly created message to return
                $newMessage = $this->getMessageById($message_id);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Message sent successfully',
                    'data' => $newMessage
                ]);
            } elseif ($application_id && $document_type) {
                // Document comment
                $this->sendDocumentComment($application_id, $document_type, $text);
                echo json_encode(['success' => true, 'message' => 'Comment sent successfully']);
            } else {
                throw new Exception('No ticket or document specified');
            }

        } catch (Exception $e) {
            error_log("Send message error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Add this new method to get message by ID
    private function getMessageById($message_id)
    {
        $tablesExist = $this->checkSupportTables();
        
        if ($tablesExist['support_replies']) {
            $sql = "SELECT id, ticket_id, 'admin' AS sender_type, message AS body, created_at 
                    FROM support_replies WHERE id = ?";
        } else {
            $sql = "SELECT id, ticket_id, sender_type, message AS body, created_at 
                    FROM support_messages WHERE id = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$message_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update sendTicketMessage to return the inserted ID
    private function sendTicketMessage($ticket_id, $message)
    {
        $tablesExist = $this->checkSupportTables();
        
        if ($tablesExist['support_replies']) {
            $checkSql = "SHOW COLUMNS FROM support_replies LIKE 'admin_id'";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute();
            $hasAdminId = $checkStmt->fetch();

            if ($hasAdminId) {
                $sql = "INSERT INTO support_replies (ticket_id, admin_id, message, replied_by, created_at) 
                        VALUES (?, ?, ?, 'admin', NOW())";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $ticket_id,
                    $_SESSION['user_id'],
                    $message
                ]);
            } else {
                $sql = "INSERT INTO support_replies (ticket_id, message, replied_by, created_at) 
                        VALUES (?, ?, 'admin', NOW())";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$ticket_id, $message]);
            }
            $message_id = $this->db->lastInsertId();
        } else {
            $sql = "INSERT INTO support_messages (ticket_id, message, sender_type, created_at) 
                    VALUES (?, ?, 'admin', NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ticket_id, $message]);
            $message_id = $this->db->lastInsertId();
        }

        // Update ticket timestamp
        $this->updateTicketTimestamp($ticket_id);
        
        return $message_id;
    }

    // AJAX: Send document comment
    public function sendDocumentCommentAjax()
    {
        header('Content-Type: application/json');
        
        try {
            $input = $_POST;
            $application_id = $input['application_id'] ?? null;
            $document_type = $input['document_type'] ?? null;
            $comment = trim($input['comment'] ?? '');

            if (!$application_id || !$document_type || $comment === '') {
                throw new Exception('All fields are required');
            }

            $this->sendDocumentComment($application_id, $document_type, $comment);

            echo json_encode(['success' => true, 'message' => 'Comment sent successfully']);
        } catch (Exception $e) {
            error_log("Send document comment error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Private helper methods
    private function getTicketById($ticket_id)
    {
        $sql = "SELECT t.*, a.full_name, a.phone, a.email, a.application_id 
                FROM support_tickets t 
                LEFT JOIN applications a ON t.application_id = a.application_id 
                WHERE t.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticket_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getTicketMessages($ticket_id)
    {
        // First, let's check what tables and columns actually exist
        $tablesExist = $this->checkSupportTables();
        
        if ($tablesExist['support_replies']) {
            // If support_replies table exists, use it for admin messages
            $sql = "
                SELECT id, ticket_id, 'user' AS sender_type, message AS body, created_at, is_read
                FROM support_messages WHERE ticket_id = ?
                UNION ALL
                SELECT id, ticket_id, 'admin' AS sender_type, message AS body, created_at, is_read
                FROM support_replies WHERE ticket_id = ?
                ORDER BY created_at ASC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ticket_id, $ticket_id]);
        } else {
            // If only support_messages exists, use sender_type to distinguish
            $sql = "
                SELECT id, ticket_id, sender_type, message AS body, created_at, is_read
                FROM support_messages 
                WHERE ticket_id = ? 
                ORDER BY created_at ASC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ticket_id]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function markMessagesAsRead($ticket_id)
    {
        $sql = "UPDATE support_messages SET is_read = 1 WHERE ticket_id = ? AND sender_type = 'user'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticket_id]);
    }

    // private function sendTicketMessage($ticket_id, $message)
    // {
    //     // Check if support_replies table exists and has the right structure
    //     $tablesExist = $this->checkSupportTables();
        
    //     if ($tablesExist['support_replies']) {
    //         // Use support_replies for admin messages
    //         $checkSql = "SHOW COLUMNS FROM support_replies LIKE 'admin_id'";
    //         $checkStmt = $this->db->prepare($checkSql);
    //         $checkStmt->execute();
    //         $hasAdminId = $checkStmt->fetch();

    //         if ($hasAdminId) {
    //             $sql = "INSERT INTO support_replies (ticket_id, admin_id, message, replied_by, created_at) 
    //                     VALUES (?, ?, ?, 'admin', NOW())";
    //             $stmt = $this->db->prepare($sql);
    //             $stmt->execute([
    //                 $ticket_id,
    //                 $_SESSION['user_id'],
    //                 $message
    //             ]);
    //         } else {
    //             // Fallback to simple insert
    //             $sql = "INSERT INTO support_replies (ticket_id, message, replied_by, created_at) 
    //                     VALUES (?, ?, 'admin', NOW())";
    //             $stmt = $this->db->prepare($sql);
    //             $stmt->execute([$ticket_id, $message]);
    //         }
    //     } else {
    //         // If no support_replies table, use support_messages with sender_type = 'admin'
    //         $sql = "INSERT INTO support_messages (ticket_id, message, sender_type, created_at) 
    //                 VALUES (?, ?, 'admin', NOW())";
    //         $stmt = $this->db->prepare($sql);
    //         $stmt->execute([$ticket_id, $message]);
    //     }

    //     // Update ticket timestamp
    //     $this->updateTicketTimestamp($ticket_id);
    // }

    private function sendDocumentComment($application_id, $document_type, $comment)
    {
        // Check if document_comments table has admin_id column
        $checkSql = "SHOW COLUMNS FROM document_comments LIKE 'admin_id'";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute();
        $hasAdminId = $checkStmt->fetch();

        if ($hasAdminId) {
            $sql = "INSERT INTO document_comments (application_id, document_type, comment, commented_by, admin_id) 
                    VALUES (?, ?, ?, 'admin', ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $application_id, 
                $document_type, 
                $comment,
                $_SESSION['user_id']
            ]);
        } else {
            // Fallback to simple insert
            $sql = "INSERT INTO document_comments (application_id, document_type, comment, commented_by) 
                    VALUES (?, ?, ?, 'admin')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$application_id, $document_type, $comment]);
        }
    }

    private function updateTicketTimestamp($ticket_id)
    {
        $sql = "UPDATE support_tickets SET updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticket_id]);
    }

    private function getApplicationById($application_id)
    {
        $sql = "SELECT * FROM applications WHERE application_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$application_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getDocumentComments($application_id, $document_type)
    {
        // Check if document_comments table has admin_id column for joining
        $checkSql = "SHOW COLUMNS FROM document_comments LIKE 'admin_id'";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute();
        $hasAdminId = $checkStmt->fetch();

        if ($hasAdminId) {
            $sql = "SELECT dc.*, au.username as admin_name 
                    FROM document_comments dc 
                    LEFT JOIN admin_users au ON dc.admin_id = au.id 
                    WHERE dc.application_id = ? AND dc.document_type = ? 
                    ORDER BY dc.created_at ASC";
        } else {
            $sql = "SELECT dc.*, '' as admin_name 
                    FROM document_comments dc 
                    WHERE dc.application_id = ? AND dc.document_type = ? 
                    ORDER BY dc.created_at ASC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$application_id, $document_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // In SupportController.php - Replace the checkSupportTables method with this optimized version
    private function checkSupportTables()
    {
        static $tablesChecked = null;
        
        // Return cached result if already checked
        if ($tablesChecked !== null) {
            return $tablesChecked;
        }

        $tables = [
            'support_replies' => false,
            'support_messages' => false
        ];

        try {
            // Check both tables in a single query for better performance
            $checkSql = "SHOW TABLES LIKE 'support_replies' OR SHOW TABLES LIKE 'support_messages'";
            
            // Actually, let's do it properly with separate queries but with error handling
            $checkSql1 = "SHOW TABLES LIKE 'support_replies'";
            $checkStmt1 = $this->db->prepare($checkSql1);
            $checkStmt1->execute();
            $tables['support_replies'] = (bool)$checkStmt1->fetch();

            $checkSql2 = "SHOW TABLES LIKE 'support_messages'";
            $checkStmt2 = $this->db->prepare($checkSql2);
            $checkStmt2->execute();
            $tables['support_messages'] = (bool)$checkStmt2->fetch();
            
        } catch (Exception $e) {
            // If there's an error, assume tables don't exist
            error_log("Table check error: " . $e->getMessage());
            $tables['support_replies'] = false;
            $tables['support_messages'] = false;
        }

        // Cache the result
        $tablesChecked = $tables;
        return $tables;
    }

    // Add these methods to SupportController.php

    // AJAX: Close support ticket
    public function closeTicket()
    {
        header('Content-Type: application/json');
        
        try {
            $input = $_POST;
            $ticket_id = isset($input['ticket_id']) ? (int)$input['ticket_id'] : null;

            if (!$ticket_id) {
                throw new Exception('Ticket ID is required');
            }

            $success = $this->updateTicketStatus($ticket_id, 'closed');
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Ticket closed successfully',
                    'status' => 'closed'
                ]);
            } else {
                throw new Exception('Failed to close ticket');
            }

        } catch (Exception $e) {
            error_log("Close ticket error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // AJAX: Reopen support ticket
    public function reopenTicket()
    {
        header('Content-Type: application/json');
        
        try {
            $input = $_POST;
            $ticket_id = isset($input['ticket_id']) ? (int)$input['ticket_id'] : null;

            if (!$ticket_id) {
                throw new Exception('Ticket ID is required');
            }

            $success = $this->updateTicketStatus($ticket_id, 'open');
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Ticket reopened successfully',
                    'status' => 'open'
                ]);
            } else {
                throw new Exception('Failed to reopen ticket');
            }

        } catch (Exception $e) {
            error_log("Reopen ticket error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Update ticket status
    // private function updateTicketStatus($ticket_id, $status)
    // {
    //     $sql = "UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?";
    //     $stmt = $this->db->prepare($sql);
    //     return $stmt->execute([$status, $ticket_id]);
    // }

    // Add this method to get ticket status
    private function getTicketStatus($ticket_id)
    {
        $sql = "SELECT status FROM support_tickets WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticket_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['status'] : null;
    }
    // Add this method to SupportController.php for additional status updates
    public function updateTicketStatus()
    {
        header('Content-Type: application/json');
        
        try {
            $input = $_POST;
            $ticket_id = isset($input['ticket_id']) ? (int)$input['ticket_id'] : null;
            $status = $input['status'] ?? null;

            if (!$ticket_id || !$status) {
                throw new Exception('Ticket ID and status are required');
            }

            $validStatuses = ['open', 'in_progress', 'resolved', 'closed'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Invalid status');
            }

            $success = $this->updateTicketStatus($ticket_id, $status);
            
            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Ticket status updated successfully',
                    'status' => $status
                ]);
            } else {
                throw new Exception('Failed to update ticket status');
            }

        } catch (Exception $e) {
            error_log("Update ticket status error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

}
?>