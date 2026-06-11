<?php
/**
 * PHP Cheat Sheet - 42: WeakReferences and WeakMaps
 * 
 * Topics covered:
 * - Reference Counting vs Weak References
 * - Creating and resolving WeakReference instances (WeakReference::create)
 * - Using WeakMap (PHP 8.0) for binding metadata to objects
 * - Preventing memory leaks in registry/caching patterns
 */

echo "=== 1. UNDERSTANDING WEAKREFERENCES ===\n";
echo "Normally, assigning an object to a variable or array increases its reference count, preventing the Garbage Collector (GC) from releasing it.\n";
echo "A WeakReference allows you to keep a reference to an object without preventing it from being destroyed.\n\n";

class User {
    public string $name;
    public function __construct(string $name) {
        $this->name = $name;
    }
    public function __destruct() {
        echo "GC: Destroying User '{$this->name}' object from memory.\n";
    }
}

// 1. Create a strong reference
$user1 = new User("Clayton");

// 2. Create a weak reference to that object
$weakRef = WeakReference::create($user1);

echo "Resolving weak reference: " . ($weakRef->get() ? "Active object exists (User: " . $weakRef->get()->name . ")" : "Object has been deleted") . "\n";

echo "Unsetting the strong reference variable...\n";
// Unset the only strong reference
unset($user1); 

// The destructor should trigger here, and the weak reference will now resolve to null
echo "Resolving weak reference again: " . ($weakRef->get() ? "Active object exists" : "Object has been garbage collected (null)") . "\n\n";


echo "=== 2. THE WEAKMAP (PHP 8.0+) ===\n";
echo "A WeakMap is a key-value storage where keys are objects. If an object key has no other strong references, it is garbage collected and its entry is automatically removed from the WeakMap.\n";
echo "This is extremely useful for caching data associated with objects without causing memory leaks.\n\n";

if (!class_exists('WeakMap')) {
    echo "WeakMap is not supported in this version of PHP (requires PHP 8.0+).\n";
} else {
    // Creating a WeakMap
    $cacheMap = new WeakMap();
    
    // Creating object instances
    $invoice1 = new stdClass();
    $invoice1->id = 101;
    
    $invoice2 = new stdClass();
    $invoice2->id = 102;
    
    // Storing cache values keyed by object handles
    $cacheMap[$invoice1] = ['calculated_total' => 250.00, 'expires' => time() + 60];
    $cacheMap[$invoice2] = ['calculated_total' => 1200.00, 'expires' => time() + 60];
    
    echo "WeakMap entry count initially: " . count($cacheMap) . "\n";
    echo "Is invoice1 cached? " . (isset($cacheMap[$invoice1]) ? "Yes" : "No") . "\n";
    
    echo "Unsetting invoice1 strong reference variable...\n";
    unset($invoice1); // invoice1 is now dereferenced and garbage collected
    
    echo "WeakMap entry count now: " . count($cacheMap) . " (The entry was automatically removed!)\n";
    echo "Is invoice2 still cached? " . (isset($cacheMap[$invoice2]) ? "Yes (Total: " . $cacheMap[$invoice2]['calculated_total'] . ")" : "No") . "\n\n";
}


echo "=== 3. PRACTICAL REGISTRY CACHE COMPARISON ===\n";
echo "Standard caching arrays vs WeakMap caching arrays:\n\n";

$compareCode = '
// Memory Leak Pattern (Standard Array Caching):
class LeakServiceCache {
    private array $cache = [];
    public function calculateFor(object $obj) {
        $oid = spl_object_hash($obj);
        if (!isset($this->cache[$oid])) {
            $this->cache[$oid] = $this->expensiveCalculation($obj);
        }
        return $this->cache[$oid];
    }
    // Problem: The cache array keeps keys (or references) forever. 
    // Even if $obj is unset elsewhere, it cannot be GC\'d because of the cache hash.
}

// Memory Safe Pattern (WeakMap Caching):
class MemorySafeServiceCache {
    private WeakMap $cache;
    public function __construct() {
        $this->cache = new WeakMap();
    }
    public function calculateFor(object $obj) {
        if (!isset($this->cache[$obj])) {
            $this->cache[$obj] = $this->expensiveCalculation($obj);
        }
        return $this->cache[$obj];
    }
    // Benefit: Once the object key is unset, the cache entry is immediately cleared.
}
';

echo "Cache Pattern Comparison:\n" . trim($compareCode) . "\n";
?>
