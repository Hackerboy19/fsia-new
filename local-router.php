<?php
/**
 * Root-level Local Router for PHP Built-in Server
 * Solves asset resolution and script mapping in development.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Map '/' or '/index.php' to index.php in php directory
if ($uri === '/' || $uri === '/index.php') {
    $script = __DIR__ . '/public_html/php/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $script;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    chdir(dirname($script));
    include $script;
    return true;
}

// 2. Map '/assets/...' to '/public_html/assets-new/...'
if (strpos($uri, '/assets/') === 0) {
    $filePath = __DIR__ . '/public_html/assets-new/' . substr($uri, 8);
    return serveStatic($filePath);
}

// 3. Map '/assets-new/...' to '/public_html/assets-new/...'
if (strpos($uri, '/assets-new/') === 0) {
    $filePath = __DIR__ . '/public_html' . $uri;
    return serveStatic($filePath);
}

// 4. Map any other file under public_html/
$publicHtmlPath = __DIR__ . '/public_html' . $uri;
if (file_exists($publicHtmlPath) && !is_dir($publicHtmlPath)) {
    if (pathinfo($publicHtmlPath, PATHINFO_EXTENSION) === 'php') {
        return servePhp($publicHtmlPath, $uri);
    }
    return serveStatic($publicHtmlPath);
}

// 5. Map root requests (e.g. /miss-universe-beauty-pageant.php) to public_html/php/
$phpPath = __DIR__ . '/public_html/php' . $uri;
if (file_exists($phpPath) && !is_dir($phpPath)) {
    if (pathinfo($phpPath, PATHINFO_EXTENSION) === 'php') {
        return servePhp($phpPath, $uri);
    }
    return serveStatic($phpPath);
}

return false;

/**
 * Helper to serve a static file with correct headers.
 */
function serveStatic($path) {
    if (file_exists($path) && !is_dir($path)) {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $mime = 'text/plain';
        if ($ext === 'css') {
            $mime = 'text/css';
        } elseif ($ext === 'js') {
            $mime = 'application/javascript';
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $mime = 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
        } elseif ($ext === 'svg') {
            $mime = 'image/svg+xml';
        } else {
            $mime = mime_content_type($path) ?: $mime;
        }
        header("Content-Type: $mime");
        readfile($path);
        return true;
    }
    return false;
}

/**
 * Helper to execute a PHP script within its correct directory context.
 */
function servePhp($path, $uri) {
    $_SERVER['SCRIPT_FILENAME'] = $path;
    $_SERVER['SCRIPT_NAME'] = $uri;
    $_SERVER['PHP_SELF'] = $uri;
    chdir(dirname($path));
    include $path;
    return true;
}
?>
