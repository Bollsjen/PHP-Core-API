<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {

    /**
     * @Route(path="/api/auth/login", method="POST")
     */
    function login($params){
        Session::login($user->id, $user->toArray());
        return $this->Ok(['message' => 'Logged in successfully']);
    }

    /**
     * @Route(path="/api/auth/logout", method="DELETE")
     */
    function logout(){
        Session::logout();

        return $this->NoContent();
    }
}