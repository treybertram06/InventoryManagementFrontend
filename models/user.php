<?php

enum UserRole: string {
    case Admin = 'admin'; // These must match the db values
    case Technician = 'technician';
}

class User {
    public int $ID;
    public string $username;
    public string $email;
    public UserRole $role;

    function __construct(int $ID, string $username, string $email, UserRole $role) {
        $this->ID = $ID;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
    }

    public function get_name(): string {
        return $this->username;
    }



}