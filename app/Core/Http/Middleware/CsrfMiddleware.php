<?php
namespace Core\Http\Middleware;

class CsrfMiddleware
{
    public function handle($request)
    {
        if ($request->isPost()) {
            $token = $_POST['_token'] ?? null;
            if (!$token || $token !== ($_SESSION['_token'] ?? null)) {
                http_response_code(403);
                exit('Invalid CSRF token');
            }
        }

        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
    }
}
