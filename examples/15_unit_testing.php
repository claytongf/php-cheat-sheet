<?php

/**
 * PHP Cheat Sheet - 15: Unit Testing in PHP
 *
 * Topics covered:
 * - Introduction to PHPUnit (the industry standard testing framework)
 * - Writing Test Cases (Classes extending TestCase)
 * - Key Assertions (assertEquals, assertTrue, etc.)
 * - Setup and Teardown lifecycles (setUp, tearDown)
 * - Testing exceptions
 *
 * Note: Since PHPUnit requires external dependencies, this script implements
 * a lightweight mock testing runner to execute sample tests in stdout.
 */

// --- 1. THE CLASS WE WANT TO TEST ---

class Calculator
{
    public function add(float $a, float $b): float
    {
        return $a + $b;
    }

    public function divide(float $a, float $b): float
    {
        if ($b === 0.0) {
            throw new InvalidArgumentException('Cannot divide by zero.');
        }
        return $a / $b;
    }
}

// --- 2. THE REAL PHPUNIT TEST STRUCTURE (AS A COMMENTS REFERENCE) ---
/*
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    private Calculator $calculator;

    // Runs before each test method
    protected function setUp(): void {
        $this->calculator = new Calculator();
    }

    public function testAddNumbers(): void {
        $result = $this->calculator->add(2.5, 3.5);
        $this->assertEquals(6.0, $result);
    }

    public function testDivisionByZeroThrowsException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->calculator->divide(10, 0);
    }

    // Runs after each test method
    protected function tearDown(): void {
        unset($this->calculator);
    }
}
*/

// --- 3. MOCK UNIT TESTING ENGINE (FOR DEMONSTRATION RUN) ---

class MockAssert
{
    public static int $assertionsCount = 0;

    public static function assertEquals($expected, $actual, string $message = ''): void
    {
        self::$assertionsCount++;
        if ($expected !== $actual) {
            $expectedStr = var_export($expected, true);
            $actualStr = var_export($actual, true);
            throw new Exception("Assertion Failed: Expected $expectedStr, but got $actualStr. $message");
        }
    }

    public static function assertTrue($condition, string $message = ''): void
    {
        self::$assertionsCount++;
        if ($condition !== true) {
            throw new Exception("Assertion Failed: Condition is not true. $message");
        }
    }
}

// Our mock Test Case class representing PHPUnit's TestCase
class CalculatorTestCase
{
    private Calculator $calculator;

    // Simulation of setUp()
    public function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    // Test cases
    public function testAdditionSuccess(): void
    {
        $result = $this->calculator->add(10, 5);
        MockAssert::assertEquals(15.0, $result, 'Adding 10 + 5 should equal 15');
    }

    public function testAdditionDecimal(): void
    {
        $result = $this->calculator->add(1.5, 2.25);
        MockAssert::assertEquals(3.75, $result, 'Adding floats should yield exact float result');
    }

    public function testDivisionSuccess(): void
    {
        $result = $this->calculator->divide(20, 4);
        MockAssert::assertEquals(5.0, $result, '20 divided by 4 should equal 5');
    }

    public function testDivisionByZeroThrowsException(): void
    {
        try {
            $this->calculator->divide(10, 0);
            throw new Exception('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            MockAssert::assertTrue(true); // Exception was caught, test passed
        }
    }
}

// --- 4. RUNNING THE TEST SUITE ---

echo "=== RUNNING UNIT TEST SUITE (CalculatorTestCase) ===\n\n";

$testSuite = new CalculatorTestCase();
$methods = get_class_methods($testSuite);

$passed = 0;
$failed = 0;

foreach ($methods as $method) {
    // Only execute methods starting with "test"
    if (str_starts_with($method, 'test')) {
        try {
            // Run lifecycle setUp before each test
            $testSuite->setUp();

            // Execute test
            $testSuite->$method();

            echo "\033[32m[ PASS ]\033[0m $method\n";
            $passed++;
        } catch (Throwable $e) {
            echo "\033[31m[ FAIL ]\033[0m $method\n";
            echo '         -> ' . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

echo "\n----------------------------------------\n";
echo 'Tests Run: ' . ($passed + $failed) . " | Passed: $passed | Failed: $failed\n";
echo 'Assertions Made: ' . MockAssert::$assertionsCount . "\n";
