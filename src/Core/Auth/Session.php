<?php

namespace App\Core\Auth;

class Session {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($userId, $userData = []) {
        self::init();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_data'] = $userData;
        $_SESSION['logged_in'] = true;
    }

    public static function logout() {
        self::init();
        session_destroy();
    }

    public static function isLoggedIn(): bool {
        self::init();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function getUserId() {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUserData() {
        self::init();
        return $_SESSION['user_data'] ?? null;
    }
}