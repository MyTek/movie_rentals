<?php

namespace App\Enums;

enum MovieTag: string
{
    case TRENDING = 'trending';
    case UNDER = 'under';
    case NONE = '';
}
