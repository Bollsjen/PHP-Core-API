<?php

namespace App\Core;

abstract class Controller {
    protected function Json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        return json_encode($data);
    }

    protected function Ok($data = null) {
        return $this->Json($data, 200);
    }

    protected function Created($data = null) {
        return $this->Json($data, 201);
    }

    protected function NoContent() {
        http_response_code(204);
        return null;
    }

    protected function BadRequest($message = 'Bad Request') {
        return $this->Json(['error' => $message], 400);
    }

    protected function NotFound($message = 'Not Found') {
        return $this->Json(['error' => $message], 404);
    }

    protected function Text($content, $statusCode = 200) {
        header('Content-Type: text/plain');
        http_response_code($statusCode);
        return $content;
    }
}