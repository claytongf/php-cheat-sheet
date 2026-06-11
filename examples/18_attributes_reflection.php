<?php
/**
 * PHP Cheat Sheet - 18: Attributes and Reflection API
 * 
 * Topics covered:
 * - PHP 8.0 Attributes (Metadata annotations)
 * - Declaring Attribute classes using #[Attribute]
 * - Reading Attributes dynamically using the Reflection API (ReflectionClass, ReflectionMethod)
 */

echo "=== 1. DECLARING CUSTOM ATTRIBUTES ===\n";

// To make a class an attribute, mark it with #[Attribute]
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Route {
    public function __construct(
        public string $path,
        public string $method = 'GET'
    ) {}
}


echo "\n=== 2. ATTACHING ATTRIBUTES TO AN API CONTROLLER ===\n";

class UserController {
    
    #[Route('/users', 'GET')]
    public function listUsers(): void {
        echo "Executing listUsers...\n";
    }

    #[Route('/users', 'POST')]
    public function createUser(): void {
        echo "Executing createUser...\n";
    }

    public function helperMethod(): void {
        // No Route attribute attached
    }
}
echo "UserController defined. Attributes #[Route] attached to methods.\n";


echo "\n=== 3. PARSING METADATA VIA REFLECTION API ===\n";

// Instantiate the Reflection class for UserController
$reflection = new ReflectionClass(UserController::class);

echo "Scanning class: " . $reflection->getName() . "\n";
echo "Found routes:\n";

// Get all public methods of the class
$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

foreach ($methods as $method) {
    // Check if the method contains the Route attribute
    $attributes = $method->getAttributes(Route::class);
    
    if (!empty($attributes)) {
        // Get the first matched attribute instance
        $routeAttr = $attributes[0];
        
        // Instantiate the Attribute class to access its properties
        /** @var Route $route */
        $route = $routeAttr->newInstance();
        
        echo "- Method: {$method->getName()}()\n";
        echo "  Route Path: {$route->path}\n";
        echo "  HTTP Method: {$route->method}\n\n";
    }
}
?>
