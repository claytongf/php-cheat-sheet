<?php
/**
 * PHP Cheat Sheet Dashboard
 * 
 * An interactive, premium web interface to browse, view, and run PHP cheat sheets.
 */

// Define directory of examples
$examplesDir = __DIR__ . '/examples';

// Clean filename to prevent path traversal vulnerability
function getSafeFilePath($file, $dir) {
    if (empty($file)) return null;
    $basename = basename($file);
    $path = $dir . '/' . $basename;
    if (file_exists($path) && is_file($path)) {
        return $path;
    }
    return null;
}

// Get list of example files dynamically
$examples = [];
if (is_dir($examplesDir)) {
    $files = scandir($examplesDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $examples[] = $file;
        }
    }
    sort($examples);
}

// AJAX API Endpoint to fetch code and output
if (isset($_GET['ajax']) && isset($_GET['file'])) {
    header('Content-Type: application/json');
    $safePath = getSafeFilePath($_GET['file'], $examplesDir);
    
    if (!$safePath) {
        echo json_encode(['success' => false, 'error' => 'File not found or access denied.']);
        exit;
    }

    $code = file_get_contents($safePath);
    
    // Capture execution output
    ob_start();
    try {
        // Isolate variables by running inside a closure
        $run = function($file) {
            // Disable session warning outputs if any headers are already sent
            $oldReporting = error_reporting();
            error_reporting($oldReporting & ~E_WARNING);
            include $file;
            error_reporting($oldReporting);
        };
        $run($safePath);
    } catch (Throwable $e) {
        echo "\n[Execution Error] " . $e->getMessage() . "\n";
        echo "Line " . $e->getLine() . " in file " . basename($e->getFile()) . "\n";
    }
    $output = ob_get_clean();

    echo json_encode([
        'success' => true,
        'filename' => basename($safePath),
        'code' => $code,
        'output' => $output ? $output : "Script executed successfully with no output."
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern PHP Interactive Cheat Sheet</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="An interactive PHP Cheat Sheet and playground showcasing core PHP concepts, syntax, and features with real-time execution.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🐘</text></svg>">
    
    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Highlight.js for IDE-like Syntax Highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    
    <style>
        :root {
            --bg-color: #0c0a1c;
            --sidebar-bg: rgba(18, 14, 38, 0.7);
            --card-bg: rgba(28, 23, 56, 0.45);
            --border-color: rgba(255, 255, 255, 0.08);
            --accent-primary: #8f5fe8;
            --accent-secondary: #00f2fe;
            --accent-gradient: linear-gradient(135deg, #8f5fe8 0%, #00f2fe 100%);
            --text-main: #f3f1f9;
            --text-muted: #9f9ab5;
            --editor-bg: #141126;
            --terminal-bg: #090812;
            --terminal-green: #39e38e;
            --terminal-blue: #00d4ff;
            --shadow-primary: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 10% 20%, rgba(143, 95, 232, 0.15) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(0, 242, 254, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Header block */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 40px;
            background: rgba(12, 10, 28, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            z-index: 100;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-section h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .logo-section span {
            background: rgba(143, 95, 232, 0.2);
            color: var(--accent-primary);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(143, 95, 232, 0.3);
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover {
            color: var(--accent-secondary);
        }

        /* Main structure */
        .container {
            display: flex;
            flex: 1;
            height: calc(100vh - 73px);
            overflow: hidden;
        }

        /* Sidebar Styling */
        aside {
            width: 320px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 24px;
            overflow-y: auto;
        }

        .search-container {
            margin-bottom: 20px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 36px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--accent-secondary);
            box-shadow: 0 0 10px rgba(0, 242, 254, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .sidebar-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin-bottom: 16px;
            font-weight: 700;
        }

        .category-group {
            margin-bottom: 24px;
        }

        .category-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--accent-secondary);
            font-weight: 700;
            margin-bottom: 12px;
            padding-left: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            user-select: none;
        }

        .category-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(0, 242, 254, 0.25), transparent);
        }

        .example-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .example-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            color: var(--text-muted);
            text-decoration: none;
        }

        .example-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border-color: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .example-item.active {
            background: var(--accent-gradient);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(143, 95, 232, 0.3);
        }

        .example-item .number {
            font-family: 'Fira Code', monospace;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 12px;
            opacity: 0.7;
        }

        .example-item .title {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Workspace Panels */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: rgba(10, 8, 20, 0.4);
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 32px;
            border-bottom: 1px solid var(--border-color);
            background: rgba(12, 10, 28, 0.2);
        }

        .selected-info h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .selected-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .run-btn {
            background: var(--accent-gradient);
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0, 242, 254, 0.25);
            transition: all 0.3s;
        }

        .run-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 242, 254, 0.4);
        }

        .run-btn:active {
            transform: translateY(0);
        }

        /* Code Viewer and Terminal side-by-side grid */
        .workspace-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            flex: 1;
            overflow: hidden;
        }

        .panel {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid var(--border-color);
        }

        .panel:last-child {
            border-right: none;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            background: rgba(12, 10, 28, 0.4);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
        }

        .panel-body {
            flex: 1;
            overflow: auto;
            position: relative;
        }

        /* Code display container */
        .code-container {
            background-color: var(--editor-bg);
            height: 100%;
            margin: 0;
            padding: 24px;
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            overflow: auto;
            white-space: pre;
            color: #b4b1c4;
        }

        .code-container code.hljs {
            background: transparent;
            padding: 0;
        }

        /* Terminal Output Container */
        .terminal-container {
            background-color: var(--terminal-bg);
            height: 100%;
            padding: 24px;
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            color: var(--text-main);
            overflow-y: auto;
            white-space: pre-wrap;
            position: relative;
        }

        .terminal-line {
            margin-bottom: 4px;
        }

        .terminal-welcome {
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .terminal-accent {
            color: var(--terminal-blue);
        }

        .terminal-output {
            color: var(--terminal-green);
        }

        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Loader Animation */
        .loader {
            display: none;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(12, 10, 28, 0.7);
            backdrop-filter: blur(4px);
            z-index: 10;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(143, 95, 232, 0.1);
            border-left-color: var(--accent-secondary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Empty state styling */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
            padding: 40px;
        }

        .empty-state svg {
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .workspace-grid {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr 1fr;
            }
            .container {
                height: auto;
                flex-direction: column;
            }
            aside {
                width: 100%;
                height: 250px;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo-section">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="url(#logoGrad)" stroke-width="2" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="url(#logoGrad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="url(#logoGrad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="logoGrad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#8f5fe8"/>
                        <stop offset="1" stop-color="#00f2fe"/>
                    </linearGradient>
                </defs>
            </svg>
            <h1>PHP Cheat Sheet</h1>
            <span>Interactive Playground</span>
        </div>
        <div class="nav-links">
            <a href="https://github.com" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                GitHub Repository
            </a>
        </div>
    </header>

    <div class="container">
        <!-- Sidebar -->
        <aside>
            <div class="sidebar-title">Topics & Code</div>
            
            <!-- Search Container -->
            <div class="search-container">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" class="search-input" id="search-bar" placeholder="Search topics (e.g. OOP, API, PDO)..." oninput="filterTopics()">
            </div>

            <?php 
                $keywordsMap = [
                    '01_basics' => 'tags comments output echo print var_dump variables constants define const',
                    '02_data_types' => 'string integer float boolean null casting check type strlen trim replace',
                    '03_control_flow' => 'condition if else switch match loop for while foreach break continue',
                    '04_functions' => 'parameter return default strict union named closure arrow splat variadic',
                    '05_arrays' => 'associative indexed destructuring keys values merge map filter reduce',
                    '06_oop' => 'class object visibility constructor inheritance abstract interface static trait',
                    '07_superglobals' => 'server get post session cookie files globals',
                    '08_error_handling' => 'try catch finally exception throw custom error handler warning',
                    '09_file_system' => 'file directory exist mkdir read write file_get_contents stream fopen flock csv fputcsv fgetcsv glob recursive pathinfo realpath',
                    '10_database' => 'pdo connection sqlite prepared statement insert select transaction commit rollback',
                    '11_pdo_advanced' => 'pdo join inner join left join group by count sum subquery transaction',
                    '12_mysqli' => 'mysqli oo procedural connection prepared bind_param bind_result transaction',
                    '13a_stdlib_strings' => 'string uppercase lowercase trim pad length split shuffle reverse wordwrap strip_tags html',
                    '13b_stdlib_arrays' => 'array count in_array key values push pop shift unshift merge chunk unique slice splice diff intersect sort asort ksort usort',
                    '13c_stdlib_math' => 'math absolute ceil floor round min max pow sqrt decbin hexdec rand mt_rand random_int bytes',
                    '13d_stdlib_datetime' => 'date time timezone unix timestamp microtime strtotime mktime gmdate DateTime DateTimeImmutable DateInterval DatePeriod',
                    '13e_stdlib_misc' => 'isset empty unset gettype is_null is_scalar is_numeric is_string is_int uniqid sleep function_exists class_exists ini_get ini_set',
                    '14_api_creation' => 'api rest json cors headers request method routing input status code',
                    '15_unit_testing' => 'unit testing phpunit assertions test lifecycle setup teardown mock',
                    '16_security' => 'security password hash verify xss htmlspecialchars sanitization validation csrf',
                    '17_composer_autoload' => 'composer composer.json autoload psr-4 spl_autoload_register namespace',
                    '18_attributes_reflection' => 'attribute reflection annotation metadata custom parsing',
                    '19_fibers' => 'fibers async concurrency suspend resume cooperative thread',
                    '20_regex' => 'regex regular expression pcre preg_match preg_match_all preg_replace split groups',
                    '21_enums_readonly' => 'enums enum unit backed readonly property class values php8.1 php8.2',
                    '22_http_requests' => 'http requests curl stream context header post get api fetch',
                    '23_cryptography' => 'cryptography encryption decryption openssl random bytes hash hmac sha256 signature aes-256-cbc',
                    '24_design_patterns' => 'design patterns singleton factory dependency injection container service resolver',
                    '25_xml_html_parsing' => 'xml html parsing simplexml domdocument domxpath xpath elements bookstore',
                    '26_generators' => 'generators generator yield memory foreach dataset co-routine send',
                    '27_cli_scripting' => 'cli command line interactive prompts prompt stdin stdout stderr argv argc exit status code',
                    '28_session_security' => 'session security custom handler save cookie params hijack regenerate SessionHandlerInterface',
                    '29_sending_emails' => 'emails mail smtp headers html mime phpmailer symfony mailer',
                    '30_file_uploads' => 'file upload size type mime validation move_uploaded_file $_FILES security',
                    '31_localization_intl' => 'localization internationalization intl locale numberformatter dateformatter messageformatter currency',
                    '32_image_processing_gd' => 'image graphics gd dynamic resize thumbnail draw shape font png jpeg colors',
                    '33_caching_apcu_redis' => 'caching optimization apcu redis store fetch keys ttl speed opcache',
                    '34_sockets_networking' => 'sockets tcp server client stream connection port address network protocol fsockopen',
                    '35_jwt_authentication' => 'jwt token auth encode decode sign verify hmac sha256 base64 stateless api header',
                    '36_benchmarking_performance' => 'benchmark performance profiling memory time hrtime microtime memory_get_usage gc garbage collection',
                    '37_advanced_cli' => 'cli terminal getopt argv prompt console colors ansi progress bar readline stdin stdout stderr',
                    '38_soap_graphql' => 'soap graphql wsdl client query variables client mutation api request json',
                    '39_pdf_excel_generation' => 'pdf excel stylesheet spreadsheets fpdf dompdf phpspreadsheet tables columns rows report',
                    '40_mail_attachments' => 'mail email sending raw headers attachments mime multipart boundary base64 encoding',
                    '41_queues_workers' => 'queues queue worker jobs background database locking select for update rabbitmq beanstalkd',
                    '42_weak_references' => 'weakreferences weakreference weakmap garbage collection object metadata caching gc memory',
                    '43_dns_network' => 'dns network utility checkdnsrr mx records gethostbyname ip2long long2ip resolution ip address',
                    '44_event_loop' => 'eventloop event loop reactive programming reactphp swoole async non-blocking stream timer',
                    '13f_stdlib_spl_datastructures' => 'spl stack queue fixedarray priorityqueue objectstorage data structures algorithms memory heap',
                    '13g_stdlib_output_buffering' => 'output buffering ob ob_start ob_get_clean ob_flush clean end templates buffer',
                    '13h_stdlib_urls_queries' => 'url query http_build_query parse_url parse_str urlencode rawurlencode query string',
                    '13i_stdlib_oop_file' => 'file oop splfileinfo splfileobject metadata stream lines write seek',
                    '13j_stdlib_json_serializable' => 'json json_encode json_decode jsonserializable serialization format options pretty print',
                    '45_process_control_pcntl' => 'process control pcntl fork posix sigterm sigint signals shmop shared memory daemon background IPC',
                    '46_system_execution' => 'system execution command shell_exec proc_open pipes passthru escapeshellarg escapeshellcmd subprocess',
                    '47_advanced_spl_iterators' => 'iterator spl arrayiterator limititerator callbackfilteriterator multipleiterator iteratoraggregate collection',
                    '48_production_optimization' => 'production optimization opcache jit preloading fpm php-fpm tuning performance pm max_children config',
                    '49_advanced_validation_filters' => 'validation filter sanitization filter_var filter_input filter_var_array email url ip validation flags'
                ];

                $categories = [
                    'basic' => [
                        'name' => 'Básico',
                        'files' => []
                    ],
                    'medium' => [
                        'name' => 'Médio',
                        'files' => []
                    ],
                    'advanced' => [
                        'name' => 'Avançado',
                        'files' => []
                    ]
                ];

                $basicKeys = [
                    '01_basics', '02_data_types', '03_control_flow', '04_functions', '05_arrays',
                    '06_oop', '07_superglobals', '08_error_handling', '09_file_system', '10_database'
                ];

                $advancedKeys = [
                    '18_attributes_reflection', '19_fibers', '23_cryptography', '24_design_patterns',
                    '26_generators', '31_localization_intl', '32_image_processing_gd',
                    '33_caching_apcu_redis', '34_sockets_networking', '35_jwt_authentication',
                    '36_benchmarking_performance', '37_advanced_cli', '41_queues_workers',
                    '42_weak_references', '44_event_loop', '13f_stdlib_spl_datastructures',
                    '13g_stdlib_output_buffering', '45_process_control_pcntl', '46_system_execution',
                    '48_production_optimization'
                ];

                foreach ($examples as $file) {
                    $fileKey = pathinfo($file, PATHINFO_FILENAME);
                    if (in_array($fileKey, $basicKeys)) {
                        $categories['basic']['files'][] = $file;
                    } elseif (in_array($fileKey, $advancedKeys)) {
                        $categories['advanced']['files'][] = $file;
                    } else {
                        $categories['medium']['files'][] = $file;
                    }
                }
                ?>
                <?php if (empty($examples)): ?>
                    <div style="color: var(--text-muted); font-size: 0.9rem; padding: 10px 0;">No examples found in examples/ folder.</div>
                <?php else: ?>
                    <?php foreach ($categories as $catKey => $catData): 
                        if (empty($catData['files'])) continue;
                    ?>
                        <div class="category-group" data-category="<?php echo $catKey; ?>">
                            <div class="category-header"><?php echo htmlspecialchars($catData['name']); ?></div>
                            <ul class="example-list">
                                <?php foreach ($catData['files'] as $file): 
                                    $fileKey = pathinfo($file, PATHINFO_FILENAME);
                                    $keywords = $keywordsMap[$fileKey] ?? '';
                                    $displayName = str_replace('_', ' ', $fileKey);
                                    $displayName = ucwords(preg_replace('/^\d+\s/', '', $displayName));
                                    $flatIndex = array_search($file, $examples);
                                    $formattedNumber = sprintf("%02d", $flatIndex + 1);
                                ?>
                                    <li>
                                        <div class="example-item" 
                                             data-filename="<?php echo htmlspecialchars($file); ?>" 
                                             data-keywords="<?php echo htmlspecialchars($keywords . ' ' . strtolower($displayName)); ?>"
                                             onclick="selectExample(this)">
                                            <span class="number"><?php echo $formattedNumber; ?></span>
                                            <span class="title"><?php echo htmlspecialchars($displayName); ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
        </aside>

        <!-- Main Content -->
        <main>
            <!-- Top bar showing selected file info & action button -->
            <div class="dashboard-header">
                <div class="selected-info">
                    <h2 id="selected-title">Welcome to the PHP Cheat Sheet</h2>
                    <p id="selected-subtitle">Select a topic from the sidebar to inspect and run the code</p>
                </div>
                <button class="run-btn" id="run-button" onclick="runCurrentScript()" disabled>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    Run Code
                </button>
            </div>

            <!-- Workspace side-by-side splits -->
            <div class="workspace-grid">
                <!-- Code Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <span>Source File Code</span>
                        <span id="file-extension-badge" style="color: var(--accent-secondary); font-family: monospace;">.php</span>
                    </div>
                    <div class="panel-body">
                        <div class="loader" id="code-loader">
                            <div class="spinner"></div>
                        </div>
                        <pre class="code-container"><code id="code-viewer" class="empty-state">
<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="11" y2="17"></line></svg>
Select a file from the sidebar list to inspect the PHP syntax and comments.
                        </code></pre>
                    </div>
                </div>

                <!-- Terminal Output Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <span>Terminal Execution Output</span>
                        <span style="color: var(--terminal-green); font-family: monospace;">STDOUT</span>
                    </div>
                    <div class="panel-body">
                        <div class="loader" id="terminal-loader">
                            <div class="spinner"></div>
                        </div>
                        <div class="terminal-container" id="terminal-viewer">
                            <div class="terminal-welcome">
<span class="terminal-accent">PHP Interactive Cheat Sheet Dashboard v1.0.0</span>
Host System: Local Web Server
Status: Ready to execute scripts

Select a script and click 'Run Code' to see immediate outputs here.
------------------------------------------------------------------------
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentFilename = null;

        // Escape HTML tags to prevent XSS and formatting issues in the viewer
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        async function selectExample(element) {
            // Remove active classes
            document.querySelectorAll('.example-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            const filename = element.getAttribute('data-filename');
            currentFilename = filename;

            // Update header title
            const titleText = element.querySelector('.title').innerText;
            document.getElementById('selected-title').innerText = titleText;
            document.getElementById('selected-subtitle').innerText = `examples/${filename}`;

            // Enable Run button
            document.getElementById('run-button').removeAttribute('disabled');

            // Load code
            await loadScriptDetails(filename, false); // Load only code, do not auto-run immediately
        }

        async function loadScriptDetails(filename, runCode = false) {
            const codeLoader = document.getElementById('code-loader');
            const termLoader = document.getElementById('terminal-loader');
            
            codeLoader.style.display = 'flex';
            if (runCode) {
                termLoader.style.display = 'flex';
            }

            try {
                const response = await fetch(`index.php?ajax=1&file=${encodeURIComponent(filename)}`);
                const data = await response.json();

                if (data.success) {
                    // Inject code
                    const codeViewer = document.getElementById('code-viewer');
                    codeViewer.classList.remove('empty-state');
                    codeViewer.textContent = data.code;
                    codeViewer.className = 'language-php';
                    hljs.highlightElement(codeViewer);

                    // Inject terminal output if runCode is true, or keep welcome
                    if (runCode) {
                        displayTerminalOutput(data.output);
                    }
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to the PHP server backend.');
            } finally {
                codeLoader.style.display = 'none';
                termLoader.style.display = 'none';
            }
        }

        function runCurrentScript() {
            if (!currentFilename) return;
            loadScriptDetails(currentFilename, true);
        }

        function displayTerminalOutput(output) {
            const terminal = document.getElementById('terminal-viewer');
            const timestamp = new Date().toLocaleTimeString();
            
            let html = `<div class="terminal-welcome"><span class="terminal-accent">[System ${timestamp}]</span> Executing examples/${currentFilename}...</div>`;
            html += `<div class="terminal-output">${escapeHtml(output)}</div>`;
            
            terminal.innerHTML = html;
            terminal.scrollTop = terminal.scrollHeight; // Scroll to bottom
        }

        function filterTopics() {
            const query = document.getElementById('search-bar').value.toLowerCase().trim();
            const categories = document.querySelectorAll('.category-group');
            
            categories.forEach(cat => {
                const items = cat.querySelectorAll('.example-item');
                let visibleCount = 0;
                
                items.forEach(item => {
                    const keywords = item.getAttribute('data-keywords') || '';
                    const filename = item.getAttribute('data-filename') || '';
                    const title = item.querySelector('.title').innerText.toLowerCase();
                    
                    if (query === "" || keywords.includes(query) || title.includes(query) || filename.toLowerCase().includes(query)) {
                        item.parentElement.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.parentElement.style.display = 'none';
                    }
                });
                
                if (visibleCount > 0 || query === "") {
                    cat.style.display = 'block';
                } else {
                    cat.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
