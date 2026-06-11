<?php
/**
 * PHP Cheat Sheet - 25: XML & HTML Parsing
 * 
 * Topics covered:
 * - SimpleXML (Quick XML parsing & object mapping)
 * - DOMDocument (DOM tree navigation and node modification)
 * - DOMXPath (Querying nested nodes via XPath selectors)
 */

echo "=== 1. SimpleXML (Easy parsing) ===\n";

$xmlData = '<?xml version="1.0" encoding="UTF-8"?>
<bookstore>
    <book category="programming">
        <title lang="en">Clean Code</title>
        <author>Robert C. Martin</author>
        <price>39.99</price>
    </book>
    <book category="web">
        <title lang="en">Learning PHP 8</title>
        <author>Jane Doe</author>
        <price>29.99</price>
    </book>
</bookstore>';

// Parse XML string into a SimpleXMLElement object
$xml = simplexml_load_string($xmlData);

if ($xml !== false) {
    echo "Bookstore titles parsed:\n";
    foreach ($xml->book as $book) {
        // Access nested tags as properties, and attributes as array keys
        $category = (string)$book['category'];
        $title = (string)$book->title;
        $lang = (string)$book->title['lang'];
        $price = (float)$book->price;
        
        echo "- '$title' (Lang: $lang, Category: $category): \$$price\n";
    }
}


echo "\n=== 2. DOMDocument (DOM Tree Traversal) ===\n";

// XML/HTML document loading
$dom = new DOMDocument();
// Suppress warnings from potentially invalid markup syntax
libxml_use_internal_errors(true);
$dom->loadXML($xmlData);
libxml_clear_errors();

// Get elements by tag name
$books = $dom->getElementsByTagName('book');
echo "Found " . $books->length . " book tags in DOMDocument.\n";

if ($books->length > 0) {
    $firstBook = $books->item(0);
    // Find child node text content
    $titles = $firstBook->getElementsByTagName('title');
    if ($titles->length > 0) {
        echo "First book title node value: " . $titles->item(0)->nodeValue . "\n";
    }
}


echo "\n=== 3. DOMXPath (Targeted Node Querying) ===\n";

$xpath = new DOMXPath($dom);

// XPath query to select all titles of books in the 'programming' category
$queryStr = "/bookstore/book[@category='programming']/title";
$matchedNodes = $xpath->query($queryStr);

echo "Querying XPath: '$queryStr':\n";
if ($matchedNodes !== false) {
    foreach ($matchedNodes as $node) {
        echo "- Found node: " . $node->nodeValue . " (Lang attribute: " . $node->getAttribute('lang') . ")\n";
    }
}
?>
