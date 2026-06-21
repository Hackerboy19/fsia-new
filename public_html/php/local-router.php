<?php
/**
 * PHP Built-in Web Server Router Script
 * Maps root-relative URLs and handles compatibility for renamed folders.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// 1. If visiting root '/' or '/index.php', serve 'public_html/index.php'
if ($uri === '/' || $uri === '/index.php') {
    $script = __DIR__ . '/public_html/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $script;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    chdir(dirname($script));
    include $script;
    return true;
}

// 2. Compatibility Layer: Rewrite '/assets/...' to '/assets-new/...' internally
if (strpos($uri, '/assets/') === 0) {
    $newUri = '/assets-new/' . substr($uri, 8);
    $filePath = __DIR__ . $newUri;
    if (file_exists($filePath) && !is_dir($filePath)) {
        $mimeType = mime_content_type($filePath);
        // Override mime types that might be incorrectly resolved by PHP's built-in mime database
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($ext === 'css') {
            $mimeType = 'text/css';
        } elseif ($ext === 'js') {
            $mimeType = 'application/javascript';
        }
        header("Content-Type: $mimeType");
        readfile($filePath);
        return true;
    }
}

// 3. If the file exists directly in the workspace root, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // let the built-in server handle the static file
}

// 4. If the file exists under public_html (e.g. public_html/about.php), map and serve it
$publicHtmlPath = __DIR__ . '/public_html' . $uri;
if (file_exists($publicHtmlPath) && !is_dir($publicHtmlPath)) {
    if (pathinfo($publicHtmlPath, PATHINFO_EXTENSION) === 'php') {
        $_SERVER['SCRIPT_FILENAME'] = $publicHtmlPath;
        $_SERVER['SCRIPT_NAME'] = $uri;
        $_SERVER['PHP_SELF'] = $uri;
        chdir(dirname($publicHtmlPath));
        include $publicHtmlPath;
        return true;
    }
    
    // For static files in public_html, send content-type and serve
    $mimeType = mime_content_type($publicHtmlPath);
    header("Content-Type: $mimeType");
    readfile($publicHtmlPath);
    return true;
}

// 5. Default 404 handler
return false;
