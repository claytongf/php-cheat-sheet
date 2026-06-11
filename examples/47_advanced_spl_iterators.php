<?php
/**
 * PHP Cheat Sheet - 47: Advanced SPL Iterators
 * 
 * Topics covered:
 * - ArrayIterator: Wrap arrays to implement Iterator/ArrayAccess
 * - LimitIterator: Pagination/slicing loops without manual offsets
 * - CallbackFilterIterator: Filtering values on the fly with callback closures
 * - MultipleIterator: Zipping multiple lists together to iterate simultaneously
 * - IteratorAggregate: Creating custom traversable objects
 */

echo "=== 1. ARRAYITERATOR & LIMITITERATOR (Pagination) ===\n";
$fruits = ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry', 'Fig', 'Grape'];

$iterator = new ArrayIterator($fruits);

// LimitIterator: wraps any iterator and takes offset & count
// LimitIterator(Iterator, offset, count)
$paginated = new LimitIterator($iterator, 2, 3); // Start at index 2 (Cherry), return 3 items

echo "Displaying page contents (offset 2, count 3):\n";
foreach ($paginated as $index => $value) {
    echo " - Index $index: $value\n";
}
echo "\n";


echo "=== 2. CALLBACKFILTERITERATOR ===\n";
// Wrap ArrayIterator to filter out elements that don't match criteria
$evenLengthFruits = new CallbackFilterIterator($iterator, function(string $current, int $key, Iterator $innerIterator) {
    // Keep fruits with even name lengths
    return strlen($current) % 2 === 0;
});

echo "Fruits with even length names:\n";
foreach ($evenLengthFruits as $key => $fruit) {
    echo " - $fruit (length " . strlen($fruit) . ")\n";
}
echo "\n";


echo "=== 3. MULTIPLEITERATOR (Zipping Iterators) ===\n";
// Zips multiple iterators together to loop synchronously
$ids = new ArrayIterator([101, 102, 103]);
$names = new ArrayIterator(['Clayton', 'Alice', 'Bob']);
$roles = new ArrayIterator(['Developer', 'Designer', 'Manager']);

$mit = new MultipleIterator(MultipleIterator::MIT_NEED_ALL);
$mit->attachIterator($ids, 'id');
$mit->attachIterator($names, 'name');
$mit->attachIterator($roles, 'role');

echo "Iterating zipped lists:\n";
foreach ($mit as $values) {
    echo " - ID: {$values[0]} | Name: {$values[1]} | Role: {$values[2]}\n";
}
echo "\n";


echo "=== 4. CUSTOM ITERATORAGGREGATE ===\n";
// Implementing IteratorAggregate allows classes to define custom iteration
class DepartmentList implements IteratorAggregate {
    private array $departments = [];

    public function addDepartment(string $name, string $code): void {
        $this->departments[$code] = $name;
    }

    /**
     * Required method from IteratorAggregate interface
     */
    public function getIterator(): Traversable {
        // Return an ArrayIterator over the internal array
        return new ArrayIterator($this->departments);
    }
}

$deptList = new DepartmentList();
$deptList->addDepartment("Engineering", "ENG");
$deptList->addDepartment("Human Resources", "HR");
$deptList->addDepartment("Finance", "FIN");

echo "Iterating DepartmentList object directly:\n";
foreach ($deptList as $code => $name) {
    echo " - [$code] $name\n";
}
