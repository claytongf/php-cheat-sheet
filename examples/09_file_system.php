<?php
/**
 * PHP Cheat Sheet - 09: File System Operations (Comprehensive)
 * 
 * Topics covered:
 * - Checking permissions, paths, and metadata
 * - Directory operations (mkdir, rmdir, recursive glob/scans)
 * - Quick file helpers (file_get_contents, file_put_contents, file())
 * - Advanced stream pointer modes and operations (fopen, fseek, ftell, rewind)
 * - Thread-safe file locking (flock)
 * - Reading & writing CSV files (fputcsv, fgetcsv)
 * - File mutations (copy, rename, unlink)
 * - OOP directory scanning (RecursiveDirectoryIterator)
 */

$demoDir = __DIR__ . '/fs_demo';
$demoFile = $demoDir . '/sample.txt';
$csvFile = $demoDir . '/contacts.csv';

echo "=== 1. PATH RESOLUTIONS & METADATA ===\n";
// Create folder if missing
if (!is_dir($demoDir)) {
    mkdir($demoDir, 0755, true);
}

// Write a sample file first for info inspection
file_put_contents($demoFile, "Line 1: PHP Core File System\nLine 2: Advanced Stream Operations\n");

echo "Checking file info for: " . basename($demoFile) . "\n";
echo "- Exists: " . (file_exists($demoFile) ? 'Yes' : 'No') . "\n";
echo "- Is File: " . (is_file($demoFile) ? 'Yes' : 'No') . "\n";
echo "- Is Directory: " . (is_dir($demoFile) ? 'Yes' : 'No') . "\n";
echo "- Is Readable: " . (is_readable($demoFile) ? 'Yes' : 'No') . "\n";
echo "- Is Writable: " . (is_writable($demoFile) ? 'Yes' : 'No') . "\n";
echo "- Size: " . filesize($demoFile) . " bytes\n";
echo "- Last Modified: " . date('Y-m-d H:i:s', filemtime($demoFile)) . "\n";

// Path parsing using pathinfo()
$pathParts = pathinfo($demoFile);
echo "Path Info Elements:\n";
print_r([
    'dirname' => $pathParts['dirname'],
    'basename' => $pathParts['basename'],
    'extension' => $pathParts['extension'] ?? 'none',
    'filename' => $pathParts['filename']
]);
echo "- Absolute path (realpath): " . realpath($demoFile) . "\n\n";


echo "=== 2. QUICK READ & WRITE HELPER METHODS ===\n";

// Write string to file (FILE_APPEND appends, LOCK_EX locks file while writing)
echo "Appending a line via file_put_contents(..., FILE_APPEND)...\n";
file_put_contents($demoFile, "Line 3: Appended content via helper\n", FILE_APPEND | LOCK_EX);

// Read entire file into string
$content = file_get_contents($demoFile);
echo "Content length read: " . strlen($content) . " chars\n";

// file() reads the entire file into an array of lines
echo "Reading lines using file():\n";
$lines = file($demoFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
print_r($lines);
echo "\n";


echo "=== 3. STREAM MODES & POINTER MANIPULATION ===\n";
echo "Understanding fopen() modes:\n";
echo "- 'r': Read only (pointer at start)\n";
echo "- 'r+': Read/Write (pointer at start)\n";
echo "- 'w': Write only (truncates file to 0 size; creates file if missing)\n";
echo "- 'w+': Read/Write (truncates file to 0 size; creates file if missing)\n";
echo "- 'a': Append only (pointer at end; creates file if missing)\n";
echo "- 'a+': Read/Append (pointer at end; creates file if missing)\n";
echo "- 'x': Create and write (fails with warning if file already exists)\n\n";

$handle = fopen($demoFile, 'r+');
if ($handle) {
    echo "Current pointer offset position: " . ftell($handle) . "\n";
    
    // Read first 10 bytes
    $chunk = fread($handle, 10);
    echo "Read 10 bytes: '$chunk'\n";
    echo "Current pointer offset position after read: " . ftell($handle) . "\n";
    
    // Seek to byte offset 15
    fseek($handle, 15);
    echo "Moved pointer manually to offset 15. Current position: " . ftell($handle) . "\n";
    
    // Rewind back to the start
    rewind($handle);
    echo "Rewound pointer. Current position: " . ftell($handle) . "\n";
    
    fclose($handle);
}
echo "\n";


echo "=== 4. THREAD-SAFE FILE LOCKING (flock) ===\n";
echo "flock() protects files from race conditions under concurrent operations:\n";

$lockHandle = fopen($demoFile, 'r+');
if ($lockHandle) {
    // Acquire an exclusive write lock (blocks other processes)
    // LOCK_SH: Shared lock (readers)
    // LOCK_EX: Exclusive lock (writers)
    // LOCK_UN: Release lock
    // LOCK_NB: Non-blocking (fail immediately if lock cannot be acquired)
    if (flock($lockHandle, LOCK_EX)) {
        echo "Exclusive lock acquired. Performing write...\n";
        ftruncate($lockHandle, 0); // Truncate file
        rewind($lockHandle);
        fwrite($lockHandle, "New thread-safe file contents.\n");
        fflush($lockHandle); // Flush output before releasing lock
        
        // Release lock
        flock($lockHandle, LOCK_UN);
        echo "Lock released successfully.\n";
    } else {
        echo "Could not lock the file.\n";
    }
    fclose($lockHandle);
}
echo "\n";


echo "=== 5. COMPILE-SAFE CSV WRITING & READING ===\n";

$csvData = [
    ['Name', 'Email', 'Role'],
    ['Clayton Souza', 'clayton@example.com', 'Developer'],
    ['Alice Smith', 'alice@example.com', 'Designer'],
    ['Bob Johnson', 'bob@example.com', 'Manager']
];

// 1. Write CSV
$csvWriteHandle = fopen($csvFile, 'w');
if ($csvWriteHandle) {
    foreach ($csvData as $row) {
        // fputcsv automatically formats fields, handles commas, and double quotes
        fputcsv($csvWriteHandle, $row, ',', '"');
    }
    fclose($csvWriteHandle);
    echo "CSV file successfully written to: " . basename($csvFile) . "\n";
}

// 2. Read CSV
$csvReadHandle = fopen($csvFile, 'r');
if ($csvReadHandle) {
    echo "Reading and parsing CSV records:\n";
    while (($row = fgetcsv($csvReadHandle, 1024, ',', '"')) !== false) {
        echo "- Name: {$row[0]} | Email: {$row[1]} | Role: {$row[2]}\n";
    }
    fclose($csvReadHandle);
}
echo "\n";


echo "=== 6. PATTERN MATCHING & FILE MUTATIONS ===\n";

// glob() finds pathnames matching a pattern
$phpFiles = glob($demoDir . '/*.{txt,csv}', GLOB_BRACE);
echo "Matching files in '$demoDir' (.txt or .csv):\n";
foreach ($phpFiles as $file) {
    echo "- " . basename($file) . "\n";
}

// Copy file
$copiedFile = $demoDir . '/sample_copied.txt';
copy($demoFile, $copiedFile);
echo "Copied " . basename($demoFile) . " to " . basename($copiedFile) . "\n";

// Rename file
$renamedFile = $demoDir . '/sample_renamed.txt';
rename($copiedFile, $renamedFile);
echo "Renamed copied file to " . basename($renamedFile) . "\n\n";


echo "=== 7. DEEP RECURSIVE DIRECTORY ITERATION ===\n";
echo "Scanning directory recursively using standard Object Oriented Iterators:\n";

// Create a subdirectory with a file to demonstrate depth
$subDir = $demoDir . '/subfolder';
if (!is_dir($subDir)) {
    mkdir($subDir, 0755, true);
}
file_put_contents($subDir . '/deep_file.txt', "Deep content\n");

// Recursively traverse directory
$directoryIterator = new RecursiveDirectoryIterator($demoDir, RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directoryIterator);

foreach ($iterator as $info) {
    echo "- RelPath: " . $iterator->getSubPathName() . " (Size: " . $info->getSize() . " bytes)\n";
}
echo "\n";


echo "=== 8. CLEAN UP ===\n";
echo "Removing created temporary files and folders...\n";

// Remove all files recursively to clean folder
foreach ($iterator as $info) {
    unlink($info->getPathname());
}
// Remove directories
rmdir($subDir);
rmdir($demoDir);

echo "Cleanup finished. Workspace is clean.\n";
?>
