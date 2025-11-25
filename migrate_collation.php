<?php
// migrate_collation.php
// Run this script once to change all collations

require_once 'config.php';

function migrateDatabaseCollation() {
    try {
        $pdo = getDBConnection();
        
        echo "Starting database collation migration...\n";
        
        // Step 1: Drop all foreign key constraints
        echo "Dropping foreign key constraints...\n";
        dropAllForeignKeys($pdo);
        
        // Step 2: Change database collation
        echo "Changing database collation...\n";
        $pdo->exec("ALTER DATABASE `abbott_admin` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Step 3: Change all tables collation
        echo "Changing table collations...\n";
        $tables = ['applications', 'otp_verification', 'document_uploads', 'support_tickets', 'support_messages', 'document_comments'];
        
        foreach ($tables as $table) {
            if (tableExists($pdo, $table)) {
                echo "Converting table: $table\n";
                $pdo->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }
        
        // Step 4: Recreate foreign key constraints
        echo "Recreating foreign key constraints...\n";
        recreateAllForeignKeys($pdo);
        
        echo "Database collation migration completed successfully!\n";
        return true;
        
    } catch (Exception $e) {
        echo "Migration failed: " . $e->getMessage() . "\n";
        return false;
    }
}

function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function dropAllForeignKeys($pdo) {
    // Get all foreign keys
    $stmt = $pdo->query("
        SELECT TABLE_NAME, CONSTRAINT_NAME
        FROM information_schema.TABLE_CONSTRAINTS 
        WHERE CONSTRAINT_SCHEMA = 'abbott_admin' 
        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");
    
    $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($foreignKeys as $fk) {
        try {
            $pdo->exec("ALTER TABLE `{$fk['TABLE_NAME']}` DROP FOREIGN KEY `{$fk['CONSTRAINT_NAME']}`");
            echo "Dropped foreign key: {$fk['CONSTRAINT_NAME']} from {$fk['TABLE_NAME']}\n";
        } catch (Exception $e) {
            echo "Warning: Could not drop foreign key {$fk['CONSTRAINT_NAME']}: " . $e->getMessage() . "\n";
        }
    }
}

function recreateAllForeignKeys($pdo) {
    // Recreate foreign keys for support_tickets
    if (tableExists($pdo, 'support_tickets') && tableExists($pdo, 'applications')) {
        try {
            $pdo->exec("
                ALTER TABLE `support_tickets` 
                ADD CONSTRAINT `fk_support_tickets_application` 
                FOREIGN KEY (`application_id`) 
                REFERENCES `applications` (`application_id`) 
                ON DELETE CASCADE
            ");
            echo "Recreated foreign key: fk_support_tickets_application\n";
        } catch (Exception $e) {
            echo "Warning: Could not recreate support_tickets foreign key: " . $e->getMessage() . "\n";
        }
    }
    
    // Recreate foreign keys for support_messages
    if (tableExists($pdo, 'support_messages') && tableExists($pdo, 'support_tickets')) {
        try {
            $pdo->exec("
                ALTER TABLE `support_messages` 
                ADD CONSTRAINT `fk_support_messages_ticket` 
                FOREIGN KEY (`ticket_id`) 
                REFERENCES `support_tickets` (`id`) 
                ON DELETE CASCADE
            ");
            echo "Recreated foreign key: fk_support_messages_ticket\n";
        } catch (Exception $e) {
            echo "Warning: Could not recreate support_messages foreign key: " . $e->getMessage() . "\n";
        }
    }
    
    // Recreate foreign keys for document_comments
    if (tableExists($pdo, 'document_comments') && tableExists($pdo, 'applications')) {
        try {
            $pdo->exec("
                ALTER TABLE `document_comments` 
                ADD CONSTRAINT `fk_document_comments_application` 
                FOREIGN KEY (`application_id`) 
                REFERENCES `applications` (`application_id`) 
                ON DELETE CASCADE
            ");
            echo "Recreated foreign key: fk_document_comments_application\n";
        } catch (Exception $e) {
            echo "Warning: Could not recreate document_comments foreign key: " . $e->getMessage() . "\n";
        }
    }
    
    // Recreate foreign keys for document_uploads
    if (tableExists($pdo, 'document_uploads') && tableExists($pdo, 'applications')) {
        try {
            $pdo->exec("
                ALTER TABLE `document_uploads` 
                ADD CONSTRAINT `fk_documents_application` 
                FOREIGN KEY (`application_id`) 
                REFERENCES `applications` (`application_id`) 
                ON DELETE CASCADE
            ");
            echo "Recreated foreign key: fk_documents_application\n";
        } catch (Exception $e) {
            echo "Warning: Could not recreate document_uploads foreign key: " . $e->getMessage() . "\n";
        }
    }
}

// Run the migration
migrateDatabaseCollation();
?>