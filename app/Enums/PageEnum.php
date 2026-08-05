<?php

namespace App\Enums;

enum PageEnum: string
{
    const AUTH  = 'login';
    case HOME   = 'home';
    case COMMON = 'common';
    case CRYPTO_STORE = 'crypto_store';
    case ABOUT = 'about';
}

