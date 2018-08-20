<?php namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends Controller
{
    /**
     * Load index page
     *
     * @return string
     */
    public function index()
    {
        //get messages
        $model = new Message();
        $messages = $model->getPaginatedMessages($this->request->page ?: 1);
        //get number of pages
        $pages = $model->getPages();

        return view('home')->render(compact('messages', 'pages'));
    }
}
