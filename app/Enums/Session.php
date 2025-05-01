<?php

namespace App\Enums;

enum Session: string
{
    case SCHOOL_LANG = 'SCHOOL_LANG';

    public static function getLangId(): int
    {
        if (session(Session::SCHOOL_LANG->value))
            return session(Session::SCHOOL_LANG->value)->id;
        return -1;
    }
    public static function getLangCode(): string
    {


        if (session(Session::SCHOOL_LANG->value))
            return session(Session::SCHOOL_LANG->value)->code;
        return "";
    }
}
