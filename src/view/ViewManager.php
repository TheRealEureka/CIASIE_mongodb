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
    public static function getView($name, array $attributs = array()): string
    {
        if(file_exists(self::$path.$name)){
            $view = file_get_contents(self::$path.$name);
            foreach ($attributs as $key => $value){
                $view = str_replace("{{".$key."}}", $value, $view);
            }
            return preg_replace("/{{.*}}/", "", $view);
        }
        throw new \Exception("View not found");
    }
}