<?php
/**
 * PHP Cheat Sheet - 13i: OOP File Handling with SplFileInfo & SplFileObject
 * 
 * Topics covered:
 * - SplFileInfo: Getting file metadata in an object-oriented way
 * - SplFileObject: Reading, writing, iterating, and seeking file streams as objects
 */

// Define temporary file path
$tempFilePath = sys_get_temp_dir() . '/spl_file_demo.txt';

// Cleanup if it already exists
if (file_exists($tempFilePath)) {
    unlink($tempFilePath);
}

echo "=== 1. SPL FILE INFO (Metadata) ===\n";
// Create sample file
file_put_contents($tempFilePath, "First Line\nSecond Line\nThird Line of SPL text\n");

// Instantiating SplFileInfo
$fileInfo = new SplFileInfo($tempFilePath);

echo "File Details via SplFileInfo:\n";
echo "- Real path: " . $fileInfo->getRealPath() . "\n";
echo "- Basename: " . $fileInfo->getBasename() . "\n";
echo "- Extension: " . $fileInfo->getExtension() . "\n";
echo "- Type: " . $fileInfo->getType() . "\n";
echo "- Size: " . $fileInfo->getSize() . " bytes\n";
echo "- Readable: " . ($fileInfo->isReadable() ? 'Yes' : 'No') . "\n";
echo "- Writable: " . ($fileInfo->isWritable() ? 'Yes' : 'No') . "\n";
echo "- Link: " . ($fileInfo->isLink() ? 'Yes' : 'No') . "\n\n";


echo "=== 2. SPL FILE OBJECT (Reading & Iterating) ===\n";
// SplFileObject extends SplFileInfo and implements Iterator, SeekableIterator
$fileObj = new SplFileObject($tempFilePath, 'r');

echo "Iterating lines directly using foreach:\n";
foreach ($fileObj as $lineNumber => $lineContent) {
    // Note: $lineNumber is 0-indexed
    echo "Line " . ($lineNumber + 1) . ": " . trim($lineContent) . "\n";
}
echo "\n";


echo "=== 3. SEEKING & READING SPECIFIC LINES ===\n";
// Seek to line index 1 (the 2nd line, since index is 0-based)
$fileObj->seek(1);
echo "Seeked to index 1. Current line contents: " . trim($fileObj->current()) . "\n";

// Go back to the beginning
$fileObj->rewind();
echo "Rewound. First line: " . trim($fileObj->fgets()) . "\n\n";


echo "=== 4. WRITING FILES (SplFileObject) ===\n";
$writeFilePath = sys_get_temp_dir() . '/spl_write_demo.txt';
$writeFileObj = new SplFileObject($writeFilePath, 'w+');

// Write data
$bytesWritten = $writeFileObj->fwrite("Line 1: Written via SplFileObject\n");
echo "Bytes written: $bytesWritten\n";

// Format as CSV (PHP 8.5 requires the escape parameter to be explicitly set)
$writeFileObj->fputcsv(['clayton', 'developer', 'php-8.5'], escape: '\\');

// Rewind and read back
$writeFileObj->rewind();
echo "Reading back file contents:\n";
while (!$writeFileObj->eof()) {
    $line = $writeFileObj->fgets();
    if ($line !== '') {
        echo " - " . trim($line) . "\n";
    }
}

// Close and cleanup
unset($fileObj);
unset($writeFileObj);

if (file_exists($tempFilePath)) {
    unlink($tempFilePath);
}
if (file_exists($writeFilePath)) {
    unlink($writeFilePath);
}
