<?php
/**
 * PHP Cheat Sheet - 43: DNS and Network Utilities
 * 
 * Topics covered:
 * - Resolving domains to IPs (gethostbyname, gethostbyaddr)
 * - Verifying domain records (checkdnsrr for MX email validations)
 * - Querying DNS resource records (dns_get_record)
 * - Efficient IP storage formatting (ip2long, long2ip)
 * - Checking if an IP falls within a CIDR subnet range
 */

echo "=== 1. HOST IP RESOLUTION ===\n";

$domain = "google.com";

// Resolve domain name to IPv4 address
$ipAddress = gethostbyname($domain);
echo "Resolving '$domain' to IP: $ipAddress\n";

// Reverse resolve IP back to host name
$hostName = gethostbyaddr($ipAddress);
echo "Reverse resolving '$ipAddress' to host: $hostName\n\n";


echo "=== 2. VERIFYING DNS RECORDS (checkdnsrr) ===\n";
echo "checkdnsrr() checks DNS records for a host. Extremely useful to validate email domains before sending:\n\n";

function validateEmailDomain(string $email): bool {
    $parts = explode('@', $email);
    if (count($parts) !== 2) return false;
    
    $domain = $parts[1];
    
    // Check if the domain has Mail Exchanger (MX) records configured
    // This runs a fast query and returns true if MX records are found
    if (php_sapi_name() !== 'cli' || !@checkdnsrr($domain, 'MX')) {
        // Mock fallback check for simulation if offline
        echo "[Simulation] Querying DNS MX records for '$domain'...\n";
        return in_array($domain, ['gmail.com', 'outlook.com', 'yahoo.com']);
    }
    
    return true;
}

$emailsToTest = ['user@gmail.com', 'john@nonexistent-fake-domain-1029.xyz'];

foreach ($emailsToTest as $email) {
    $isValid = validateEmailDomain($email);
    echo "Is email '$email' domain valid? " . ($isValid ? "Yes (MX Record exists)" : "No (MX Record missing)") . "\n";
}
echo "\n";


echo "=== 3. DETAILED DNS RECORD QUERY (dns_get_record) ===\n";
echo "dns_get_record() fetches detailed resource records (A, MX, TXT, NS, CNAME):\n\n";

if (php_sapi_name() !== 'cli') {
    // Offline / Web Simulation
    echo "Querying A records for 'example.com':\n";
    print_r([
        [
            'host' => 'example.com',
            'class' => 'IN',
            'ttl' => 86400,
            'type' => 'A',
            'ip' => '93.184.216.34'
        ]
    ]);
} else {
    // Real DNS record fetch
    $dnsRecords = @dns_get_record("example.com", DNS_A);
    echo "A Records for example.com:\n";
    print_r($dnsRecords);
}
echo "\n";


echo "=== 4. EFFICIENT IP STORAGE & FORMATTING (ip2long / long2ip) ===\n";
echo "Storing IP addresses as strings (e.g. '192.168.1.15') consumes 15 bytes. Converting them to integers takes only 4 bytes (32-bit):\n\n";

$ip = "192.168.1.25";

// 1. Convert IP string to 32-bit signed integer
$ipLong = ip2long($ip);
echo "IP string: '$ip' -> Integer representation: $ipLong\n";

// 2. Convert 32-bit signed integer back to IP string
$ipString = long2ip($ipLong);
echo "Integer: $ipLong -> IP string representation: '$ipString'\n\n";


echo "=== 5. CHECKING IP IN CIDR RANGE ===\n";
echo "Determine if a client IP falls inside a CIDR subnet block (e.g., Cloudflare IP ranges):\n\n";

function ipInSubnet(string $ip, string $cidr): bool {
    list($subnet, $bits) = explode('/', $cidr);
    
    $ipLong = ip2long($ip);
    $subnetLong = ip2long($subnet);
    
    // Mask calculations
    // Shift bits to get network mask (e.g. /24 -> 255.255.255.0)
    $mask = -1 << (32 - $bits);
    
    // Bitwise AND matches network addresses
    return ($ipLong & $mask) == ($subnetLong & $mask);
}

$clientIp = "192.168.1.55";
$subnet = "192.168.1.0/24"; // Subnet mask

$isAllowed = ipInSubnet($clientIp, $subnet);
echo "Does IP '$clientIp' belong to subnet '$subnet'? " . ($isAllowed ? "Yes" : "No") . "\n";

$isAllowed2 = ipInSubnet("10.0.0.1", $subnet);
echo "Does IP '10.0.0.1' belong to subnet '$subnet'? " . ($isAllowed2 ? "Yes" : "No") . "\n";
?>
