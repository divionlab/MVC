<?php
namespace Core\Modules;

class ModuleLoader
{
    protected array $modules = [];
    protected array $css = [];
    protected array $js = [];
    protected array $images = [];
    protected array $videos = [];

    public function loadModules(string $modulesPath)
    {
        foreach (scandir($modulesPath) as $module) {
            if ($module === '.' || $module === '..')
                continue;

            $path = $modulesPath . '/' . $module;
            $json = $path . '/module.json';

            if (!file_exists($json))
                continue;
            $config = json_decode(file_get_contents($json), true);
            if (!($config['enabled'] ?? false))
                continue;

            $this->modules[$module] = $config;

            // CSS
            if (!empty($config['assets']['css'])) {
                foreach ($config['assets']['css'] as $file) {
                    $filename = basename($file);
                    $this->css[] = "/assets/module/{$module}/css/{$filename}";
                }
            }

            // JS
            if (!empty($config['assets']['js'])) {
                foreach ($config['assets']['js'] as $file) {
                    $filename = basename($file);
                    $this->js[] = "/assets/module/{$module}/js/{$filename}";
                }
            }

            // Images (automatiškai)
            $imageDir = "{$path}/Assets/Images";
            if (is_dir($imageDir)) {
                foreach (glob("{$imageDir}/*.{jpg,jpeg,png,gif,svg,webp}", GLOB_BRACE) as $img) {
                    $filename = basename($img);
                    $this->images[] = "/assets/module/{$module}/images/{$filename}";
                }
            }

            // Videos (automatiškai)
            $videoDir = "{$path}/Assets/Videos";
            if (is_dir($videoDir)) {
                foreach (glob("{$videoDir}/*.{mp4,webm,ogg}", GLOB_BRACE) as $vid) {
                    $filename = basename($vid);
                    $this->videos[] = "/assets/module/{$module}/videos/{$filename}";
                }
            }
        }
    }

    // 👇 Čia trūkstamas metodas
    public function getModules(): array
    {
        return $this->modules;
    }
    public function getCss(): array
    {
        return $this->css;
    }
    public function getJs(): array
    {
        return $this->js;
    }
    public function getImages(): array
    {
        return $this->images;
    }
    public function getVideos(): array
    {
        return $this->videos;
    }
}
