<?php

namespace App\view;

class ViewManager
{
    /**
     * @var string path of the view
     */
    private static string $path = __DIR__."/front/";

    /**
     * @throws \Exception
     * @param string $name
     * @return string $html
     */
    public static function getView($name): string
    {
        if(file_exists(self::$path.$name)){
            return file_get_contents(self::$path.$name);
        }
        throw new \Exception("View not found");
    }
}