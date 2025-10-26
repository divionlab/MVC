<?php
namespace Core\Http\Middleware;

class SessionMiddleware
{
    public function handle($request)
    {
        // ✅ Užtikriname, kad sesija neprasidėtų kelis kartus
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Saugos nustatymai (pasirinktinai)
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);

            // Startuojame sesiją
            session_start([
                'cookie_lifetime' => 86400, // 1 diena
                'read_and_close' => false,
            ]);
        }

        // ✅ Užtikriname, kad sesijoje būtų nustatyta kalba
        if (!isset($_SESSION['lang'])) {
            $_SESSION['lang'] = 'en'; // numatytoji kalba
        }

        return $request;
    }
}

