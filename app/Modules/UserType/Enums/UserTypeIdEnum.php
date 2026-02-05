<?php

namespace App\Modules\UserType\Enums;

enum UserTypeIdEnum: int
{
    case ROOT = 1;
    case ADMINISTRATOR = 2;
    case INSTRUCTOR = 3;
    case STUDENT = 4;
}
