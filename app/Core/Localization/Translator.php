<?php
namespace Core\Localization;

use PDO;
use Core\Http\Request;

class Translator
{
    protected Request $request;
    protected PDO|null $db;
    protected string $default = 'en';
    protected array $fileCache = []; // ['Module_locale' => array]
    protected array $dbCache = [];   // ['module_locale' => ['key' => 'value']]

    public function __construct(Request $request, ?PDO $db = null)
    {
        $this->request = $request;
        $this->db = $db;
    }

    public function getLocale(): string
    {
        return $this->request->getLang() ?: $this->default;
    }

    // Load module lang file (module names in your modules folder should match)
protected function loadModuleFile(string $module, ?string $locale = null): array
{
    $locale = $locale ?: $this->getLocale();
    $cacheKey = "{$module}_{$locale}";
    if (isset($this->fileCache[$cacheKey])) return $this->fileCache[$cacheKey];

    $file = BASE_PATH . "/app/Modules/{$module}/Resources/Lang/{$locale}.php";
    if (!file_exists($file)) {
        $file = BASE_PATH . "/app/Modules/{$module}/Resources/Lang/{$this->default}.php";
    }

    $this->fileCache[$cacheKey] = file_exists($file) ? (include $file) : [];
    return $this->fileCache[$cacheKey];
}

protected function loadGlobalFile(?string $locale = null): array
{
    $locale = $locale ?: $this->getLocale();
    $cacheKey = "global_{$locale}";
    if (isset($this->fileCache[$cacheKey])) return $this->fileCache[$cacheKey];

    $file = BASE_PATH . "/app/Resources/Lang/{$locale}.php";
    if (!file_exists($file)) {
        $file = BASE_PATH . "/app/Resources/Lang/{$this->default}.php";
    }

    $this->fileCache[$cacheKey] = file_exists($file) ? (include $file) : [];
    return $this->fileCache[$cacheKey];
}


    // OPTIONAL: load translations from DB table `translations` (module, key, locale)
 protected function loadModuleDb(string $module, ?string $locale = null): array
{
    $locale = $locale ?: $this->getLocale();
    $cacheKey = "{$module}_{$locale}";
    if (isset($this->dbCache[$cacheKey])) return $this->dbCache[$cacheKey];

    if (!$this->db) {
        $this->dbCache[$cacheKey] = [];
        return [];
    }

    $stmt = $this->db->prepare("SELECT `key`, `value` FROM translations WHERE module = :module AND locale = :locale");
    $stmt->execute(['module' => $module, 'locale' => $locale]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) {
        $out[$r['key']] = $r['value'];
    }
    $this->dbCache[$cacheKey] = $out;
    return $out;
}

    // key format: "Module::group.key" OR "Module::key" OR "core::menu.home"
    public function trans(string $key, array $replacements = []): string
    {
        $locale = $this->getLocale();

        // parse module if provided
        if (strpos($key, '::') !== false) {
            [$module, $path] = explode('::', $key, 2);
        } else {
            // default module 'Core' (arba 'core')
            $module = 'Core';
            $path = $key;
        }

        // 1) DB
        $dbArr = $this->loadModuleDb($module, $locale);
        if (isset($dbArr[$path]) && $dbArr[$path] !== '') {
            return $this->applyReplacements($dbArr[$path], $replacements);
        }

        // 2) File
        $fileArr = $this->loadModuleFile($module, $locale);
        $val = $this->arrayGet($fileArr, $path);
        if ($val !== null) {
            return $this->applyReplacements($val, $replacements);
        }

        // 3) Fallback to default locale file
        if ($locale !== $this->default) {
            $fileArr = $this->loadModuleFile($module, $this->default);
            $val = $this->arrayGet($fileArr, $path);
            if ($val !== null) {
                return $this->applyReplacements($val, $replacements);
            }

            // fallback DB default locale
            $dbArr = $this->loadModuleDb($module, $this->default);
            if (isset($dbArr[$path]) && $dbArr[$path] !== '') {
                return $this->applyReplacements($dbArr[$path], $replacements);
            }
        }

        // 4) Global file fallback (shared translations like nav/footer)
$globalArr = $this->loadGlobalFile($locale);
$val = $this->arrayGet($globalArr, $key);
if ($val !== null) {
    return $this->applyReplacements($val, $replacements);
}

// Fallback į default global file
if ($locale !== $this->default) {
    $globalArr = $this->loadGlobalFile($this->default);
    $val = $this->arrayGet($globalArr, $key);
    if ($val !== null) {
        return $this->applyReplacements($val, $replacements);
    }
}


        // last resort: return path
        return $path;
    }

    protected function arrayGet(array $arr, string $path)
    {
        $keys = explode('.', $path);
        $cur = $arr;
        foreach ($keys as $k) {
            if (is_array($cur) && array_key_exists($k, $cur)) {
                $cur = $cur[$k];
            } else {
                return null;
            }
        }
        return $cur;
    }

    protected function applyReplacements(string $str, array $replacements): string
    {
        foreach ($replacements as $k => $v) {
            $str = str_replace("{{$k}}", (string)$v, $str);
        }
        return $str;
    }
}
