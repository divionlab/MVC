<?php
namespace Core\View;

class View
{
    public static function render(string $view, array $data = []): string
    {
        [$module, $path] = explode('::', $view);
        $viewPath = __DIR__ . "/../../Modules/$module/Resources/Views/$path.php";

        extract($data);
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    public static function renderLayout(string $layout, array $data = []): string
    {
        $layoutPath = __DIR__ . "/../../Resources/Views/Layouts/$layout.php";
        extract($data);
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }
}
