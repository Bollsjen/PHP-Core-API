<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Auth\Session;

/**
 * @BasePath("/api/auth")
 */
class AuthController extends Controller {

    /**
     * @Route(path="/login", method="POST")
     */
    function login($params){
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';
        $email = $params['email'] ?? '';

        $user = new User($username, $password, $email, []);

        Session::login($user->id, $user->toArray());
        return $this->Ok(['message' => 'Logged in successfully']);
    }

    /**
     * @Route(path="/logout", method="DELETE")
     */
    function logout(){
        Session::logout();

        return $this->NoContent();
    }
}