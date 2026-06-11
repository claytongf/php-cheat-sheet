<?php
/**
 * PHP Cheat Sheet - 44: Event-Driven & Reactive Programming
 * 
 * Topics covered:
 * - Asynchronous single-threaded principles (Node.js style) in PHP
 * - Implementing a custom Event Loop in pure PHP (timers & ticks)
 * - Scheduling one-shot and periodic (intervals) callbacks
 * - Overview of react/event-loop (ReactPHP) and Swoole/RoadRunner engines
 */

echo "=== 1. ASYNCHRONOUS PROGRAMMING PRINCIPLES ===\n";
echo "Normally, PHP executes sequentially (blocking I/O). Event-driven frameworks (like ReactPHP or Swoole)\n";
echo "use a single-threaded 'Event Loop' to coordinate timers, network sockets, and file streams asynchronously:\n\n";

/**
 * Pure PHP Event Loop Implementation (Demystified)
 */
class SimpleEventLoop {
    private array $timers = [];
    private array $intervals = [];
    private bool $running = false;
    private float $startTime;

    public function __construct() {
        $this->startTime = microtime(true);
    }

    /**
     * Schedule a callback to run once after a delay (one-shot timer)
     */
    public function addTimer(float $delay, callable $callback): void {
        $this->timers[] = [
            'trigger_at' => microtime(true) + $delay,
            'callback' => $callback
        ];
    }

    /**
     * Schedule a callback to run repeatedly at fixed intervals
     */
    public function addPeriodicTimer(float $interval, callable $callback): void {
        $this->intervals[] = [
            'interval' => $interval,
            'next_trigger' => microtime(true) + $interval,
            'callback' => $callback
        ];
    }

    /**
     * Start the event loop cycle
     */
    public function run(): void {
        $this->running = true;
        
        echo "[EventLoop] Started loop execution cycle...\n";
        
        while ($this->running && (!empty($this->timers) || !empty($this->intervals))) {
            $now = microtime(true);
            
            // 1. Process One-shot Timers
            foreach ($this->timers as $key => $timer) {
                if ($now >= $timer['trigger_at']) {
                    // Fire callback
                    call_user_func($timer['callback']);
                    unset($this->timers[$key]); // Remove after execution
                }
            }
            
            // 2. Process Periodic Timers
            foreach ($this->intervals as $key => &$interval) {
                if ($now >= $interval['next_trigger']) {
                    // Fire callback
                    $keepRunning = call_user_func($interval['callback']);
                    
                    // If the callback returns false, stop the periodic timer
                    if ($keepRunning === false) {
                        unset($this->intervals[$key]);
                    } else {
                        $interval['next_trigger'] = microtime(true) + $interval['interval'];
                    }
                }
            }
            
            // 3. Sleep briefly to prevent 100% CPU core locking (tick interval)
            usleep(10000); // 10ms tick resolution
        }
        
        echo "[EventLoop] Stopped. No scheduled events remaining.\n";
    }

    public function stop(): void {
        $this->running = false;
    }
    
    public function getElapsedTime(): string {
        return number_format(microtime(true) - $this->startTime, 3) . "s";
    }
}

// Running the loop demonstration
$loop = new SimpleEventLoop();

// 1. Add one-shot timers
$loop->addTimer(0.1, function() use ($loop) {
    echo "[" . $loop->getElapsedTime() . "] Timer A fired after 100ms\n";
});

$loop->addTimer(0.3, function() use ($loop) {
    echo "[" . $loop->getElapsedTime() . "] Timer B fired after 300ms\n";
});

// 2. Add a periodic timer (runs every 100ms up to 3 times)
$counter = 0;
$loop->addPeriodicTimer(0.1, function() use (&$counter, $loop) {
    $counter++;
    echo "[" . $loop->getElapsedTime() . "] Periodic Timer tick #$counter\n";
    
    if ($counter >= 3) {
        echo "[" . $loop->getElapsedTime() . "] Cancelling periodic timer...\n";
        return false; // Stop the interval
    }
    return true; // Keep running
});

// Run the loop
$loop->run();
echo "\n";


echo "=== 2. PRODUCTION REACTIVE ENGINES ===\n";
echo "In real production PHP, instead of writing custom event loops, developers use:\n";
echo "1. ReactPHP: Component library (react/event-loop) written in pure PHP. Handles HTTP servers, DNS, and WebSockets.\n";
echo "2. Swoole: Highly optimized C extension for PHP. Implements native asynchronous coroutines, HTTP/WebSocket servers, and connection pooling with massive performance.\n";
echo "3. RoadRunner: High-performance PHP application server written in Go, acting as a load balancer/worker orchestrator.\n";
?>
