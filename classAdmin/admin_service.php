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

  // Method to identify the role of an admin based on their email and password
  public function getAdminRole($email, $password) {
    // Loop through all admin credentials to find a match
    foreach ($this->adminCredentials as $role => $credentials) {
        // Check if the provided email and password match the stored credentials
        if ($email === $credentials['email'] && $password === $credentials['password']) {
            return $role; // Return the role if a match is found
        }
    }
    // Return null if no match is found
    return null;
}

// Method to retrieve all stored admin credentials
public function getAdminCredentials() {
    return $this->adminCredentials; // Return the array of admin credentials
}
}
?>