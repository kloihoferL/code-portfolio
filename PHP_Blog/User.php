<?php

class User
{
    public function __construct(private string $username, private string $password, private string $email, private int $role) {
    }

}
?>