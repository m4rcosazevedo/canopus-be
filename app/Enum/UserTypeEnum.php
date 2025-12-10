<?php

namespace App\Enum;

enum UserTypeEnum: string
{
    case MONTHLY = 'monthly';
    case PACKAGE = 'package';
    case UNLIMITED = 'unlimited';
}
