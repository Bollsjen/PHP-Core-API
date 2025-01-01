<?php

namespace App\Controllers;

use App\Core\Controller;

class UsersController extends Controller {
    /**
     * @Route(path="/api/users", method="GET")
     * @Auth
     */
    public function index($params) {
        $users = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith']
        ];
        return $this->Ok($users);
    }
    
    /**
     * @Route(path="/api/users/{id}", method="GET")
     * @Auth
     */
    public function get($params) {
        $id = $params['id'] ?? null;
        if (!$id) return $this->BadRequest('ID is required');
        
        $user = ['id' => $id, 'name' => 'John Doe'];
        return $this->Ok($user);
    }
    
    /**
     * @Route(path="/api/users", method="POST")
     * @Auth
     */
    public function create($params) {
        $name = $params['name'] ?? null;
        if (!$name) return $this->BadRequest('Name is required');
        
        $user = ['id' => 3, 'name' => $name];
        return $this->Created($user);
    }
    
    /**
     * @Route(path="/api/users/{id}", method="PUT")
     * @Auth
     */
    public function update($params) {
        $id = $params['id'] ?? null;
        $name = $params['name'] ?? null;
        
        if (!$id || !$name) {
            return $this->BadRequest('ID and name are required');
        }
        
        $user = ['id' => $id, 'name' => $name];
        return $this->Ok($user);
    }
    
    /**
     * @Route(path="/api/users/{id}", method="DELETE")
     * @Auth
     */
    public function delete($params) {
        $id = $params['id'] ?? null;
        if (!$id) return $this->BadRequest('ID is required');
        
        return $this->NoContent();
    }
}