<?php

namespace App\Modules\UserType\Enums;

enum UserTypeEnum: string
{
    case ROOT = 'root';
    case ADMINISTRATOR = 'administrator';
    case INSTRUCTOR = 'instructor';
    case STUDENT = 'student';
}
