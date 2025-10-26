<h2>Welcome to Home Module!</h2>
<p>This is your first modular page.</p>
<?php
echo __t('Home::title');                 // "Home" arba "Pagrindinis"
echo __t('Home::menu.home');             // nested key
echo __t('Home::welcome', ['name' => 'Deividas']);
