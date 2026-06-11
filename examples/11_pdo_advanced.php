<?php
/**
 * PHP Cheat Sheet - 11: Advanced PDO (PHP Data Objects)
 * 
 * Topics covered:
 * - Complex SQL queries using PDO (SQLite In-Memory)
 *   - INNER JOINs & LEFT JOINs
 *   - Aggregation and Grouping (COUNT, SUM, GROUP BY)
 *   - Subqueries
 *   - Database Transactions (beginTransaction, commit, rollBack)
 */

echo "=== ADVANCED PDO QUERIES (SQLite In-Memory) ===\n";

try {
    // 1. DSN Connection Setup
    $pdo = new PDO("sqlite::memory:");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables: Users and Posts (1-to-many relationship)
    $pdo->exec("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            email TEXT NOT NULL
        )
    ");
    
    $pdo->exec("
        CREATE TABLE posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            title TEXT NOT NULL,
            body TEXT NOT NULL,
            likes INTEGER DEFAULT 0,
            FOREIGN KEY(user_id) REFERENCES users(id)
        )
    ");
    
    // Seed database
    $insertUser = $pdo->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
    $insertUser->execute(['clayton', 'clayton@example.com']); // User ID 1
    $insertUser->execute(['alice', 'alice@example.com']);     // User ID 2
    $insertUser->execute(['bob', 'bob@example.com']);         // User ID 3
    
    $insertPost = $pdo->prepare("INSERT INTO posts (user_id, title, body, likes) VALUES (?, ?, ?, ?)");
    $insertPost->execute([1, 'PHP 8.2 Features', 'All about PHP 8.2 standard library...', 15]);
    $insertPost->execute([1, 'Modern PDO Tutorial', 'How to use prepared statements...', 32]);
    $insertPost->execute([2, 'Object-Oriented Programming', 'Inheritance, interfaces, and classes.', 8]);
    $insertPost->execute([2, 'Designing REST APIs', 'Best practices for PHP routers.', 24]);
    
    
    // 1a. INNER JOIN (Fetch posts with user details)
    echo "\n[1a] INNER JOIN - Fetching posts and author names:\n";
    $joinSql = "
        SELECT posts.id, posts.title, users.username, posts.likes
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        ORDER BY posts.likes DESC
    ";
    $stmt = $pdo->query($joinSql);
    foreach ($stmt->fetchAll() as $row) {
        echo "- '{$row['title']}' by {$row['username']} (Likes: {$row['likes']})\n";
    }
    
    
    // 1b. AGGREGATION & GROUP BY (Count posts and calculate total likes per user)
    echo "\n[1b] GROUP BY & AGGREGATE - Post stats per user:\n";
    $aggSql = "
        SELECT users.username, 
               COUNT(posts.id) as total_posts, 
               SUM(posts.likes) as total_likes
        FROM users
        LEFT JOIN posts ON users.id = posts.user_id
        GROUP BY users.id
    ";
    $stmt = $pdo->query($aggSql);
    foreach ($stmt->fetchAll() as $row) {
        $likes = $row['total_likes'] ?? 0;
        echo "- User '{$row['username']}': {$row['total_posts']} posts, {$likes} total likes\n";
    }
    
    
    // 1c. SUBQUERY (Find users who have posts with more than 20 likes)
    echo "\n[1c] SUBQUERY - Users with highly liked posts:\n";
    $subSql = "
        SELECT username, email 
        FROM users 
        WHERE id IN (
            SELECT DISTINCT user_id 
            FROM posts 
            WHERE likes > :min_likes
        )
    ";
    $stmt = $pdo->prepare($subSql);
    $stmt->execute([':min_likes' => 20]);
    foreach ($stmt->fetchAll() as $row) {
        echo "- {$row['username']} ({$row['email']})\n";
    }

    
    // 1d. TRANSACTIONS
    echo "\n[1d] TRANSACTION FLOW:\n";
    $pdo->beginTransaction();
    
    $insertStmt = $pdo->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
    $insertStmt->execute(['new_user', 'new_user@example.com']);
    $newUserId = $pdo->lastInsertId();
    
    $insertPost->execute([$newUserId, 'Transaction Post', 'Writing inside an atomic block.', 1]);
    
    $pdo->commit();
    echo "Transaction committed! User and Post inserted atomically.\n";
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "PDO Error: " . $e->getMessage() . "\n";
}
?>
