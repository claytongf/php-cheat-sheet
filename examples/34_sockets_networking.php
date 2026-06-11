<?php
/**
 * PHP Cheat Sheet - 34: Sockets & Network Programming
 * 
 * Topics covered:
 * - Low-level socket functions vs streams
 * - Creating a TCP Socket Client (fsockopen, stream_socket_client)
 * - Creating a TCP Socket Server (stream_socket_server)
 * - Accepting connections, reading requests, and writing responses
 * - Non-blocking sockets and connection timeouts
 */

echo "=== 1. SOCKET CLIENT (fsockopen / stream_socket_client) ===\n";
echo "A socket client initiates a network connection to a server over TCP/UDP:\n\n";

// We simulate connecting to a public HTTP server (e.g. example.com:80) or show the structure.
// To keep execution fast and fully offline-resilient, we show the code and capture the pattern.
$clientCode = '
// Opening a TCP connection to port 80 with a 3-second timeout
$socket = @fsockopen("example.com", 80, $errCode, $errStr, 3);

if (!$socket) {
    echo "Connection failed: $errStr ($errCode)\n";
} else {
    // Send a standard HTTP GET request header
    $request = "GET / HTTP/1.1\r\n";
    $request .= "Host: example.com\r\n";
    $request .= "Connection: Close\r\n\r\n";
    
    fwrite($socket, $request);
    
    // Read response line by line (or until EOF)
    $response = "";
    while (!feof($socket)) {
        $response .= fgets($socket, 1024);
    }
    
    fclose($socket);
    echo "Response received successfully.\n";
}
';

echo "TCP Client Implementation Pattern:\n" . trim($clientCode) . "\n\n";


echo "=== 2. SOCKET SERVER (stream_socket_server) ===\n";
echo "A socket server listens on a port for incoming network connections:\n\n";

// Dynamic simulation: we will create a server on a random port, connect to it, accept 1 connection, respond, and close.
// This proves it works in real-time inside this runner!

// Find a free port by binding to port 0 (OS chooses a random free port)
$server = @stream_socket_server("tcp://127.0.0.1:0", $errno, $errstr);

if (!$server) {
    echo "Could not start socket server: $errstr ($errno)\n";
} else {
    // Get the socket name (which contains the chosen port)
    $socketName = stream_socket_get_name($server, false);
    echo "Server successfully listening on TCP address: $socketName\n";
    
    // Parse the port
    $port = parse_url("tcp://" . $socketName, PHP_URL_PORT);
    
    // To prevent blocking forever, set a short timeout of 1 second on the server socket
    stream_set_timeout($server, 1);
    
    // Launch a client request in simulation mode
    // We will do a non-blocking request loop or mock a client connection right after starting
    echo "Client: Connecting to local server on port $port...\n";
    $client = @stream_socket_client("tcp://127.0.0.1:$port", $cErrno, $cErrstr, 1);
    
    if (!$client) {
        echo "Client connection failed: $cErrstr ($cErrno)\n";
    } else {
        // Send message from client to server
        fwrite($client, "Hello Server! This is Clayton.\r\n");
        
        // Server: Accept the client connection
        $connection = @stream_socket_accept($server, 1); // wait up to 1 second
        
        if ($connection) {
            echo "Server: Accepted incoming connection.\n";
            
            // Read client data
            $received = fread($connection, 1024);
            echo "Server received: " . trim($received) . "\n";
            
            // Send HTTP/text response back to client
            $response = "HTTP/1.1 200 OK\r\nContent-Type: text/plain\r\nConnection: close\r\n\r\nHello Client! Connection was successful.\n";
            fwrite($connection, $response);
            fclose($connection);
            echo "Server: Response sent, connection closed.\n";
        }
        
        // Client: Read response back from server
        $clientResponse = fread($client, 1024);
        echo "Client received response:\n---\n" . trim($clientResponse) . "\n---\n";
        
        fclose($client);
    }
    
    fclose($server);
    echo "Server socket closed.\n\n";
}

echo "=== 3. SOCKET STREAM SELECT (CONCURRENT SERVER PATTERN) ===\n";
echo "To handle multiple clients simultaneously without multi-threading, use stream_select():\n\n";

$selectCode = '
$clients = [$serverSocket]; // Array of streams to monitor

while (true) {
    $read = $clients;
    $write = null;
    $except = null;
    
    // Monitor streams for incoming data/connections (blocks until socket changes)
    if (stream_select($read, $write, $except, 10) === 0) {
        continue; // Timeout reached
    }
    
    foreach ($read as $socket) {
        if ($socket === $serverSocket) {
            // New incoming connection request
            $newClient = stream_socket_accept($serverSocket);
            $clients[] = $newClient;
            echo "New client connected!\n";
        } else {
            // Existing client has sent data
            $data = fread($socket, 1024);
            if (empty($data)) {
                // Client disconnected
                fclose($socket);
                $key = array_search($socket, $clients);
                unset($clients[$key]);
                echo "Client disconnected.\n";
            } else {
                // Process client message
                fwrite($socket, "Received: " . $data);
            }
        }
    }
}
';

echo "Multi-Client stream_select Loop:\n" . trim($selectCode) . "\n";
?>
