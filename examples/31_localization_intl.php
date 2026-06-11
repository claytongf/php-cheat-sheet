<?php
/**
 * PHP Cheat Sheet - 31: Internationalization (i18n) & Localization (l10n)
 * 
 * Topics covered:
 * - Checking and enabling the 'intl' extension
 * - Localizing numbers, currency, and percentages using NumberFormatter
 * - Localizing dates and times using IntlDateFormatter
 * - Message translations and complex pluralizations using MessageFormatter
 * - Locale-aware string sorting using Collator
 */

echo "=== 1. CHECKING FOR THE INTL EXTENSION ===\n";
if (!extension_loaded('intl')) {
    echo "The 'intl' (Internationalization) extension is not enabled in your PHP installation.\n";
    echo "To enable it, uncomment 'extension=intl' in php.ini or install via packages (e.g. apt-get install php-intl).\n\n";
    
    // Mock simulation classes if not available so the file compiles and demonstrates patterns
    class NumberFormatter {
        public const CURRENCY = 1;
        public const DECIMAL = 2;
        public const PERCENT = 3;
        private $locale;
        public function __construct($locale, $style) { $this->locale = $locale; }
        public function formatCurrency($val, $curr) {
            return ($this->locale === 'pt_BR' ? 'R$ ' : '$ ') . number_format($val, 2, ',', '.');
        }
        public function format($val) { return number_format($val, 2); }
    }
    class IntlDateFormatter {
        public const FULL = 1;
        public const MEDIUM = 2;
        public function __construct($locale, $d, $t) {}
        public function format($datetime) { return date('d/m/Y H:i:s', $datetime); }
    }
    class MessageFormatter {
        public static function formatMessage($locale, $pattern, $args) {
            return "Simulated message format output: " . json_encode($args);
        }
    }
    class Collator {
        public function __construct($locale) {}
        public function sort(&$arr) { sort($arr); }
    }
    echo "[Simulation Mode Enabled] Simulated classes will represent the output.\n\n";
} else {
    echo "The 'intl' extension is active. Using native PECL classes.\n\n";
}

echo "=== 2. LOCALIZING NUMBERS AND CURRENCIES (NumberFormatter) ===\n";

$value = 1234567.89;

// Brazilian Real (pt_BR)
$fmtBR = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
echo "pt_BR Currency: " . $fmtBR->formatCurrency($value, 'BRL') . "\n";

// US Dollar (en_US)
$fmtUS = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
echo "en_US Currency: " . $fmtUS->formatCurrency($value, 'USD') . "\n";

// Euro in Germany (de_DE)
$fmtDE = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);
echo "de_DE Currency: " . $fmtDE->formatCurrency($value, 'EUR') . "\n";

// Plain Numbers & Percentages
$decFmt = new NumberFormatter('fr_FR', NumberFormatter::DECIMAL);
echo "fr_FR Decimal: " . $decFmt->format($value) . "\n";

$pctFmt = new NumberFormatter('en_US', NumberFormatter::PERCENT);
echo "en_US Percentage: " . $pctFmt->format(0.854) . "\n\n";


echo "=== 3. LOCALIZING DATES AND TIMES (IntlDateFormatter) ===\n";

$now = time();

// Brazilian Date Format
$dateBR = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::MEDIUM
);
echo "pt_BR Full Date: " . $dateBR->format($now) . "\n";

// Japanese Date Format
$dateJP = new IntlDateFormatter(
    'ja_JP',
    IntlDateFormatter::FULL,
    IntlDateFormatter::FULL
);
echo "ja_JP Full Date: " . $dateJP->format($now) . "\n";

// English Date Format
$dateUS = new IntlDateFormatter(
    'en_US',
    IntlDateFormatter::MEDIUM,
    IntlDateFormatter::SHORT
);
echo "en_US Medium Date: " . $dateUS->format($now) . "\n\n";


echo "=== 4. PLURALIZATIONS AND MESSAGE TEMPLATES (MessageFormatter) ===\n";
echo "MessageFormatter resolves grammar matching rules for plurals dynamically:\n";

$pattern = "{0, name} has {1, plural, =0{no notifications} =1{one notification} other{# notifications}}.";

echo "- en_US 0: " . MessageFormatter::formatMessage('en_US', $pattern, ['Alice', 0]) . "\n";
echo "- en_US 1: " . MessageFormatter::formatMessage('en_US', $pattern, ['Bob', 1]) . "\n";
echo "- en_US 4: " . MessageFormatter::formatMessage('en_US', $pattern, ['Charlie', 4]) . "\n\n";


echo "=== 5. LOCALE-AWARE STRING COLLATING (Collator) ===\n";
echo "Standard PHP sort() sorts by ASCII values, causing accented chars (á, ç) to sort incorrectly in European alphabets.\n";

$words = ['maçã', 'banana', 'abacaxi', 'caju', 'amora', 'Água'];

echo "Before sorting:  " . implode(', ', $words) . "\n";

// Native sort
$standardSorted = $words;
sort($standardSorted);
echo "Standard sort(): " . implode(', ', $standardSorted) . " (Notice 'Água' placed at the end due to uppercase accented ASCII)\n";

// Collator locale-aware sort
$collator = new Collator('pt_BR');
$collatorSorted = $words;
$collator->sort($collatorSorted);
echo "Collator sort(): " . implode(', ', $collatorSorted) . " (Correct Portuguese alphabetical sorting!)\n";
?>
