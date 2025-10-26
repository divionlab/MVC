<?php
namespace Core\Controllers;
use Core\Http\Response;
class AssetController
{
    public function serve($request)
    {
        $pathParts = explode('/', trim($request->path(), '/'));
        if (count($pathParts) < 5) {
            return new Response('Bad Request', 400);
        }
        [$assets, $module, $name, $type, $file] = $pathParts;
        $basePath = BASE_PATH . "/app/Modules/{$name}/Assets";
        $subdir = match ($type) {
            'css' => 'Styles',
            'js' => 'JavaScript',
            'images' => 'Images',
            'videos' => 'Videos',
            default => null
        };
        if (!$subdir) {
            return new Response('Invalid asset type', 400);
        }
        $filePath = "{$basePath}/{$subdir}/{$file}";
        if (!file_exists($filePath)) {
            return new Response('File not found', 404);
        }
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $mime = match ($ext) {
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            default => 'application/octet-stream',
        };
        header("Content-Type: {$mime}");
        readfile($filePath);
        exit;
    }
}