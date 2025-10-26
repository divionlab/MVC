<?php
use Core\Localization\Translator;

/**
 * global translator instance - tiekiame per $GLOBALS iš Bootstrap arba index jei taip patogu
 * PASTABA: įsitikink, kad Bootstrap arba index sukuria $translator ir priskiria $GLOBALS['translator']
 */

if (!function_exists('__t')) {
    function __t(string $key, array $replacements = []): string
    {
        $translator = $GLOBALS['translator'] ?? null;
        if ($translator instanceof Translator) {
            return $translator->trans($key, $replacements);
        }
        return $key;
    }
}

if (!function_exists('lang_url')) {
    function lang_url(string $path = ''): string
    {
        $lang = $_SESSION['locale'] ?? ($GLOBALS['request']->getLang() ?? 'en');
        $path = ltrim($path, '/');
        return '/' . $lang . ($path !== '' ? '/' . $path : '');
    }
}



if (!function_exists('dd')) {
    function dd(...$vars)
    {
        echo '<pre>';
        foreach ($vars as $v) {
            var_dump($v);
        }
        echo '</pre>';
        die();
    }
}
if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}
if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        $token = csrf_token();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token) . '">';
    }
}
if (!function_exists('view')) {
    function view($path, $data = [])
    {
        $fullPath = BASE_PATH . '/app/Modules/' . $path . '.php';
        if (file_exists($fullPath)) {
            extract($data);
            include $fullPath;
        } else {
            echo "View file not found: $fullPath";
        }
    }
}