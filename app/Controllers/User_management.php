<?php
namespace App\Controllers;

use App\Models\UserTest;


class User_management extends \CodeIgniter\Controller {



    public function index() {
       dd(UserTest::all());
    }

}
