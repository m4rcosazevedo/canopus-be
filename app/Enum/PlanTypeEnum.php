<?php

namespace App\Enum;

enum PlanTypeEnum: string
{
    case MONTHLY = 'monthly';
    case PACKAGE = 'package';
    case UNLIMITED = 'unlimited';
}
