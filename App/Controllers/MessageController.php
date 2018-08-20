<?php namespace App\Controllers;

use App\Core\Controller;
use App\Core\Route;
use App\Core\Validator;
use App\Models\Message;

/**
 * Class MessageController
 * @package App\Controllers
 */
class MessageController extends Controller
{
    /**
     * Validate and store new message
     *
     * @return string
     * @throws \Exception
     */
    public function store()
    {
        //get data from request
        $data = $this->request->only('fullname', 'birthdate', 'email', 'message');
        //run validation
        $validator = new Validator($data, [
            'fullname' => 'required|min_words[2]|alpha',
            'birthdate' => 'required|before_now_date[Y-m-d]',
            'email' => 'email',
            'message' => 'required',
        ]);
        $validator->validate();

        //If data has validation errors return response with errors
        if ($validator->hasErrors()) {
            $errors = $validator->getErrors();
            return $this->responseError($errors);
        }

        //get cleaned data
        $data = $validator->getData();
        //resolve first and last name from fullname.
        $fullName = explode(' ', $data['fullname']);
        $firstName = $fullName[0];
        $lastName = $fullName[1];

        //insert new message into DB and get last inserted message
        $model = new Message();
        $message = $model->create([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'dateOfBirth' => $data['birthdate'],
            'email' => $data['email'] ?? null,
            'message' => $data['message']
        ])->addAge()->first();

        return $this->successResposnse($message);
    }

    /**
     * Build response with errors
     *
     * @param array $errors
     * @return string
     */
    private function responseError($errors = [])
    {
        //if request was from ajax return errors in json format
        if (Route::getInstance()->isAjax) {
            return json_encode(compact('errors'));
        }

        //get messages
        $model = new Message();
        $messages = $model->getPaginatedMessages($this->request->page ?: 1);
        //get total number of pages
        $pages = $model->getPages();

        //ir request was not from ajax build new view and load it
        return view('home')->render(compact('messages', 'pages', 'errors'));
    }

    /**
     * Build success response
     *
     * @param $message
     * @return string
     */
    private function successResposnse($message)
    {
        //is request method was made with ajax return message in json format
        if (Route::getInstance()->isAjax) {
            return json_encode(compact('message'));
        }
        //redirect to message list
        redirect(Route::getInstance()->url(""));
    }
}
