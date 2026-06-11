<?php
/**
 * PHP Cheat Sheet - 10: Database Access with PDO
 * 
 * Topics covered:
 * - Establishing a PDO connection (using an SQLite In-Memory Database)
 * - Setting PDO error and fetch attributes
 * - Creating tables
 * - Prepared statements (preventing SQL injection)
 * - Fetching results (assoc arrays)
 * - PDO Transaction basics
 */

echo "=== 1. DATABASE CONNECTION ===\n";

try {
    // We use sqlite::memory: which creates a temporary database entirely in memory.
    // This allows the cheat sheet to run out of the box on any system with SQLite extension active.
    $dsn = "sqlite::memory:";
    $pdo = new PDO($dsn);
    
    // Set error mode to Exceptions so database errors throw Catchable exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    echo "Connection established successfully to SQLite in-memory database!\n";
} catch (PDOException $e) {
    echo "Database Connection Failed: " . $e->getMessage() . "\n";
    exit(1);
}


echo "\n=== 2. CREATING TABLES ===\n";

try {
    $createTableSql = "
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";
    
    // exec() is used for queries that do not return a result set (CREATE, UPDATE, DELETE)
    $pdo->exec($createTableSql);
    echo "Table 'users' created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}


echo "\n=== 3. PREPARED STATEMENTS (INSERTING DATA) ===\n";

try {
    // Prepared statements protect against SQL Injection by separating SQL logic from user inputs.
    $insertSql = "INSERT INTO users (username, email) VALUES (:username, :email)";
    
    // Prepare the SQL statement template
    $stmt = $pdo->prepare($insertSql);
    
    $usersToInsert = [
        ['username' => 'clayton', 'email' => 'clayton@example.com'],
        ['username' => 'alice', 'email' => 'alice@example.com'],
        ['username' => 'bob', 'email' => 'bob@example.com'],
    ];
    
    echo "Inserting users:\n";
    foreach ($usersToInsert as $userData) {
        // Bind arguments and execute
        $stmt->execute([
            ':username' => $userData['username'],
            ':email' => $userData['email']
        ]);
        echo "- Inserted user: {$userData['username']} (ID: " . $pdo->lastInsertId() . ")\n";
    }
} catch (PDOException $e) {
    echo "Error inserting data: " . $e->getMessage() . "\n";
}


echo "\n=== 4. QUERYING DATA (SELECT STATEMENTS) ===\n";

try {
    $selectSql = "SELECT id, username, email, created_at FROM users WHERE id > :min_id";
    $selectStmt = $pdo->prepare($selectSql);
    
    // Execute query with a parameter
    $selectStmt->execute([':min_id' => 1]);
    
    // Fetch all matching rows
    $results = $selectStmt->fetchAll();
    
    echo "Query results (users with ID > 1):\n";
    foreach ($results as $row) {
        echo "- ID: {$row['id']} | Username: {$row['username']} | Email: {$row['email']} | Created At: {$row['created_at']}\n";
    }
} catch (PDOException $e) {
    echo "Error querying data: " . $e->getMessage() . "\n";
}


echo "\n=== 5. TRANSACTIONS ===\n";

try {
    // Transactions ensure multiple database queries execute atomically. If one fails, we roll back.
    echo "Beginning Transaction...\n";
    $pdo->beginTransaction();
    
    $insertStmt = $pdo->prepare("INSERT INTO users (username, email) VALUES (:username, :email)");
    
    $insertStmt->execute([':username' => 'charlie', 'email' => 'charlie@example.com']);
    $insertStmt->execute([':username' => 'david', 'email' => 'david@example.com']);
    
    // Commit changes to database
    $pdo->commit();
    echo "Transaction committed successfully! Both users inserted.\n";
} catch (Exception $e) {
    // Rollback changes if any query fails
    $pdo->rollBack();
    echo "Transaction failed! Rolled back. Error: " . $e->getMessage() . "\n";
}
?>
