<?php
use Core\Modules\ModuleLoader;
$loader = new ModuleLoader();
$loader->loadModules(BASE_PATH . '/app/Modules');
$cssFiles = $loader->getCss();
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
    <head>
        <meta charset="UTF-8">
        <title>My Modular Site</title>
        <link rel="stylesheet" href="/Assets/Styles/main.css">
        <?php foreach ($cssFiles as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    </head>
    <body>
        <header>
            <h1><?= __t('greeting') ?></h1>
        </header>
        