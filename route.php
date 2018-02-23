<?php
/**
 * Created by PhpStorm.
 * User: ecst
 * Date: 2018-02-22
 * Time: 오후 3:47
 */
$requestUri = $_SERVER["REQUEST_URI"];
$rawParam = strpos($requestUri, '?');
if (false !== $rawParam) {
    $requestUri = substr($requestUri, 0, $rawParam);
}

function convertParams(array $params)
{
    foreach ($params as $idx => $value) {
        $_GET[$idx] = $value;
        $_REQUEST[$idx] = $value;
    }
}

if (preg_match('/^\/(layouts|m.layouts)\/(.+)\/(.+).html$/', $requestUri)
    || preg_match('/^\/(modules|addons|widgets)\/(.+)\/(conf|queries|schemas)\/(.+).xml$/', $requestUri)) {
    return true;

} elseif (file_exists(__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $requestUri))) {
    return false;

} elseif (preg_match('/^\/(.+)\/files\/(member_extra_info|attach|cache|faceOff)\/(.*)/', $requestUri, $matches)) {
    # static files
    $matches[3] = str_replace('/', DIRECTORY_SEPARATOR, $matches[3]);
    $matches[3] = str_replace('\\', DIRECTORY_SEPARATOR, $matches[3]);
    $suspected = implode(DIRECTORY_SEPARATOR, [__DIR__, 'files', $matches[2], $matches[3]]);

    if (file_exists($suspected)) {
        header('Content-Type: ' . mime_content_type($suspected));
        readfile($suspected);
    } else {
        return false;
    }
} elseif (preg_match('/^\/(.+)\/(files|modules|widgets|widgetstyles|layouts|m.layouts|addons)\/(.*)/', $requestUri, $matches)) {
    # static files
    //rewrite  /$2/$3 last;
    $matches[3] = str_replace('/', DIRECTORY_SEPARATOR, $matches[3]);
    $matches[3] = str_replace('\\', DIRECTORY_SEPARATOR, $matches[3]);
    $suspected = implode(DIRECTORY_SEPARATOR, [__DIR__, $matches[2], $matches[3]]);

    if (file_exists($suspected)) {
        header('Content-Type: ' . mime_content_type($suspected));
        readfile($suspected);
    } else {
        return false;
    }
} else {
    if (preg_match('/^\/(rss|atom)$/', $requestUri, $matches)) {
        # rss , blogAPI
        convertParams(['module' => 'rss', 'act' => $matches[1]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/(rss|atom|api)$/', $requestUri, $matches)) {
        # rss , blogAPI
        convertParams(['mid' => $matches[1], 'act' => $matches[2]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)\/(rss|atom|api)$/', $requestUri, $matches)) {
        # rss , blogAPI
        convertParams(['vid' => $matches[1], 'mid' => $matches[2], 'act' => $matches[3]]);

    } elseif (preg_match('/^\/([0-9]+)\/(.+)\/trackback$/', $requestUri, $matches)) {
        # trackback
        convertParams(['document_srl' => $matches[1], 'key' => $matches[2], 'act' => 'trackback']);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([0-9]+)\/(.+)\/trackback$/', $requestUri, $matches)) {
        # trackback
        convertParams(['mid' => $matches[1], 'document_srl' => $matches[2], 'key' => $matches[3], 'act' => 'trackback']);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)\/([0-9]+)\/(.+)\/trackback$/', $requestUri, $matches)) {
        # trackback
        convertParams(['vid' => $matches[1], 'mid' => $matches[2], 'document_srl' => $matches[3], 'act' => 'trackback']);

    } elseif (preg_match('/^\/admin\/?$/', $requestUri)) {
        # administrator page
        convertParams(['module' => 'admin']);

    } elseif (preg_match('/^\/([0-9]+)$/', $requestUri, $matches)) {
        # document permanent link
        convertParams(['document_srl' => $matches[1]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/?$/', $requestUri, $matches)) {
        # mid link
        convertParams(['mid' => $matches[1]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([0-9]+)$/', $requestUri, $matches)) {
        # mid + document link
        convertParams(['mid' => $matches[1], 'document_srl' => $matches[2]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)\/?$/', $requestUri, $matches)) {
        # vid + mid link
        convertParams(['vid' => $matches[1], 'mid' => $matches[2]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)\/([0-9]+)$/', $requestUri, $matches)) {
        # vid + mid + document link
        convertParams(['vid' => $matches[1], 'mid' => $matches[2], 'document_srl' => $matches[3]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/entry\/(.+)$/', $requestUri, $matches)) {
        # mid + entry title
        convertParams(['mid' => $matches[1], 'entry' => $matches[2]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)\/entry\/(.+)$/', $requestUri, $matches)) {
        # vid + mid + entry title
        convertParams(['vid' => $matches[1], 'mid' => $matches[2], 'entry' => $matches[3]]);

    } elseif (preg_match('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_.-]+)$/', $requestUri, $matches)) {
        # shop + vid / [category|product] / identifier
        convertParams(['act' => 'route', 'vid' => $matches[1], 'type' => $matches[2], 'identifier' => $matches[3]]);

    }

    include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
}

return true;