<?php
/**
 * PHP Cheat Sheet - 06: Object-Oriented Programming
 * 
 * Topics covered:
 * - Classes and Objects
 * - PHP 8 Constructor Property Promotion
 * - Access Modifiers (public, protected, private)
 * - Inheritance & Abstract Classes
 * - Interfaces
 * - Static Properties & Methods
 * - Traits (Code Reusability)
 */

echo "=== 1. CLASS, PROPERTIES & METHODS ===\n";

// A simple interface defining a contract
interface Loggable {
    public function getLogString(): string;
}

// A parent abstract class
abstract class Product {
    protected string $sku;
    
    // PHP 8 Constructor Property Promotion allows declaring properties directly in parameters!
    public function __construct(
        protected string $name, 
        protected float $price
    ) {
        $this->sku = "SKU-" . uniqid();
    }
    
    // Abstract method must be implemented by subclasses
    abstract public function getDescription(): string;
    
    // Regular getter
    public function getPrice(): float {
        return $this->price;
    }
}


echo "\n=== 2. INHERITANCE & VISIBILITY ===\n";

// Concrete child class implementing Loggable interface
class Book extends Product implements Loggable {
    private string $author;

    public function __construct(string $name, float $price, string $author) {
        // Call the parent constructor
        parent::__construct($name, $price);
        $this->author = $author;
    }

    // Implementing the abstract method
    public function getDescription(): string {
        return "Book: '{$this->name}' by {$this->author} [SKU: {$this->sku}]";
    }

    // Implementing Loggable interface method
    public function getLogString(): string {
        return "[LOG] Book added: {$this->name} (\${$this->price})";
    }
}

// Create instance
$book = new Book("Clean Code", 49.99, "Robert C. Martin");
echo $book->getDescription() . "\n";
echo "Price: $" . $book->getPrice() . "\n";
echo $book->getLogString() . "\n";


echo "\n=== 3. STATIC MEMBERS ===\n";

class MathUtils {
    // Class Constant (always public/static implicitly, but can have visibility)
    public const PI = 3.14159265;
    
    public static int $calcCount = 0;

    public static function square(float $n): float {
        self::$calcCount++;
        return $n * $n;
    }
}

// Access static constant and method without instantiating the class
echo "PI Constant: " . MathUtils::PI . "\n";
echo "Square of 8: " . MathUtils::square(8) . "\n";
echo "Calculations count: " . MathUtils::$calcCount . "\n";


echo "\n=== 4. TRAITS (HORIZONTAL CODE SHARING) ===\n";

// Traits are a mechanism for code reuse in single inheritance languages like PHP.
trait Shareable {
    public function share(string $platform): void {
        echo "Sharing item on $platform...\n";
    }
}

class Article extends Product {
    use Shareable; // Injecting trait methods

    public function getDescription(): string {
        return "Article: '{$this->name}'";
    }
}

$article = new Article("Introduction to PHP 8", 0.0);
echo $article->getDescription() . "\n";
$article->share("Twitter");
?>
