<?php

namespace App\Entity;

enum Rol: string
{
    case ADMIN = 'Admin';
    case USER = 'User';
}
