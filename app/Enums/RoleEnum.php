<?php

namespace App\Enums;

enum RoleEnum: int
{
    case USER = 0;
    case VOLUNTEER=1;
    case OFFICIAL = 2;
    case ADMIN = 3;
    case SUPERADMIN = 4;
}
