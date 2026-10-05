<?php

declare(strict_types=1);

$caPath = getenv('MYSQL_ATTR_SSL_CA');

if (! $caPath) {
    echo "[check-ca] MYSQL_ATTR_SSL_CA environment variable is not set. Skipping CA verification.\n";
    exit(0);
}

if (! file_exists($caPath) || ! is_readable($caPath)) {
    fwrite(STDERR, "[check-ca] ERROR: CA certificate file not found or not readable at: {$caPath}\n");
    exit(1);
}

$content = file_get_contents($caPath);
if ($content === false || trim($content) === '') {
    fwrite(STDERR, "[check-ca] ERROR: CA certificate file is empty at: {$caPath}\n");
    exit(1);
}

if (! str_contains($content, '-----BEGIN CERTIFICATE-----')) {
    fwrite(STDERR, "[check-ca] ERROR: CA certificate file does not contain a valid PEM certificate header.\n");
    exit(1);
}

$certData = @openssl_x509_parse($content);
if ($certData === false) {
    fwrite(STDERR, "[check-ca] WARNING: Could not parse X509 certificate data, but PEM header is present.\n");
} else {
    $subject = $certData['name'] ?? 'Unknown Subject';
    $validTo = isset($certData['validTo_time_t']) ? date('Y-m-d H:i:s', $certData['validTo_time_t']) : 'Unknown';
    echo "[check-ca] SUCCESS: CA certificate verified. Subject: {$subject}, Valid to: {$validTo}\n";
}

exit(0);
