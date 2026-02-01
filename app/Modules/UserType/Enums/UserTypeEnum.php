<?php

namespace App\Modules\UserType\Enums;

enum UserTypeEnum: string
{
    case MONTHLY = 'monthly';
    case PACKAGE = 'package';
    case UNLIMITED = 'unlimited';
}
