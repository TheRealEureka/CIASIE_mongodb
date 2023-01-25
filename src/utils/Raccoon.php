<?php

namespace App\Utils;

class Raccoon
{
    public static function registerUser($data, &$message) : bool{
        if(!self::checkUser($data, $message)){
            return false;
        }
        $db = MongoConnector::makeConnection();
        $col = $db->selectCollection('users');
        $user = array(
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email' => $data['email'],
            'sites' => array()
        );
        $col->insertOne($user);
        return true;
    }
    public static function loginUser($data, &$message) : bool{
        if(!isset($data['username']) || !isset($data['password'])){
            $message = "Invalid data";
            return false;
        }
        $user = self::getUser($data['username']);
        if(!$user){
            $message = "User not found";
            return false;
        }
        if(!password_verify($data['password'], $user['password'])){
            $message = "Invalid password";
            return false;
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    private static function checkUser($data, &$message) : bool{
        if(!isset($data['username']) || !isset($data['password']) || !isset($data['password_verify']) || isset($data['email'])){
            $message = "Invalid data";
            return false;
        }
        if(strlen($data['username']) < 3 || strlen($data['password']) < 3){
            $message = "Username or password too short";
            return false;
        }
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            $message = "Invalid email";
            return false;
        }
        if($data['password'] != $data['password_verify']){
            $message = "Passwords do not match";
            return false;
        }
        if(self::getUser($data['username'])){
            $message = "User already exists";
            return false;
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    public static function getUser($name){
        $db = MongoConnector::makeConnection();
        $col = $db->selectCollection('users');
        return $col->findOne(['username' => $name]);
    }
}