<?php
/**
 * PHP Cheat Sheet - 13f: Standard Library (SPL) Data Structures
 * 
 * Topics covered:
 * - SplStack: LIFO (Last-In, First-Out) stack
 * - SplQueue: FIFO (First-In, First-Out) queue
 * - SplFixedArray: Memory-efficient fixed-size arrays
 * - SplPriorityQueue: Order elements based on priority values
 * - SplObjectStorage: Map objects to data or create sets of unique objects
 */

echo "=== 1. SPL STACK (LIFO) ===\n";
$stack = new SplStack();

// Push items onto the stack
$stack->push("Request #1");
$stack->push("Request #2");
$stack->push("Request #3");

echo "Stack count: " . $stack->count() . "\n";
echo "Top of stack (peeking): " . $stack->top() . "\n";

// Iterate (LIFO behavior)
echo "Iterating through stack:\n";
foreach ($stack as $item) {
    echo " - $item\n";
}

// Pop items
echo "Popping: " . $stack->pop() . "\n";
echo "Popping: " . $stack->pop() . "\n";
echo "Stack count after pops: " . $stack->count() . "\n\n";


echo "=== 2. SPL QUEUE (FIFO) ===\n";
$queue = new SplQueue();

// Enqueue items
$queue->enqueue("Job A");
$queue->enqueue("Job B");
$queue->enqueue("Job C");

echo "Queue count: " . $queue->count() . "\n";
echo "Front of queue (peeking): " . $queue->bottom() . "\n";

// Iterate (FIFO behavior)
echo "Iterating through queue:\n";
foreach ($queue as $item) {
    echo " - $item\n";
}

// Dequeue items
echo "Dequeuing: " . $queue->dequeue() . "\n";
echo "Dequeuing: " . $queue->dequeue() . "\n";
echo "Queue count after dequeues: " . $queue->count() . "\n\n";


echo "=== 3. SPL FIXED ARRAY ===\n";
// SplFixedArray is faster and uses less memory because it has a non-resizable size
$size = 5;
$fixed = new SplFixedArray($size);

$fixed[0] = "Apple";
$fixed[1] = "Banana";
$fixed[3] = "Cherry"; // index 2 remains null

echo "Fixed Array Size: " . $fixed->getSize() . "\n";
for ($i = 0; $i < $size; $i++) {
    echo " - Index $i: " . ($fixed[$i] ?? '(null)') . "\n";
}

// Attempting to resize it
$fixed->setSize(6);
$fixed[5] = "Date";
echo "New size: " . $fixed->getSize() . "\n\n";


echo "=== 4. SPL PRIORITY QUEUE ===\n";
class TaskQueue extends SplPriorityQueue {
    // Customizing behavior if needed. By default, it extracts value only.
}

$pq = new TaskQueue();
$pq->insert("Low Priority Task", 1);
$pq->insert("Critical Priority Task", 100);
$pq->insert("Medium Priority Task", 50);
$pq->insert("High Priority Task", 75);

// Set extraction mode to extract both value and priority or just data
// SplPriorityQueue::EXTR_DATA (value only), SplPriorityQueue::EXTR_PRIORITY (priority only), SplPriorityQueue::EXTR_BOTH (array)
$pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

echo "Extracting queue items by priority:\n";
while ($pq->valid()) {
    $item = $pq->extract();
    echo " - Task: {$item['data']} (Priority: {$item['priority']})\n";
}
echo "\n";


echo "=== 5. SPL OBJECT STORAGE ===\n";
// Allows mapping objects to arbitrary data, or creating unique sets of objects.
$storage = new SplObjectStorage();

$obj1 = new stdClass();
$obj1->name = "User Object 1";

$obj2 = new stdClass();
$obj2->name = "User Object 2";

// Attach objects (PHP 8.5 deprecates attach() in favor of ArrayAccess offsetSet)
$storage->offsetSet($obj1, ["role" => "Admin"]);
$storage->offsetSet($obj2, ["role" => "Editor"]);

// Check if an object exists in storage (PHP 8.5 deprecates contains() in favor of offsetExists)
echo "Contains obj1: " . ($storage->offsetExists($obj1) ? "Yes" : "No") . "\n";

// Get associated data
$data = $storage[$obj1];
echo "obj1 role: " . $data['role'] . "\n";

// Iterate storage
echo "Iterating objects in SplObjectStorage:\n";
foreach ($storage as $obj) {
    $info = $storage->getInfo();
    echo " - Object name: {$obj->name}, Role info: {$info['role']}\n";
}

// Detach (PHP 8.5 deprecates detach() in favor of offsetUnset)
$storage->offsetUnset($obj1);
echo "Contains obj1 after detach: " . ($storage->offsetExists($obj1) ? "Yes" : "No") . "\n";
