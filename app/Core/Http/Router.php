<?php
namespace Core\Http;
class Router
{
    protected array $routes = [];
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->loadModuleRoutes();
    }
    protected function loadModuleRoutes()
    {
        $this->routes = include BASE_PATH . '/app/Core/Routes/loader.php';
    }
  public function dispatch(): Response
{
    $path = trim($this->request->path(), '/');

    // ✅ Palaikomos kalbos
    $supportedLangs = ['en', 'lt', 'ru'];
    $segments = explode('/', $path);
    $firstSegment = $segments[0] ?? '';

    // ✅ Patikriname ar pirmasis segmentas yra kalbos kodas
    if (in_array($firstSegment, $supportedLangs)) {
        $lang = $firstSegment;
        $this->request->setLang($lang);
        $_SESSION['lang'] = $lang;
        array_shift($segments);
        $path = implode('/', $segments);
    } else {
        // Jei nėra kalbos — nukreipiame į numatytąją (pvz. en)
        $defaultLang = $_SESSION['lang'] ?? 'en';
        header("Location: /{$defaultLang}/" . $path);
        exit;
    }

    // ✅ Toliau – toks pat route matching kaip buvo
    foreach ($this->routes as $route => [$controller, $method]) {
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', trim($route, '/'));
        $pattern = "#^{$pattern}/?$#";

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches);

            if (!class_exists($controller)) {
                return new Response("Controller {$controller} not found", 500);
            }

            $instance = new $controller();

            if (!method_exists($instance, $method)) {
                return new Response("Method {$method} not found in {$controller}", 500);
            }

            return new Response(
                $instance->$method($this->request, ...$matches)
            );
        }
    }

    return new Response('404 Not Found', 404);
}

}