<?php

declare(strict_types=1);

$dist = getcwd();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = rtrim($dist, '/') . $path;

if (file_exists($file)) {
    return false;
}

http_response_code(404);
echo "404 Not Found";
