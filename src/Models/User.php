<?php

namespace App\Models;

class User {
    public static array $users = [];
    private static int $nextId = 1;

    public int $id;
    public string $username;
    public string $password;
    public string $email;
    public array $data;

    public function __construct(string $username, string $password, string $email, array $data = []) {
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->email = $email;
        $this->data = $data;

        if(count(self::$users) < 0)
            self::$users = [
                self::$nextId++ => new User('bollsjen', 'Nuster13', 'Magnus')
            ];
    }

    // Save user to static array
    public function save(): self {
        if (!isset($this->id)) {
            $this->id = self::$nextId++;
        }
        self::$users[$this->id] = $this;
        return $this;
    }

    // Get all users
    public static function all(): array {
        return array_values(self::$users);
    }

    // Find user by ID
    public static function find(int $id): ?self {
        return self::$users[$id] ?? null;
    }

    // Find user by username
    public static function findByUsername(string $username): ?self {
        foreach (self::$users as $user) {
            if ($user->username === $username) {
                return $user;
            }
        }
        return null;
    }

    // Delete user
    public static function delete(int $id): bool {
        if (isset(self::$users[$id])) {
            unset(self::$users[$id]);
            return true;
        }
        return false;
    }

    // Convert to array for API responses
    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'data' => $this->data
        ];
    }

    // Verify password
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
}