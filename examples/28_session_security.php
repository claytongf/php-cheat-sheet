<?php
/**
 * PHP Cheat Sheet - 28: Session Security & Custom Session Handlers
 * 
 * Topics covered:
 * - Securing Sessions (HTTPOnly, Secure, SameSite cookie settings)
 * - Session Hijacking protection via ID regeneration
 * - Custom Session Handlers (implementing SessionHandlerInterface)
 */

echo "=== 1. SECURE SESSION COOKIE SETTINGS ===\n";
echo "Before starting the session, configure session cookie settings for security:\n\n";

// Configuration options
$sessionParams = [
    'lifetime' => 3600,                     // Cookie expires in 1 hour
    'path' => '/',                          // Available across the whole site
    'domain' => '',                         // Default domain
    'secure' => true,                       // Send only over HTTPS (use true in production)
    'httponly' => true,                     // Prevents JS from reading session cookie (XSS protection)
    'samesite' => 'Strict'                  // CSRF protection (Strict, Lax, or None)
];

// PHP 7.3+ syntax to set session cookie parameters
// session_set_cookie_params($sessionParams);

echo "Configured settings array:\n";
print_r($sessionParams);


echo "\n=== 2. SESSION REGENERATION (Hijacking Protection) ===\n";
echo "Always regenerate the session ID during privilege changes (like login):\n\n";

/*
session_start();
// Regenerate session ID and delete the old session file
session_regenerate_id(true); 
*/
echo "Called: session_regenerate_id(true);\n";
echo "This prevents session fixation attacks by replacing the current ID with a new random one.\n";


echo "\n=== 3. CUSTOM SESSION HANDLERS (SessionHandlerInterface) ===\n";
echo "PHP allows storing session data in custom locations (databases, Redis) using handlers.\n";
echo "Below is a simulation of a memory-based session handler:\n\n";

class MemorySessionHandler implements SessionHandlerInterface {
    private array $storage = [];

    public function open(string $path, string $name): bool {
        echo ">> [SessionHandler] Open: path=$path, name=$name\n";
        return true;
    }

    public function close(): bool {
        echo ">> [SessionHandler] Close\n";
        return true;
    }

    public function read(string $id): string {
        echo ">> [SessionHandler] Read ID: $id\n";
        return $this->storage[$id] ?? '';
    }

    public function write(string $id, string $data): bool {
        echo ">> [SessionHandler] Write ID: $id | Data: $data\n";
        $this->storage[$id] = $data;
        return true;
    }

    public function destroy(string $id): bool {
        echo ">> [SessionHandler] Destroy ID: $id\n";
        unset($this->storage[$id]);
        return true;
    }

    public function gc(int $max_lifetime): int|false {
        echo ">> [SessionHandler] Garbage Collection: max_lifetime=$max_lifetime\n";
        return 0; // Number of deleted sessions
    }
}

// Instantiate and register custom handler
$handler = new MemorySessionHandler();

// Registering with PHP save handler
// session_set_save_handler($handler, true);

echo "Registering: session_set_save_handler(\$handler, true);\n\n";

// Simulating session read/write cycles programmatically
echo "Simulating session start...\n";
$handler->open('/var/lib/php/sessions', 'PHPSESSID');

$sessionId = 'mock-session-id-12345';
echo "Simulating writing variables (\$_SESSION['user_id'] = 42):\n";
$handler->write($sessionId, 'user_id|i:42;role|s:5:"admin";');

echo "\nSimulating reading back session variables:\n";
$dataRead = $handler->read($sessionId);
echo "Data read from session: $dataRead\n";

echo "\nSimulating session destroy (logout):\n";
$handler->destroy($sessionId);

$handler->close();
?>
