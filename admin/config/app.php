<?php

$appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');

if ($appUrl === '') {
    throw new RuntimeException('APP_URL is missing from .env');
}

if (!defined('APP_URL')) {
    define('APP_URL', $appUrl);
}