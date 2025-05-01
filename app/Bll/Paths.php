<?php

namespace App\Bll;

class Paths
{
    public static function get_public_path($name)
    {
        return 'uploads'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
    }

    public static function get_storage_path($store_id,$name)
    {
        return 'uploads'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
    }

    public static function get_private_path($store_id,$name)
    {
        return 'app'.DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
    }
}
