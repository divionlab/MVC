<?php
namespace Modules\Home\Controllers;

use Core\Http\Request;
use Core\View\View;

class HomeController
{
    public function ShowHome(Request $request)
    {
        return View::renderLayout('main', [
            'content' => View::render('Home::index', [
                'lang' => $request->getLang(),
                'csrf' => $_SESSION['_token']
            ])
        ]);
    }
}
