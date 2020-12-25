<?php declare(strict_types=1);

namespace ImageViewer\Controller;

use ImageViewer\Database;
use ImageViewer\OutputWrapper;

class RegisterController extends AbstractController
{
    private Database $db;

    public function __construct(OutputWrapper $output, Database $db)
    {
        parent::__construct($output);
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
