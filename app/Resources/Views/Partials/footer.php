        <footer>
            <p>&copy; <?= date('Y') ?> My Modular Site</p>
        </footer>
<?php if (!empty($loader)): ?>
    <?php foreach ($loader->getJs() as $js): ?>
    <script src="<?= $js ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
    </body>
</html>