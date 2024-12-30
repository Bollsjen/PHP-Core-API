<?php
// Ensure this file is being run directly and not included
if (defined('RUNNING_FROM_ROOT') === false) define('RUNNING_FROM_ROOT', true);

// Set error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the public index.php file
require_once __DIR__ . '/public/index.php';