<?php
/**
 * PHP Cheat Sheet - 48: Production Environment Optimization
 * 
 * Topics covered:
 * - OPcache Status: opcache_get_status(), caching checks, preloading
 * - PHP-FPM Pool Configurations: pm, max_children, start_servers tuning
 * - JIT Compilation Settings: opcache.jit, jit_buffer_size
 */

echo "=== 1. OPCACHE CONFIGURATION & METRICS ===\n";
$status = function_exists('opcache_get_status') ? opcache_get_status(false) : false;
$opcacheEnabled = is_array($status);

echo "OPcache Extension status: " . (function_exists('opcache_get_status') ? "Loaded" : "Not Loaded") . "\n";
echo "OPcache config setting (opcache.enable): " . (ini_get('opcache.enable') ? "On" : "Off") . "\n";

if ($opcacheEnabled && isset($status['memory_usage'], $status['opcache_statistics'])) {
    echo "OPcache Status Info:\n";
    echo "- Cache Full: " . ($status['cache_full'] ? 'Yes' : 'No') . "\n";
    echo "- Used Memory: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
    echo "- Free Memory: " . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . " MB\n";
    echo "- Hit Rate: " . round($status['opcache_statistics']['op_cache_hit_rate'], 2) . "%\n\n";
} else {
    echo "OPcache status: OPcache is not active or enabled on this runtime environment.\n\n";
}

echo "Recommended production `php.ini` Settings for OPcache:\n";
echo "```ini\n";
echo "opcache.enable=1\n";
echo "opcache.memory_consumption=256      ; MB allocated for code caching\n";
echo "opcache.interned_strings_buffer=16  ; MB allocated for interned strings\n";
echo "opcache.max_accelerated_files=20000 ; Max scripts to cache\n";
echo "opcache.validate_timestamps=0       ; 0 in production (never check files for changes)\n";
echo "opcache.revalidate_freq=0           ; ignored when validate_timestamps is 0\n";
echo "opcache.preload=/var/www/preload.php ; Preload classes at startup\n";
echo "```\n\n";


echo "=== 2. JIT COMPILATION (PHP 8+) ===\n";
echo "JIT (Just-In-Time) compiles bytecode to native machine CPU instructions.\n";
echo "JIT Status (opcache.jit): " . (ini_get('opcache.jit') ?: 'Disabled') . "\n";
echo "JIT Buffer Size (opcache.jit_buffer_size): " . (ini_get('opcache.jit_buffer_size') ?: '0') . "\n\n";

echo "Recommended JIT configurations for production:\n";
echo "```ini\n";
echo "opcache.jit=tracing                 ; tracing compiles hot paths, highly recommended\n";
echo "opcache.jit_buffer_size=128M        ; buffer allocated for compiled machine code\n";
echo "```\n";
echo "- Tracing JIT compiles loops and nested functions that execute frequently.\n";
echo "- Function JIT compiles entire functions, but is often slower than tracing.\n\n";


echo "=== 3. PHP-FPM POOL CONFIGURATION CHEATSHEET ===\n";
echo "PHP-FPM processes incoming requests. Selecting correct process managers is vital:\n\n";
echo "1. pm = static (Best for dedicated web servers; fixed memory usage)\n";
echo "   - Processes are started once and kept alive forever. No fork overhead.\n";
echo "   - Formula: max_children = (Total RAM - OS Buffer - DB Memory) / Avg process size (usually 30-50MB)\n\n";

echo "2. pm = dynamic (Ideal for shared/low-memory environments)\n";
echo "   - Scale up/down dynamically between limits.\n";
echo "   - Key variables:\n";
echo "     pm.max_children = 50       ; absolute maximum concurrent processes\n";
echo "     pm.start_servers = 10      ; servers launched on startup\n";
echo "     pm.min_spare_servers = 5   ; minimum spare servers kept idle\n";
echo "     pm.max_spare_servers = 20  ; maximum spare servers kept idle\n\n";

echo "3. pm = ondemand (Saves memory, but causes response latency on cold starts)\n";
echo "   - Starts processes only when requests arrive.\n";
echo "   - Key variables:\n";
echo "     pm.process_idle_timeout = 10s ; terminated if idle for 10 seconds\n";
