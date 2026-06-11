<?php

/**
 * PHP Cheat Sheet - 12: Database Access with MySQLi
 *
 * Topics covered:
 * - MySQLi Extension Overview (MySQL Improved)
 * - Object-Oriented vs Procedural syntax
 * - Database Connection & error checking
 * - Safe Queries using Prepared Statements (bind_param, bind_result)
 * - Fetching Associative Results
 * - Database Transactions in MySQLi
 */

echo "=== DATABASE ACCESS WITH MySQLi ===\n";
echo "Note: MySQLi is specific to MySQL/MariaDB. This file documents its usage.\n\n";

// --- 1. CONNECTION METHODS ---

echo "[1] Database Connection Setup\n";

$isSimulation = false;

// 1a. Object-Oriented Style (Recommended)
try {
    // Suppress warnings with @ and rely on exception handling
    $mysqli = @new mysqli('localhost', 'username', 'password', 'database_name');
    
    if ($mysqli->connect_error) {
        throw new mysqli_sql_exception($mysqli->connect_error, $mysqli->connect_errno);
    }
    echo "Connected successfully (OO)\n";
} catch (mysqli_sql_exception $e) {
    echo "Connection failed (OO): " . $e->getMessage() . "\n";
    echo "MySQL database server not detected. Running remainder of example in Simulation Mode.\n";
    $isSimulation = true;
}

// 1b. Procedural Style (Legacy)
if (!$isSimulation) {
    $link = @mysqli_connect('localhost', 'username', 'password', 'database_name');
    if (!$link) {
        echo "Connection failed (Procedural): " . mysqli_connect_error() . "\n";
    } else {
        echo "Connected successfully (Procedural)\n";
        mysqli_close($link);
    }
} else {
    echo "Procedural connection bypassed (Simulation Mode).\n";
}

echo "- See comments in code for OO and Procedural connections.\n";

// --- 2. RUNNING A BASIC SELECT QUERY ---

echo "\n[2] Fetching query records (Object-Oriented):\n";

if (!$isSimulation) {
    $sql = 'SELECT id, username, email FROM users';
    $result = $mysqli->query($sql);
    
    if ($result) {
        // Fetch rows as associative arrays
        while ($row = $result->fetch_assoc()) {
            echo 'ID: ' . $row['id'] . ' | Name: ' . $row['username'] . "\n";
        }
        // Free result set memory
        $result->free_result();
    } else {
        echo 'Query Error: ' . $mysqli->error . "\n";
    }
} else {
    // Simulated Select output
    echo "ID: 1 | Name: clayton (Simulated)\n";
    echo "ID: 2 | Name: alice (Simulated)\n";
}

echo "- Uses \$mysqli->query() and \$result->fetch_assoc() to parse data rows.\n";

// --- 3. PREPARED STATEMENTS ---

echo "\n[3] Prepared Statements (Preventing SQL injection):\n";

if (!$isSimulation) {
    // SQL template with parameter placeholder '?'
    $stmt = $mysqli->prepare('SELECT email FROM users WHERE username = ? AND status = ?');
    
    if ($stmt) {
        // Bind parameters
        // Types: 's' = string, 'i' = integer, 'd' = double/float, 'b' = blob
        $stmt->bind_param('ss', $usernameParam, $statusParam);
        
        // Set variables and execute
        $usernameParam = 'clayton';
        $statusParam = 'active';
        $stmt->execute();
        
        // Bind results to variables
        $stmt->bind_result($emailResult);
        
        // Fetch values
        while ($stmt->fetch()) {
            echo 'Email found: ' . $emailResult . "\n";
        }
        
        // Close statement
        $stmt->close();
    }
} else {
    // Simulated Prepared Statement output
    echo "Email found: clayton@example.com (Simulated)\n";
}

echo "- Uses \$mysqli->prepare(), \$stmt->bind_param(), and \$stmt->bind_result().\n";

// --- 4. TRANSACTIONS ---

echo "\n[4] MySQLi Transactions:\n";

if (!$isSimulation) {
    // Disable autocommit to start transaction
    $mysqli->autocommit(false);
    
    try {
        $mysqli->query("INSERT INTO users (username, email) VALUES ('bob', 'bob@example.com')");
        $mysqli->query("UPDATE logs SET count = count + 1 WHERE action = 'user_register'");
        
        // Commit transaction
        $mysqli->commit();
        echo "Transaction committed successfully.\n";
    } catch (Exception $e) {
        // Rollback if anything goes wrong
        $mysqli->rollback();
        echo 'Transaction rolled back: ' . $e->getMessage() . "\n";
    } finally {
        // Restore default autocommit behavior
        $mysqli->autocommit(true);
    }
} else {
    // Simulated Transaction output
    echo "Transaction committed successfully (Simulated).\n";
}

echo "- Uses \$mysqli->autocommit(FALSE) to begin, \$mysqli->commit() to apply, and \$mysqli->rollback() to undo.\n";

echo "\nMySQLi documentation loaded successfully!\n";
