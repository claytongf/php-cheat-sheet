<?php
/**
 * PHP Cheat Sheet - 24: Design Patterns in PHP
 * 
 * Topics covered:
 * - Singleton Pattern (Single class instance helper)
 * - Factory Pattern (Object instantiating separation)
 * - Dependency Injection Container (Service resolver container)
 */

// Utility separator
function printSeparator(): void {
    echo "\n" . str_repeat("-", 40) . "\n";
}


// --- 1. SINGLETON PATTERN ---
echo "=== 1. SINGLETON PATTERN ===\n";

class DatabaseConnection {
    private static ?DatabaseConnection $instance = null;
    private string $connectionId;

    // A private constructor prevents instantiation via 'new DatabaseConnection()'
    private function __construct() {
        $this->connectionId = "CONN-ID-" . uniqid();
    }

    // A private clone method prevents cloning of the instance
    private function __clone() {}

    // An unserialize method prevents deserialization (replaces deprecated __wakeup in PHP 8.5)
    public function __unserialize(array $data): void {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): DatabaseConnection {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    public function getConnectionId(): string {
        return $this->connectionId;
    }
}

$db1 = DatabaseConnection::getInstance();
$db2 = DatabaseConnection::getInstance();

echo "Database instance 1 Connection ID: " . $db1->getConnectionId() . "\n";
echo "Database instance 2 Connection ID: " . $db2->getConnectionId() . "\n";
echo "Are both instances identical? " . ($db1 === $db2 ? "YES (Singleton verified)" : "NO") . "\n";


printSeparator();


// --- 2. FACTORY PATTERN ---
echo "=== 2. FACTORY PATTERN ===\n";

interface Logger {
    public function log(string $message): void;
}

class FileLogger implements Logger {
    public function log(string $message): void {
        echo "[File Logger] Logging message: $message\n";
    }
}

class DatabaseLogger implements Logger {
    public function log(string $message): void {
        echo "[Database Logger] Logging message: $message\n";
    }
}

// Factory Class to encapsulate object creation logic
class LoggerFactory {
    public static function createLogger(string $type): Logger {
        return match (strtolower($type)) {
            'file' => new FileLogger(),
            'database' => new DatabaseLogger(),
            default => throw new InvalidArgumentException("Logger type '$type' not supported.")
        };
    }
}

$logger1 = LoggerFactory::createLogger('file');
$logger1->log("App initialized.");

$logger2 = LoggerFactory::createLogger('database');
$logger2->log("User logged in.");


printSeparator();


// --- 3. DEPENDENCY INJECTION (DI) CONTAINER ---
echo "=== 3. DEPENDENCY INJECTION CONTAINER ===\n";

// A simple container registers class instances or factories and resolves them when requested.
class Container {
    private array $services = [];

    // Register a service resolver callback
    public function set(string $name, callable $resolver): void {
        $this->services[$name] = $resolver;
    }

    // Retrieve and instantiate the service
    public function get(string $name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Service '$name' not found in container.");
        }
        
        // Execute the resolver callback
        return $this->services[$name]($this);
    }
}

// Define mock services
class MailService {
    public function send(string $to, string $msg): void {
        echo "[Mail Service] Sending mail to $to: '$msg'\n";
    }
}

class OrderProcessor {
    // OrderProcessor depends on MailService (Dependency Injection)
    public function __construct(private MailService $mailer) {}

    public function processOrder(int $orderId): void {
        echo "[Order Processor] Processing order ID #$orderId...\n";
        $this->mailer->send("customer@example.com", "Your order #$orderId is confirmed!");
    }
}

// Initialize Container
$container = new Container();

// Register MailService inside the container
$container->set(MailService::class, function() {
    return new MailService();
});

// Register OrderProcessor inside the container, automatically resolving MailService
$container->set(OrderProcessor::class, function(Container $c) {
    $mailServiceInstance = $c->get(MailService::class);
    return new OrderProcessor($mailServiceInstance);
});

// Retrieve and use services from the container
echo "Resolving OrderProcessor from DI Container:\n";
$processor = $container->get(OrderProcessor::class);
$processor->processOrder(9450);
?>
