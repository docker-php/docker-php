<?php

declare(strict_types=1);

// Bounded local server shared by the factory and Guzzle connection tests.
$config = json_decode($argv[1], true, 512, JSON_THROW_ON_ERROR);
$context = stream_context_create(['ssl' => $config['ssl'] ?? []]);
$server = stream_socket_server($config['address'], $errno, $error, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
if (false === $server) {
    throw new RuntimeException($error, $errno);
}

echo json_encode(['address' => stream_socket_get_name($server, false)], JSON_THROW_ON_ERROR)."\n";
fflush(STDOUT);

$responses = $config['responses'] ?? [($config['upgrade'] ?? false)
    ? ['status' => 101, 'body' => 'stream-output', 'headers' => ['Connection' => 'Upgrade', 'Upgrade' => 'tcp']]
    : ['body' => 'OK']];

foreach ($responses as $response) {
    $connection = stream_socket_accept($server, 5);
    if (false === $connection) {
        throw new RuntimeException('No test client connected.');
    }
    stream_set_timeout($connection, 5);

    if (isset($config['ssl']) && true !== @stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLSv1_2_SERVER)) {
        echo json_encode(['tls' => false], JSON_THROW_ON_ERROR)."\n";
        fclose($connection);
        fclose($server);
        exit(0);
    }

    $request = '';
    while (false !== ($line = fgets($connection))) {
        $request .= $line;
        if ("\r\n" === $line) {
            break;
        }
    }
    $body = '';
    if (preg_match('/\r\nContent-Length:\s*(\d+)/i', $request, $match)) {
        while (strlen($body) < (int) $match[1]) {
            $chunk = fread($connection, (int) $match[1] - strlen($body));
            if (false === $chunk || '' === $chunk) {
                throw new RuntimeException('Incomplete request body.');
            }
            $body .= $chunk;
        }
    }

    $result = ['request' => $request, 'body' => $body];
    $options = stream_context_get_options($connection);
    if (isset($options['ssl']['peer_certificate'])) {
        $result['peer'] = openssl_x509_parse($options['ssl']['peer_certificate'])['subject']['CN'];
    }
    echo json_encode($result, JSON_THROW_ON_ERROR)."\n";

    if ('' !== $request) {
        $body = isset($response['body_base64']) ? base64_decode($response['body_base64'], true) : ($response['body'] ?? '');
        $status = $response['status'] ?? 200;
        $headers = $response['headers'] ?? [];
        if (101 !== $status) {
            $headers += ['Connection' => 'close'];
            if (!isset($headers['Transfer-Encoding'])) {
                $headers += ['Content-Length' => strlen($body)];
            }
        }
        $message = 'HTTP/1.1 '.$status." Test\r\n";
        foreach ($headers as $name => $value) {
            $message .= $name.': '.$value."\r\n";
        }
        $segments = isset($response['segments_base64'])
            ? array_map(static fn (string $segment): string => base64_decode($segment, true), $response['segments_base64'])
            : [$body];
        // Send the first segment with the headers to exercise PHP's read buffer.
        fwrite($connection, $message."\r\n".array_shift($segments));
        foreach ($segments as $segment) {
            usleep(($response['delay_ms'] ?? 0) * 1000);
            fwrite($connection, $segment);
        }
    }
    fclose($connection);
}
fclose($server);
