<?php
/**
 * PHP Cheat Sheet - 41: Database Queues & Background Workers
 * 
 * Topics covered:
 * - Queue Architecture: Why and when to offload tasks to background jobs
 * - SQLite Queue Table Schema (runnable anywhere)
 * - Producing (Pushing) jobs to the queue
 * - Consuming (Reserving and locking) jobs safely via Transactions
 * - Processing tasks and handling failures/attempts
 * - Real-world message brokers overview (Redis, RabbitMQ, Beanstalkd)
 */

echo "=== 1. QUEUE TABLE SCHEMA SETUP ===\n";

// We create an in-memory SQLite database to simulate database-backed queue states
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create the jobs table
$db->exec("
CREATE TABLE jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    queue_name TEXT NOT NULL,
    payload TEXT NOT NULL,
    status TEXT DEFAULT 'pending', -- pending, processing, completed, failed
    attempts INTEGER DEFAULT 0,
    locked_at INTEGER DEFAULT NULL,
    created_at INTEGER NOT NULL
);
");
echo "SQLite 'jobs' table successfully created in memory.\n\n";


echo "=== 2. PRODUCER: PUSHING JOBS TO THE QUEUE ===\n";

function pushJob(PDO $db, string $queue, array $payload): int {
    $stmt = $db->prepare("
        INSERT INTO jobs (queue_name, payload, status, created_at)
        VALUES (:queue, :payload, 'pending', :created_at)
    ");
    $stmt->execute([
        'queue' => $queue,
        'payload' => json_encode($payload),
        'created_at' => time()
    ]);
    return (int)$db->lastInsertId();
}

// Pushing mock tasks
echo "Pushing job 1: Send Welcome Email\n";
pushJob($db, 'emails', ['type' => 'welcome_email', 'email' => 'clayton@example.com', 'user_id' => 123]);

echo "Pushing job 2: Generate PDF Invoice\n";
pushJob($db, 'reports', ['type' => 'generate_pdf', 'invoice_id' => 45092]);

echo "Pushing job 3: Resize Uploaded Avatar\n";
pushJob($db, 'images', ['type' => 'resize_image', 'file_path' => '/uploads/avatar.png']);
echo "\n";


echo "=== 3. CONSUMER: RESERVING JOBS SAFELY (LOCKING) ===\n";
echo "Workers must reserve and lock a job atomically to prevent other concurrent workers from picking up the same task:\n\n";

function reserveJob(PDO $db, string $queue): ?array {
    // Start transactional lock boundary
    $db->beginTransaction();
    
    try {
        // 1. Query the oldest pending job (or timed-out processing jobs for retries)
        // In MySQL, you would use: SELECT ... FOR UPDATE SKIP LOCKED
        $stmt = $db->prepare("
            SELECT * FROM jobs 
            WHERE queue_name = :queue 
              AND (status = 'pending' OR (status = 'processing' AND locked_at < :timeout))
            ORDER BY created_at ASC 
            LIMIT 1
        ");
        
        $fiveMinutesAgo = time() - 300; // 5 min timeout
        $stmt->execute(['queue' => $queue, 'timeout' => $fiveMinutesAgo]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$job) {
            $db->commit();
            return null;
        }
        
        // 2. Lock the job by changing status and increasing attempt count
        $updateStmt = $db->prepare("
            UPDATE jobs 
            SET status = 'processing', 
                locked_at = :now, 
                attempts = attempts + 1 
            WHERE id = :id
        ");
        $updateStmt->execute([
            'now' => time(),
            'id' => $job['id']
        ]);
        
        $db->commit();
        
        // Return latest values
        $job['status'] = 'processing';
        $job['attempts']++;
        $job['payload'] = json_decode($job['payload'], true);
        return $job;
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}


echo "=== 4. WORKER PROCESSING LOOP ===\n";

function processJob(array $job): bool {
    echo "Processing Job #{$job['id']} [Attempts: {$job['attempts']}]: ";
    $payload = $job['payload'];
    
    switch ($payload['type']) {
        case 'welcome_email':
            echo "Sending welcome email to {$payload['email']}... [OK]\n";
            return true;
        case 'generate_pdf':
            // Simulating a random failure trigger for demonstration
            echo "Generating invoice PDF #{$payload['invoice_id']}... [FAILED]\n";
            return false;
        case 'resize_image':
            echo "Resizing image {$payload['file_path']}... [OK]\n";
            return true;
        default:
            echo "Unknown job type.\n";
            return false;
    }
}

function updateJobStatus(PDO $db, int $jobId, string $status) {
    $stmt = $db->prepare("UPDATE jobs SET status = :status, locked_at = NULL WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $jobId]);
}

// Run simulated worker loops for queues
$queues = ['emails', 'reports', 'images'];

foreach ($queues as $queueName) {
    echo "Worker polling queue '$queueName'...\n";
    $job = reserveJob($db, $queueName);
    
    if ($job) {
        $success = processJob($job);
        if ($success) {
            updateJobStatus($db, $job['id'], 'completed');
            echo "Job #{$job['id']} marked as COMPLETED.\n";
        } else {
            // If failed, check attempts count
            if ($job['attempts'] >= 3) {
                updateJobStatus($db, $job['id'], 'failed');
                echo "Job #{$job['id']} exceeded max attempts. Marked as FAILED.\n";
            } else {
                updateJobStatus($db, $job['id'], 'pending'); // release back to queue
                echo "Job #{$job['id']} released back to PENDING for retry.\n";
            }
        }
    } else {
        echo "No jobs available in '$queueName'.\n";
    }
    echo "\n";
}


echo "=== 5. ENTERPRISE MESSAGE BROKERS ===\n";
echo "While DB queues work for small systems, enterprise PHP applications use:\n";
echo "1. Redis (via Laravel Queue / Resque): In-memory, high throughput, perfect for light tasks.\n";
echo "2. Beanstalkd: Simple, lightweight specialized daemon with job priorities and delays.\n";
echo "3. RabbitMQ / ActiveMQ: Advanced protocols (AMQP), robust routing, absolute reliability.\n";
?>
