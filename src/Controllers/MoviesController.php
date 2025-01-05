<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * @BasePath("/api/hello-world")
 */
class MoviesController extends Controller {
    /**
     * @Route(path="", method="GET")
     * @Auth
     */
    public function get($params) {
        $users = [
            ['id' => 1, 'title' => 'How to train your dragon'],
            ['id' => 2, 'title' => 'The grinch']
        ];
        return $this->Ok($users);
    }
}