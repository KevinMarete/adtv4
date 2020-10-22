<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        if (empty(session()->get('user_id'))) {
           // echo 'Sssion '.session()->get('user_id');
          // return redirect()->to(base_url() . '/public/logout/2');
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        // Do something here
    }

}
