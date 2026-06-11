# Interactive PHP Cheat Sheet & Playground 🐘

Welcome to the **Interactive PHP Cheat Sheet and Playground**! This repository is designed to be a comprehensive, easy-to-use learning resource and reference guide for core PHP concepts (from basic syntax to object-oriented programming and database connectivity).

It features a **modern, premium web-based dashboard** where you can read, analyze, and instantly execute each cheat sheet script right inside your browser.

---

## 🚀 Getting Started

No complex server setup (like Apache or Nginx) is required! You can run this repository instantly using either PHP's built-in web server or Docker.

### Option A: Using PHP's Built-in Server

#### Prerequisites
- PHP 8.0 or higher installed on your system.
- PDO SQLite extension enabled (usually enabled by default in standard PHP installations).

#### Running the Dashboard
1. Clone this repository to your local machine.
2. Open your terminal in the project root directory.
3. Start the PHP built-in development server:
   ```bash
   php -S localhost:8000
   ```
4. Open your browser and navigate to:
   [http://localhost:8000](http://localhost:8000)

### Option B: Using Docker (Recommended for complete extension support)

#### Prerequisites
- Docker and Docker Compose installed.

#### Running the Dashboard
1. Clone this repository to your local machine.
2. Run Docker Compose to build and start the server:
   ```bash
   docker compose up -d --build
   ```
3. Open your browser and navigate to:
   [http://localhost:8080](http://localhost:8080)
4. (Optional) To view logs or stop the container environment:
   ```bash
   docker compose logs -f
   docker compose down
   ```

---

## 📂 Repository Structure

The cheat sheet scripts are organized numerically inside the `examples/` directory for sequential learning:

```
php-cheat-sheet/
├── Dockerfile          # PHP Apache environment container definition
├── docker-compose.yml  # Local orchestrator exposing dashboard on port 8080
├── README.md           # This documentation file
├── index.php           # The interactive playground dashboard
└── examples/           # PHP Cheat Sheet scripts
    ├── 01_basics.php
    ├── 02_data_types.php
    ├── 03_control_flow.php
    ├── 04_functions.php
    ├── 05_arrays.php
    ├── 06_oop.php
    ├── 07_superglobals.php
    ├── 08_error_handling.php
    ├── 09_file_system.php
    ├── 10_database.php
    ├── 11_pdo_advanced.php
    ├── 12_mysqli.php
    ├── 13a_stdlib_strings.php
    ├── 13b_stdlib_arrays.php
    ├── 13c_stdlib_math.php
    ├── 13d_stdlib_datetime.php
    ├── 13e_stdlib_misc.php
    ├── 14_api_creation.php
    ├── 15_unit_testing.php
    ├── 16_security.php
    ├── 17_composer_autoload.php
    ├── 18_attributes_reflection.php
    ├── 19_fibers.php
    ├── 20_regex.php
    ├── 21_enums_readonly.php
    ├── 22_http_requests.php
    ├── 23_cryptography.php
    ├── 24_design_patterns.php
    ├── 25_xml_html_parsing.php
    ├── 26_generators.php
    ├── 27_cli_scripting.php
    ├── 28_session_security.php
    ├── 29_sending_emails.php
    ├── 30_file_uploads.php
    ├── 31_localization_intl.php
    ├── 32_image_processing_gd.php
    ├── 33_caching_apcu_redis.php
    ├── 34_sockets_networking.php
    ├── 35_jwt_authentication.php
    ├── 36_benchmarking_performance.php
    ├── 37_advanced_cli.php
    ├── 38_soap_graphql.php
    ├── 39_pdf_excel_generation.php
    ├── 40_mail_attachments.php
    ├── 41_queues_workers.php
    ├── 42_weak_references.php
    ├── 43_dns_network.php
    └── 44_event_loop.php
```

---

## 📖 Topics Covered

### [01. Basics](https://www.github.com/claytongf/php-cheat-sheet/examples/01_basics.php)
- Tags opening (`<?php`), comments (`//`, `#`, `/* */`).
- Output mechanisms (`echo`, `print`, `var_dump`, `print_r`).
- Variables declaration, scope, and interpolation.
- Constants defined via `define()` and the `const` keyword.

### [02. Data Types](https://www.github.com/claytongf/php-cheat-sheet/examples/02_data_types.php)
- Core scalars (`string`, `int`, `float`, `bool`) and special types (`null`).
- Explicit type casting.
- Type checking helpers (`is_int()`, `is_string()`, `is_null()`).
- Standard string manipulation functions (`strlen`, `trim`, `strtoupper`, `str_replace`, `substr`).

### [03. Control Flow](https://www.github.com/claytongf/php-cheat-sheet/examples/03_control_flow.php)
- Branching conditions (`if`, `elseif`, `else`).
- Ternary operator (`? :`) and Null Coalescing operator (`??`).
- Traditional `switch-case` versus the modern PHP 8 `match` expression.
- Loops (`for`, `while`, `do-while`, `foreach` over associative arrays).
- Loop flow interruption (`break`, `continue`).

### [04. Functions](https://www.github.com/claytongf/php-cheat-sheet/examples/04_functions.php)
- Default arguments, return type hints, and parameter type declarations.
- Modern Union types (`int|string|float`) and Named arguments.
- Anonymous functions (closures) and scope importing via the `use` keyword.
- Arrow functions (`fn() => ...`).
- Variadic parameters with the splat operator (`...`).

### [05. Arrays](https://www.github.com/claytongf/php-cheat-sheet/examples/05_arrays.php)
- Indexed vs. Associative arrays.
- Array destructuring syntax (`[...] = $array`).
- Core utilities: `in_array()`, `array_key_exists()`, `array_keys()`, `array_values()`, and `array_merge()`.
- Functional array processing: `array_map()`, `array_filter()`, and `array_reduce()`.

### [06. OOP (Object-Oriented Programming)](https://www.github.com/claytongf/php-cheat-sheet/examples/06_oop.php)
- Classes, objects, properties, and methods.
- Access modifiers (`public`, `protected`, `private`).
- PHP 8 constructor property promotion.
- Inheritance (`extends`), abstract classes, and Interfaces (`implements`).
- Static properties, methods, and constants (`self::`, `ClassName::`).
- Traits for horizontal code sharing (`use TraitName`).

### [07. Superglobals](https://www.github.com/claytongf/php-cheat-sheet/examples/07_superglobals.php)
- Interactive explanation of PHP's global array variables: `$_SERVER`, `$_GET`, `$_POST`, `$_SESSION`, `$_COOKIE`, and `$_FILES`.

### [08. Error & Exception Handling](https://www.github.com/claytongf/php-cheat-sheet/examples/08_error_handling.php)
- Catching exceptions using `try-catch-finally`.
- Throwing exceptions and custom exception classes.
- Multiple `catch` blocks for different exception hierarchies.
- Setting custom global error handlers with `set_error_handler()`.

### [09. File System](https://www.github.com/claytongf/php-cheat-sheet/examples/09_file_system.php)
- File permission checks (`is_readable`, `is_writable`) and metadata retrieval (`filesize`, `filemtime`, `pathinfo`, `realpath`).
- Quick reading/writing methods (`file_get_contents`, `file_put_contents`, `file`).
- Stream pointer controls (`fopen` modes, `fread`, `fwrite`, `fseek`, `ftell`, `rewind`).
- Safe concurrent file locking (`flock`).
- Parsing and writing CSV files (`fputcsv`, `fgetcsv`).
- File mutations (`copy`, `rename`, `unlink`) and glob file matching (`glob`).
- Modern recursive directory traversal (`RecursiveDirectoryIterator` and `RecursiveIteratorIterator`).

### [10. Database Access (PDO)](https://www.github.com/claytongf/php-cheat-sheet/examples/10_database.php)
- Connecting securely to databases via PDO.
- Setting exception modes and associative fetch formats.
- Executing table schemas.
- Prepared statements with named parameters to prevent SQL injection.
- Fetching single or multiple rows.
- Atomic SQL execution via database Transactions (`beginTransaction`, `commit`, `rollBack`).

### [11a. Standard Library - String Functions](https://www.github.com/claytongf/php-cheat-sheet/examples/11a_stdlib_strings.php)
- Metadata & Splitting: `strlen()`, `str_word_count()`, `str_split()`, `str_repeat()`.
- Case Shifting: `strtoupper()`, `strtolower()`, `ucfirst()`, `lcfirst()`, `ucwords()`.
- Trimming & Padding: `trim()`, `ltrim()`, `rtrim()`, `str_pad()`.
- Substrings & Searching: `strpos()`, `stripos()`, `strrpos()`, `strstr()`, `strchr()`, `substr()`.
- Editing & Replacing: `str_replace()`, `str_ireplace()`, `str_shuffle()`, `strrev()`.
- HTML & Formatting: `strip_tags()`, `nl2br()`, `wordwrap()`.

### [11b. Standard Library - Array Functions](https://www.github.com/claytongf/php-cheat-sheet/examples/11b_stdlib_arrays.php)
- Sizing & Key Checking: `count()`, `is_array()`, `in_array()`, `array_key_exists()`, `array_keys()`, `array_values()`.
- Stack & Queue operations: `array_push()`, `array_pop()`, `array_shift()`, `array_unshift()`.
- Slice, Combine & Merges: `array_merge()`, `array_combine()`, `array_slice()`, `array_splice()`, `array_chunk()`, `array_unique()`, `array_reverse()`, `array_flip()`.
- Iteration & Search: `array_search()`, `array_key_first()`, `array_key_last()`, `array_map()`, `array_filter()`, `array_reduce()`.
- Set actions: `array_diff()`, `array_intersect()`.
- Sort variations: `sort()`, `rsort()`, `asort()`, `arsort()`, `ksort()`, `krsort()`, `usort()`.

### [11c. Standard Library - Math & Numeric Functions](https://www.github.com/claytongf/php-cheat-sheet/examples/11c_stdlib_math.php)
- Rounding: `abs()`, `ceil()`, `floor()`, `round()`.
- Min/Max, Square Root & Powers: `min()`, `max()`, `pow()`, `sqrt()`.
- Base Converter: `decbin()`, `bindec()`, `dechex()`, `hexdec()`.
- Float Checks: `fmod()`, `is_nan()`, `is_infinite()`.
- Cryptographic & Pseudo-randomness: `rand()`, `mt_rand()`, `random_int()`, `random_bytes()`.

### [11d. Standard Library - Date & Time Functions](https://www.github.com/claytongf/php-cheat-sheet/examples/11d_stdlib_datetime.php)
- Unix timestamps & parsing: `time()`, `microtime()`, `strtotime()`, `mktime()`.
- Formatting & Gregorian Check: `date()`, `gmdate()`, `getdate()`, `checkdate()`.
- Timezone management: `date_default_timezone_get()`, `date_default_timezone_set()`.
- Modern OOP Date Time API: `DateTime`, `DateTimeImmutable`, `DateTimeZone`, `DateInterval`, `DatePeriod`.

### [11e. Standard Library - Miscellaneous Functions](https://www.github.com/claytongf/php-cheat-sheet/examples/11e_stdlib_misc.php)
- Variable checking: `isset()`, `empty()`, `unset()`, `gettype()`.
- Type checking: `is_null()`, `is_scalar()`, `is_numeric()`, `is_string()`, `is_int()`, `is_callable()`.
- Process & System: `uniqid()`, `usleep()`.
- Dynamic checks: `function_exists()`, `class_exists()`, `extension_loaded()`, `defined()`.
- INI settings: `ini_get()`, `ini_set()`.

### [12. Advanced PDO (PHP Data Objects)](https://www.github.com/claytongf/php-cheat-sheet/examples/12_pdo_advanced.php)
- Complex relational queries with PDO: `INNER JOIN`, `LEFT JOIN`, aggregation (`COUNT`, `SUM`), grouping (`GROUP BY`), and subqueries.
- Robust database Transaction handling (`beginTransaction`, `commit`, `rollBack`).

### [13. Database Access with MySQLi](https://www.github.com/claytongf/php-cheat-sheet/examples/13_mysqli.php)
- Establishing MySQLi database connections (Object-Oriented & Procedural styles).
- Executing standard SQL queries and fetching records as associative arrays.
- Prepared statement parameter and result bindings (`bind_param`, `bind_result`).
- Executing transactions in MySQLi.

### [14. Creating REST APIs](https://www.github.com/claytongf/php-cheat-sheet/examples/14_api_creation.php)
- Setting JSON response headers and handling CORS requests.
- Reading raw request inputs (`php://input`) to parse incoming JSON payloads.
- Directing API route endpoints (routing routing logic) and returning standard HTTP status codes (`http_response_code`).
- Outputting validation checks and handling REST requests (GET, POST, PUT, DELETE).

### [15. Unit Testing in PHP](https://www.github.com/claytongf/php-cheat-sheet/examples/15_unit_testing.php)
- Overview of PHPUnit (industry standard framework) and testing lifecycles (`setUp()`, `tearDown()`).
- Basic assertions structure (`assertEquals`, `assertTrue`, etc.).
- Testing expected Exceptions and errors.
- Running a custom-built, dependency-free mock test runner directly in standard PHP.

### [16. Security Best Practices](https://www.github.com/claytongf/php-cheat-sheet/examples/16_security.php)
- Cryptographic password hashing (`password_hash`, `password_verify`).
- Cross-Site Scripting (XSS) prevention using output escaping (`htmlspecialchars`).
- Data sanitization and verification validations using `filter_var()`.
- Session-based CSRF protection tokens concept.

### [17. Composer & PSR-4 Autoloading](https://www.github.com/claytongf/php-cheat-sheet/examples/17_composer_autoload.php)
- Configuring package dependencies using `composer.json`.
- Mapping namespace prefixes to target paths using the PSR-4 standard.
- Registering custom autoloaders dynamically using `spl_autoload_register()`.

### [18. Attributes & Reflection API](https://www.github.com/claytongf/php-cheat-sheet/examples/18_attributes_reflection.php)
- Defining metadata annotations using PHP 8.0 Attributes (`#[Attribute]`).
- Attaching custom attributes to classes and methods.
- Querying and parsing attributes at runtime using `ReflectionClass` and `ReflectionMethod`.

### [19. Asynchronous PHP with Fibers](https://www.github.com/claytongf/php-cheat-sheet/examples/19_fibers.php)
- Understanding Fiber cooperative concurrency in PHP 8.1+.
- Launching, suspending, and resuming Fibers (`Fiber::suspend()`, `\$fiber->resume()`).
- Transferring values between the main program flow and Fibers.

### [20. Regular Expressions (PCRE)](https://www.github.com/claytongf/php-cheat-sheet/examples/20_regex.php)
- Pattern validation matching (`preg_match`).
- Named capturing groups extraction.
- Parsing multiple matched tokens globally (`preg_match_all`).
- String clean replacements (`preg_replace`) and list splits (`preg_split`).

### [21. Enums & Readonly Properties](https://www.github.com/claytongf/php-cheat-sheet/examples/21_enums_readonly.php)
- Unit Enums and Backed Enums (string/int values).
- Implementing methods inside enums.
- Readonly properties (PHP 8.1) and Readonly classes (PHP 8.2).

### [22. HTTP Requests (cURL & Streams)](https://www.github.com/claytongf/php-cheat-sheet/examples/22_http_requests.php)
- Querying endpoints using simple `file_get_contents`.
- Setting POST body payloads and headers via Stream Contexts.
- Executing advanced HTTP calls using the cURL extension (`curl_init`, options, header arrays, response checks).

### [23. Cryptography & Data Encryption](https://www.github.com/claytongf/php-cheat-sheet/examples/23_cryptography.php)
- Generating secure random bytes (`random_bytes`) and hex tokens.
- Secure message hashing (`hash` with SHA-256) and HMAC signatures (`hash_hmac`).
- Symmetric data encryption/decryption using OpenSSL and AES-256-CBC with initialization vectors (IV).

### [24. Design Patterns](https://www.github.com/claytongf/php-cheat-sheet/examples/24_design_patterns.php)
- Singleton Pattern (preventing external instantiations/clones).
- Factory Pattern (encapsulating object creation).
- Simple Dependency Injection (DI) Container to register and resolve service dependencies automatically.

### [25. XML & HTML Parsing](https://www.github.com/claytongf/php-cheat-sheet/examples/25_xml_html_parsing.php)
- Traversal mapping using `SimpleXMLElement` object arrays.
- DOM Document node indexing (`DOMDocument`) and error suppression (`libxml_use_internal_errors`).
- Targeted element search using DOMXPath selectors.

### [26. Generators & Memory Efficiency](https://www.github.com/claytongf/php-cheat-sheet/examples/26_generators.php)
- Using the `yield` keyword to iterate without allocating memory.
- Tracking memory utilization dynamically (`memory_get_usage`).
- Yielding custom key-value pairs.
- Sending data back into the generator co-routines flow (`\$generator->send()`).

### [27. CLI Scripting & Interactive Console](https://www.github.com/claytongf/php-cheat-sheet/examples/27_cli_scripting.php)
- Reading command line parameters (`\$argv`, `\$argc`).
- Capturing dynamic user input through console streams (`fgets(STDIN)`).
- Directing error outputs to `STDERR` and setting exit status codes.
- Running inside simulation mode inside standard HTTP requests.

### [28. Session Security & Custom Handlers](https://www.github.com/claytongf/php-cheat-sheet/examples/28_session_security.php)
- Securing cookies with parameters (lifetime, HTTPOnly, Secure, SameSite).
- Mitigating session hijacking via ID regeneration (`session_regenerate_id`).
- Implementing `SessionHandlerInterface` to construct custom storage adapters.

### [29. Sending Emails in PHP](https://www.github.com/claytongf/php-cheat-sheet/examples/29_sending_emails.php)
- Formatting custom email headers (HTML content structures, From, Reply-to).
- Dispatching via standard native `mail()` function and understanding php.ini requirements.
- Identifying delivery challenges (spam triggers, missing authentication).
- Standard implementation setups using PHPMailer or Symfony Mailer.

### [30. File Uploads & Validation](https://www.github.com/claytongf/php-cheat-sheet/examples/30_file_uploads.php)
- Handling file parameters via `$_FILES` and processing upload error codes.
- Restricting uploads safely using file size limits and MIME-type verification with the `finfo` class/functions.
- Generating sanitized, random, unique filenames and using `move_uploaded_file()`.
- Upload directory security measures (preventing PHP script executions).

### [31. Internationalization (i18n) & Localization (l10n)](https://www.github.com/claytongf/php-cheat-sheet/examples/31_localization_intl.php)
- Number, currency, and percentage formatting across locales with `NumberFormatter`.
- Localized date/time formats using `IntlDateFormatter`.
- Complex pluralization rules and parameter inputs with `MessageFormatter`.
- Locale-aware alphabetical sorting with `Collator` (sorting accents properly).

### [32. Dynamic Image Processing with GD](https://www.github.com/claytongf/php-cheat-sheet/examples/32_image_processing_gd.php)
- Creating blank images, allocating colors, and drawing lines/rectangles/circles.
- Rendering text onto canvas blocks using built-in fonts.
- Sizing and scaling photos down to create thumbnails with `imagecopyresampled()`.
- Graceful fallbacks and CLI warnings if the GD extension is missing.

### [33. Caching & Performance Optimization](https://www.github.com/claytongf/php-cheat-sheet/examples/33_caching_apcu_redis.php)
- Shared memory user variable caching with APCu (`apcu_store`, `apcu_fetch`, `apcu_delete`).
- Connection setup and key storage with TTL in Redis client wrappers.
- OPcache configurations to store precompiled script bytecode in shared memory.

### [34. Sockets & Network Programming](https://www.github.com/claytongf/php-cheat-sheet/examples/34_sockets_networking.php)
- Standard client connections using `fsockopen` / `stream_socket_client`.
- Local loop-based socket servers using `stream_socket_server` to process headers and respond.
- Non-blocking socket select monitoring loops using `stream_select()`.

### [35. JSON Web Tokens (JWT) Authentication](https://www.github.com/claytongf/php-cheat-sheet/examples/35_jwt_authentication.php)
- Base64Url safe string encoders and decoders implementation.
- Creating headers and user claims payloads.
- Creating cryptographically signed tokens (HMAC SHA-256) and validating them.
- Time-constant validation comparisons with `hash_equals()` to prevent timing attacks.

### [36. Benchmarking & Performance Profiling](https://www.github.com/claytongf/php-cheat-sheet/examples/36_benchmarking_performance.php)
- High-resolution timing checks with `hrtime()` and `microtime()`.
- Active memory allocation checks and limits using `memory_get_usage()` and `memory_get_peak_usage()`.
- Inspecting Garbage Collection (`gc_status()`) and manual cycles sweep (`gc_collect_cycles()`).
- Micro-benchmarks comparison of O(1) hash lookups against O(N) linear scans.

### [37. Advanced CLI Scripting](https://www.github.com/claytongf/php-cheat-sheet/examples/37_advanced_cli.php)
- Command line flag and arguments parser using `getopt()`.
- Printing bold, colored, and styled terminal text with ANSI escape codes.
- Gathering terminal inputs with `fgets(STDIN)` or `readline`.
- Rendering dynamic console progress updates in-place using carriage returns (`\r`).

### [38. SOAP & GraphQL APIs](https://www.github.com/claytongf/php-cheat-sheet/examples/38_soap_graphql.php)
- Core SOAP implementations using WSDL, `SoapClient` wrappers, header tokens, and `SoapFault` exception catches.
- GraphQL JSON querying setups via HTTP POST and cURL request routines.

### [39. PDF & Excel Spreadsheet Generation](https://www.github.com/claytongf/php-cheat-sheet/examples/39_pdf_excel_generation.php)
- PDF library pattern overviews (FPDF canvas structures and Dompdf HTML converters).
- PhpSpreadsheet library structures and native CSV exports.
- Raw styled multi-worksheet generation using standalone XML layouts without dependencies.

### [40. Advanced Emails with Attachments](https://www.github.com/claytongf/php-cheat-sheet/examples/40_mail_attachments.php)
- Constructing multipart/mixed MIME message structures.
- Setting up boundaries and attaching base64 chunked files.
- Sending rich text and HTML emails using standard `mail()`.

### [41. Database Queues & Background Workers](https://www.github.com/claytongf/php-cheat-sheet/examples/41_queues_workers.php)
- SQLite database-backed job queue implementation.
- Producer job insertions and worker reservation locks using SQLite Transactions.
- Processing logs, managing attempt thresholds, and retries.
- High-level overview of message brokers (Redis, RabbitMQ, Beanstalkd).

### [42. WeakReferences and WeakMaps](https://www.github.com/claytongf/php-cheat-sheet/examples/42_weak_references.php)
- Reference counter mechanics vs weak references.
- Instantiating and resolving `WeakReference` objects.
- Caching metadata on dynamic classes using `WeakMap` keys without preventing Garbage Collection.

### [43. DNS and Network Utilities](https://www.github.com/claytongf/php-cheat-sheet/examples/43_dns_network.php)
- Domain lookup queries (`dns_get_record`) and MX validation checks (`checkdnsrr`).
- Resolving IP/host translations and 32-bit integer conversion (`ip2long`, `long2ip`).
- Matching client IP configurations within CIDR subnet block arrays.

### [44. Event-Driven & Reactive Programming](https://www.github.com/claytongf/php-cheat-sheet/examples/44_event_loop.php)
- Non-blocking execution event loop concept in single-threaded setups.
- Building a custom Event Loop class supporting one-shot and periodic intervals.
- High-performance asynchronous engines summary (ReactPHP, Swoole, RoadRunner).

---

## 🛠️ Interactive Dashboard Features
- **Dynamic File Scanning**: The dashboard automatically detects any `.php` files placed in the `examples/` directory and renders them on the sidebar.
- **Dynamic Search System**: Real-time client-side search indexing that filters the sidebar topics dynamically by class names, file numbers, or custom keywords (e.g. typing "assertion" or "regex" matches their respective topics immediately).
- **Glassmorphism Theme**: Clean, responsive layout designed with neon accents, dark gradients, and smooth transition animations.
- **Real-Time Execution**: Click **"Run Code"** to execute scripts locally and capture their `stdout` inside a simulated terminal.
- **Isolated Sandbox**: Code execution runs inside isolated closures to prevent variable leakage between dashboard logic and example code.
