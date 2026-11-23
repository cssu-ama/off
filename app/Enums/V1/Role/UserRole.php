<?php

namespace App\Enums\V1\Role;

enum UserRole: string
{
    case MANAGER = 'manager';
    case ADMIN = 'admin';
    case WRITER = 'writer';
    case USER = 'user';
}
