<?php
/**
 * PHP Cheat Sheet - 33: Caching & Performance Optimization
 * 
 * Topics covered:
 * - APCu (Alternative PHP Cache) user cache: store, fetch, delete, ttl
 * - Redis integration via the Redis extension (PECL Redis)
 * - Serialization patterns for caching complex objects
 * - OPcache (Bytecode Cache) overview and optimization configurations
 */

echo "=== 1. APCU (LOCAL MEMORY CACHE) ===\n";
echo "APCu stores variables directly in the web server shared memory, making it incredibly fast for local data caching.\n\n";

if (!extension_loaded('apcu')) {
    echo "APCu extension is not loaded. Simulating APCu cache behavior...\n";
    // Mock class/functions
    class APCuSimulator {
        private static $storage = [];
        public static function store($key, $value, $ttl = 0) {
            self::$storage[$key] = ['val' => $value, 'exp' => $ttl === 0 ? 0 : time() + $ttl];
            return true;
        }
        public static function fetch($key, &$success = null) {
            if (!isset(self::$storage[$key])) { $success = false; return false; }
            $item = self::$storage[$key];
            if ($item['exp'] !== 0 && time() > $item['exp']) {
                unset(self::$storage[$key]);
                $success = false;
                return false;
            }
            $success = true;
            return $item['val'];
        }
        public static function delete($key) { unset(self::$storage[$key]); return true; }
        public static function exists($key) { return isset(self::$storage[$key]); }
    }
    
    function apcu_store($key, $value, $ttl = 0) { return APCuSimulator::store($key, $value, $ttl); }
    function apcu_fetch($key, &$success = null) { return APCuSimulator::fetch($key, $success); }
    function apcu_delete($key) { return APCuSimulator::delete($key); }
    function apcu_exists($key) { return APCuSimulator::exists($key); }
} else {
    echo "APCu extension is active. Using native shared memory caching.\n";
}

// 1. Storing data in APCu (Key, Value, TTL in seconds)
$cacheKey = 'app_settings';
$settings = [
    'theme' => 'dark',
    'maintenance_mode' => false,
    'api_timeout' => 30
];

echo "Storing settings in APCu for 5 seconds...\n";
apcu_store($cacheKey, $settings, 5);

// 2. Fetching data from APCu
$success = false;
$cachedSettings = apcu_fetch($cacheKey, $success);

if ($success) {
    echo "Cache HIT! Settings fetched from APCu:\n";
    print_r($cachedSettings);
} else {
    echo "Cache MISS! Settings not found.\n";
}

// 3. Verifying key existence and deleting
echo "Does key exist? " . (apcu_exists($cacheKey) ? "Yes" : "No") . "\n";
apcu_delete($cacheKey);
echo "Deleted. Does key exist now? " . (apcu_exists($cacheKey) ? "Yes" : "No") . "\n\n";


echo "=== 2. REDIS (SHARED DISTRIBUTED CACHE) ===\n";
echo "Redis is an external in-memory key-value database, perfect for scaling across multiple servers (unlike APCu).\n\n";

if (!class_exists('Redis')) {
    echo "PECL Redis extension is not installed. Simulating Redis client...\n";
    // Mock Redis Class
    class Redis {
        private $storage = [];
        public function connect($host, $port) {
            echo "[Redis Mock] Connected to $host:$port\n";
            return true;
        }
        public function setex($key, $ttl, $value) {
            $this->storage[$key] = ['val' => $value, 'exp' => time() + $ttl];
            return true;
        }
        public function get($key) {
            if (!isset($this->storage[$key])) return false;
            $item = $this->storage[$key];
            if (time() > $item['exp']) {
                unset($this->storage[$key]);
                return false;
            }
            return $item['val'];
        }
    }
} else {
    echo "PECL Redis extension is active. Connecting to simulated client...\n";
}

// Redis typical integration pattern
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Caching query results as serialized JSON
$userSessionKey = 'session:user_419';
$userSessionData = [
    'user_id' => 419,
    'username' => 'clayton',
    'role' => 'admin',
    'permissions' => ['create', 'update', 'delete']
];

// Set with expiration (3600 seconds = 1 hour)
// setex($key, $ttl_seconds, $value)
$redis->setex($userSessionKey, 3600, json_encode($userSessionData));

// Fetch and decode JSON
$rawCache = $redis->get($userSessionKey);
if ($rawCache !== false) {
    $session = json_decode($rawCache, true);
    echo "Cache HIT! Session data loaded from Redis:\n";
    print_r($session);
} else {
    echo "Cache MISS! Session not found in Redis.\n";
}
echo "\n";


echo "=== 3. OPCACHE (BYTECODE OPTIMIZATION) ===\n";
echo "OPcache is PHP's official compiling cache engine. It compiles PHP scripts into executable bytecode and caches it in memory, bypassing the parser step on subsequent page loads.\n\n";
echo "Recommended production php.ini settings for maximum speed:\n";
echo "- opcache.enable=1             # Enable bytecode compiler cache\n";
echo "- opcache.memory_consumption=128 # Allocate 128MB shared RAM for bytecode\n";
echo "- opcache.interned_strings_buffer=8 # Cache common string literals (e.g. variable names)\n";
echo "- opcache.max_accelerated_files=10000 # Max PHP files allowed in cache memory\n";
echo "- opcache.revalidate_freq=0    # In production, do not check files for changes (frequency: 0)\n";
echo "- opcache.validate_timestamps=0 # Disable file checking (saves disk operations; requires server reload on deployments)\n";
?>
