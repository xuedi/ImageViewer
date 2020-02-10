<?php declare(strict_types=1);

namespace ImageViewer\Controller;

use ImageViewer\Database;

class RegisterController extends AbstractController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function addUser(string $email, string $password): void
    {
        $this->jsonReturn([
            'status' => 'OK',
            'email' => $email,
            'password' => $password,
        ]);
    }
}
