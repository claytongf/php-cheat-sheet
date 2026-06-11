<?php
/**
 * PHP Cheat Sheet - 13d: Standard Library - Date & Time Functions
 * 
 * This file lists and runs essential built-in date, time, and timezone functions in PHP.
 */

function printTitle(string $title): void {
    echo "\n--- $title ---\n";
}

printTitle("1. TIMESTAMPS");

// time() - current Unix timestamp
$now = time();
echo "time() Output: $now\n";

// microtime() - current Unix timestamp with microseconds
echo "microtime() (string) Output: " . microtime() . "\n";
echo "microtime(true) (float) Output: " . microtime(true) . "\n";

// strtotime() - parses textual English date description into Unix timestamp
$target = "next Monday 15:30";
echo "strtotime('$target') Output: " . strtotime($target) . " (" . date("Y-m-d H:i:s", strtotime($target)) . ")\n";

// mktime() - gets Unix timestamp for a date (hour, minute, second, month, day, year)
echo "mktime(12, 0, 0, 12, 25, 2026) Output: " . mktime(12, 0, 0, 12, 25, 2026) . "\n";


printTitle("2. DATE FORMATTING & VALIDATION");

// date() - formats local time/date
echo "date('Y-m-d H:i:s') Output: " . date('Y-m-d H:i:s') . "\n";
echo "date('l, F j, Y') Output: " . date('l, F j, Y') . "\n";

// gmdate() - formats GMT/UTC date
echo "gmdate('Y-m-d H:i:s') Output: " . gmdate('Y-m-d H:i:s') . "\n";

// getdate() - gets date/time information array
echo "getdate() Output: ";
print_r(getdate());

// checkdate() - validates a Gregorian date (month, day, year)
echo "checkdate(2, 29, 2024) (Leap year) Output: " . (checkdate(2, 29, 2024) ? "Valid" : "Invalid") . "\n";
echo "checkdate(2, 29, 2025) (Common year) Output: " . (checkdate(2, 29, 2025) ? "Valid" : "Invalid") . "\n";


printTitle("3. TIMEZONES");

// date_default_timezone_get() - gets default timezone
echo "Current timezone: " . date_default_timezone_get() . "\n";

// date_default_timezone_set() - sets default timezone
date_default_timezone_set("America/Sao_Paulo");
echo "Changed timezone to America/Sao_Paulo. Current time: " . date('Y-m-d H:i:s') . "\n";
date_default_timezone_set("UTC"); // Reset back to UTC


printTitle("4. MODERN OOP DATE/TIME API");

// DateTime - mutable representation
$dateObj = new DateTime("2026-06-10 10:00:00");
$dateObj->modify("+2 days");
echo "DateTime + 2 days Output: " . $dateObj->format("Y-m-d H:i:s") . "\n";

// DateTimeImmutable - immutable representation (Recommended)
$immutableObj = new DateTimeImmutable("2026-06-10 10:00:00");
$newImmutable = $immutableObj->modify("+5 days"); // Returns a new object instead of modifying original
echo "DateTimeImmutable Original: " . $immutableObj->format("Y-m-d H:i:s") . "\n";
echo "DateTimeImmutable Modified: " . $newImmutable->format("Y-m-d H:i:s") . "\n";

// DateTimeZone - timezone objects
$tz = new DateTimeZone("Europe/Paris");
$localized = new DateTime("now", $tz);
echo "Time in Paris: " . $localized->format("Y-m-d H:i:s") . "\n";

// DateInterval - represents a duration
$interval = new DateInterval("P2M5D"); // Period of 2 Months and 5 Days
echo "Interval format: " . $interval->format("%m months and %d days") . "\n";

// DatePeriod - represents a sequence of dates
$start = new DateTime("2026-06-01");
$end = new DateTime("2026-06-15");
$step = new DateInterval("P5D"); // Every 5 days
$period = new DatePeriod($start, $step, $end);

echo "DatePeriod sequence:\n";
foreach ($period as $dt) {
    echo "- " . $dt->format("Y-m-d") . "\n";
}
?>
