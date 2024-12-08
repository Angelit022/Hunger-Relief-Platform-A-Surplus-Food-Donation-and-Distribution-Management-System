<?php
class AdminService {
    private $adminCredentials = [
        'donation_admin' => [
            'email' => 'donAdmin@gmail.com',
            'password' => 'admin'
        ],
        'request_admin' => [
            'email' => 'reqAdmin@gmail.com',
            'password' => 'admin'
        ],
        'user_admin' => [
            'email' => 'userAdmin@gmail.com',
            'password' => 'admin'
        ]
    ];

    public function getAdminRole($email, $password) {
        foreach ($this->adminCredentials as $role => $credentials) {
            if ($email === $credentials['email'] && $password === $credentials['password']) {
                return $role;
            }
        }
        return null;
    }

    public function getAdminCredentials() {
        return $this->adminCredentials;
    }
}
?>