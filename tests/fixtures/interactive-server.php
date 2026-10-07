<?php

declare(strict_types=1);

// Real HTTP upgrade followed by simultaneous stdin and framed/raw output.
$config = json_decode($argv[1], true, 512, JSON_THROW_ON_ERROR);
$context = stream_context_create(['ssl' => $config['ssl'] ?? []]);
$server = stream_socket_server($config['address'], $errno, $error, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
if ($server === false) {
    throw new RuntimeException($error, $errno);
}
echo json_encode(['address' => stream_socket_get_name($server, false)], JSON_THROW_ON_ERROR) . "\n";
fflush(STDOUT);
for ($index = 0; $index < 2; ++$index) {
    $connection = stream_socket_accept($server, 5);
    if ($connection === false) {
        throw new RuntimeException('No interactive test connection.');
    }
    stream_set_timeout($connection, 3);
    if (isset($config['ssl']) && true !== stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLSv1_2_SERVER)) {
        throw new RuntimeException('Fixture TLS handshake failed.');
    }
    $request = '';
    while (false !== ($line = fgets($connection))) {
        $request .= $line;
        if ($line === "\r\n") {
            break;
        }
    }
    $body = '';
    if (preg_match('/\r\nContent-Length:\s*(\d+)/i', $request, $match)) {
        while (strlen($body) < (int) $match[1]) {
            $bytes = fread($connection, (int) $match[1] - strlen($body));
            if ($bytes === false || $bytes === '') {
                throw new RuntimeException('Incomplete HTTP body.');
            }
            $body .= $bytes;
        }
    }
    if ($index === 0) {
        echo json_encode(['request' => $request], JSON_THROW_ON_ERROR) . "\n";
        fwrite($connection, "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\nContent-Length: 2\r\nConnection: close\r\n\r\n{}");
        fclose($connection);
        continue;
    }
    $frame = static fn (int $channel, string $bytes): string => ($config['tty'] ?? false) ? $bytes : pack('CxxxN', $channel, strlen($bytes)) . $bytes;
    fwrite($connection, "HTTP/1.1 101 UPGRADED\r\nConnection: Upgrade\r\nUpgrade: tcp\r\nContent-Type: application/vnd.docker.raw-stream\r\n\r\n" . $frame(1, 'ready:'));
    $stdin = '';
    while (!feof($connection)) {
        $chunk = fread($connection, 16384);
        if ($chunk === false) {
            throw new RuntimeException('Cannot read fixture stdin.');
        }
        if ($chunk !== '') {
            $stdin .= $chunk;
            fwrite($connection, $frame(1, $chunk));
            if (($config['tty'] ?? false) && strlen($stdin) >= $config['input_length']) {
                break;
            }
        } elseif (stream_get_meta_data($connection)['timed_out']) {
            throw new RuntimeException('The client did not half-close stdin.');
        }
    }
    fwrite($connection, $frame(1, ':done') . $frame(2, 'error'));
    echo json_encode(['request' => $request, 'body' => $body, 'stdin_hash' => hash('sha256', $stdin), 'stdin_length' => strlen($stdin)], JSON_THROW_ON_ERROR) . "\n";
    fclose($connection);
}
fclose($server);
