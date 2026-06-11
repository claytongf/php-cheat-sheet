<?php
/**
 * PHP Cheat Sheet - 13g: Output Buffering Controls
 * 
 * Topics covered:
 * - Basic Output Buffering: ob_start(), ob_get_contents(), ob_clean(), ob_end_clean()
 * - Clean captures: ob_get_clean()
 * - Flush & release: ob_flush(), flush(), ob_end_flush()
 * - Practical uses: Template rendering engine, header modification after echo
 */

echo "=== 1. CAPTURING SIMPLE OUTPUT ===\n";
// Start output buffering
ob_start();

echo "This string goes into the internal output buffer.\n";
echo "It won't be displayed immediately.\n";

// Get contents of buffer without cleaning/ending it
$bufferContent = ob_get_contents();

// Discard the buffer contents and turn off output buffering
ob_end_clean();

echo "Captured content length: " . strlen($bufferContent) . " bytes\n";
echo "Captured content was:\n---\n" . $bufferContent . "---\n";


echo "=== 2. SHORTHAND CLEAN & GET (ob_get_clean) ===\n";
ob_start();
?>
<div class="user-card">
    <h3>Hello, Clayton!</h3>
    <p>This is a simulated HTML template captured via output buffering.</p>
</div>
<?php
// ob_get_clean() is equivalent to calling ob_get_contents() followed by ob_end_clean()
$htmlOutput = ob_get_clean();

echo "HTML String Captured:\n" . trim($htmlOutput) . "\n\n";


echo "=== 3. PRACTICAL EXAMPLES: TEMPLATE RENDERER ===\n";
/**
 * Simple Template Renderer using Output Buffering
 */
function renderTemplate(string $templatePath, array $variables): string {
    if (!file_exists($templatePath)) {
        return "Template not found.";
    }

    // Extract variables to local scope
    extract($variables);

    ob_start();
    include $templatePath;
    return ob_get_clean();
}

// Create a temp template file
$tempTemplate = tempnam(sys_get_temp_dir(), 'tpl_');
file_put_contents($tempTemplate, 'Hello, <?= htmlspecialchars($name) ?>! Welcome to version <?= htmlspecialchars($version) ?>.');

$rendered = renderTemplate($tempTemplate, [
    'name' => 'World',
    'version' => '8.5'
]);
echo "Rendered Template: $rendered\n";

// Cleanup temp template
if (file_exists($tempTemplate)) {
    unlink($tempTemplate);
}
echo "\n";


echo "=== 4. NESTED BUFFERING ===\n";
// Output buffering can be nested
ob_start(); // Buffer Level 1
echo "Level 1 Start\n";

ob_start(); // Buffer Level 2
echo "Level 2 Content\n";
$level2 = ob_get_clean(); // Clears and closes Level 2

echo "Level 1 Mid (Level 2 was: " . trim($level2) . ")\n";
echo "Level 1 End\n";

$level1 = ob_get_clean();
echo "Final Output of Nested Buffers:\n" . $level1;
