<?php
/**
 * PHP Cheat Sheet - 30: File Uploads & Validation
 * 
 * Topics covered:
 * - Handling uploads via $_FILES superglobal
 * - Understanding upload error codes (UPLOAD_ERR_OK, UPLOAD_ERR_INI_SIZE, etc.)
 * - Secure validations: File size limit, MIME-type inspection via Fileinfo
 * - Safe filename generation (preventing directory traversal and filename collisions)
 * - Using move_uploaded_file() safely
 * - Best practices for upload directory security (blocking script execution)
 */

echo "=== 1. UNDERSTANDING THE \$_FILES ARRAY ===\n";
echo "When a file is uploaded via a POST form with enctype=\"multipart/form-data\", PHP populates \$_FILES:\n";
echo "Structure of \$_FILES['my_file']:\n";
echo "- ['name']: Original name of the file on the client machine (untrusted!)\n";
echo "- ['type']: MIME type provided by the browser (untrusted!)\n";
echo "- ['tmp_name']: Temporary file path stored on the server\n";
echo "- ['error']: Upload error code (0 means success)\n";
echo "- ['size']: File size in bytes\n\n";

// Simulating a file upload payload for runtime execution in dashboard
if (empty($_FILES)) {
    echo "[Simulation Mode] Simulating a file upload payload...\n";
    
    // Create a temporary mock file
    $mockTmpFile = tempnam(sys_get_temp_dir(), 'php_upload_');
    file_put_contents($mockTmpFile, "<?php echo 'malicious code'; // disguised text file");
    
    $_FILES['avatar'] = [
        'name' => 'attacker_profile.php.txt',
        'type' => 'text/plain',
        'tmp_name' => $mockTmpFile,
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];
}

$uploadedFile = $_FILES['avatar'];

echo "Uploaded File Details:\n";
print_r($uploadedFile);
echo "\n";

echo "=== 2. SECURE UPLOAD VALIDATION FLOW ===\n";

// Constants for validation
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_MIMES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

function validateAndUpload(array $file, string $destinationDir): array {
    $errors = [];
    
    // Step 1: Check upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'message' => 'File size exceeds maximum upload limits.'];
            case UPLOAD_ERR_PARTIAL:
                return ['success' => false, 'message' => 'File upload was interrupted.'];
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'message' => 'No file was uploaded.'];
            default:
                return ['success' => false, 'message' => 'Unknown upload error occurred.'];
        }
    }
    
    // Step 2: Validate size limit
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "File size exceeds the limit of 2MB (Actual size: " . round($file['size'] / 1024, 2) . " KB)";
    }
    
    // Step 3: Validate MIME-Type securely (DO NOT trust $file['type'])
    // Inspect actual file bytes using the standard Fileinfo extension
    if (!extension_loaded('fileinfo')) {
        // Fallback if extension is not installed
        $realMime = $file['type']; // Fallback (less secure)
        echo "Warning: Fileinfo extension not available. Falling back to browser-supplied MIME type.\n";
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);
    }
    
    echo "Actual detected MIME-Type: $realMime\n";
    if (!in_array($realMime, ALLOWED_MIMES)) {
        $errors[] = "Invalid MIME-type detected: '$realMime'. Only JPEG, PNG, and GIF images are allowed.";
    }
    
    // Step 4: Validate file extension
    $pathInfo = pathinfo($file['name']);
    $extension = strtolower($pathInfo['extension'] ?? '');
    
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $errors[] = "Invalid file extension: '.$extension'.";
    }
    
    // Double extension protection: e.g. "image.php.jpg"
    // Inspect filename segments to ensure malicious double extensions are flagged or stripped
    if (preg_match('/\.(php|phtml|php3|php4|php5|php7|php8|phar|pl|py|cgi|asp|aspx)\./i', $file['name'])) {
        $errors[] = "Potential double extension vulnerability detected in filename.";
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Step 5: Generate secure, unique filename (Sanitization)
    // - Convert original name to a safe slug
    $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $pathInfo['filename']);
    // - Add random token to prevent filename collisions and guessing
    $uniqueName = $safeBaseName . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $targetPath = $destinationDir . '/' . $uniqueName;
    
    echo "Sanitized Target Path: $targetPath\n";
    
    // Step 6: Move file to upload directory
    // move_uploaded_file() checks to ensure the file is indeed a valid uploaded file
    // In our simulation mode, we mock this because the file is temp-generated
    if (isset($file['simulation']) || php_sapi_name() === 'cli' || strpos($file['tmp_name'], 'php_upload_') !== false) {
        echo "[Simulation] Successfully moved upload to: $targetPath\n";
        return ['success' => true, 'filename' => $uniqueName];
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $uniqueName];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file. Check directory permissions.'];
}

// Target folder simulation
$uploadDirectory = sys_get_temp_dir() . '/my_uploads';
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0755, true);
}

// Running the upload check
$result = validateAndUpload($uploadedFile, $uploadDirectory);

echo "\nUpload Result:\n";
print_r($result);

// Clean up simulated file
if (file_exists($uploadedFile['tmp_name'])) {
    @unlink($uploadedFile['tmp_name']);
}

echo "\n=== 3. CRITICAL SECURITY RECOMMENDATIONS ===\n";
echo "1. Disable script execution: Add an .htaccess file in the upload directory to prevent script execution:\n";
echo "   `RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .php8` \n";
echo "   `Engine off` (for Apache modules)\n";
echo "2. Keep uploads outside document root: Store uploaded files outside of the public HTTP directory (e.g. in /var/private/uploads) and serve them using a readfile() PHP routing script.\n";
echo "3. Regenerate filename: Always generate a new random name instead of using user input to prevent path traversal exploits (e.g. '../../index.php').\n";
?>
