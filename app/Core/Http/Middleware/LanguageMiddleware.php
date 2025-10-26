<?php
namespace Core\Http\Middleware;

use Core\Http\Request;

class LanguageMiddleware
{
    protected array $supported = ['en', 'lt', 'ru'];
    protected string $default = 'en';

    public function handle($request)
    {
        // $request gali būti Core\Http\Request
        if (!($request instanceof Request)) {
            return;
        }

        // Gauk path ir pirmą segmentą
        $path = trim($request->path(), '/');
        $segments = $path === '' ? [] : explode('/', $path);
        $first = $segments[0] ?? null;

        // Jei pirmas segmentas supported -> set lang
        if ($first && in_array($first, $this->supported)) {
            $lang = $first;
            // remove lang segment from REQUEST_URI for internal routing? We'll set Request->lang and Router will strip
            $_SESSION['locale'] = $lang;
            $request->setLang($lang);
        } else {
            // Jei nėra -> paimk iš sesijos arba default
            $lang = $_SESSION['locale'] ?? ($request->getLang() ?? $this->default);
            $_SESSION['locale'] = $lang;
            $request->setLang($lang);
        }

        // Optional: set cookie for persistence across subdomains etc.
        if (!headers_sent()) {
            setcookie('locale', $_SESSION['locale'], time() + 30*24*60*60, '/');
        }
    }
}

