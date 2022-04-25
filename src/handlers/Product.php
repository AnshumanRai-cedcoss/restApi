<?php

namespace Api\Handlers;

use Phalcon\Di\Injectable;

/**
 * Producr Handler class
 * to handle all the product requests
 */     
class Product extends Injectable
{
    /**
     * Get function
     *To handle all the product get requests 
     */
    function search($keyword = "")
    {

        $key = urldecode($keyword);
        $ar = explode(' ', $key);
        $array = [];
        echo "<pre>";
        $str ="";
        foreach ($ar as $key => $value) {
            $str .= $value."|";
        }
        $str = substr($str, 0, -1);
            $res = $this->mongo->users->find(["name" =>  ['$regex' => $str, '$options' => 'i']])->toArray();
            foreach ($res as $key => $value) {
                $id = (array)$value["_id"];
                $res = [
                    "id" => $id["oid"],
                    "name" => $value['name'],
                    "email" => $value['email'],
                ];
                array_push($array, $res);
            }
        print_r($array);
    }

    function get($per_page = 2,$page = 1)
    {
        $options = [
            "limit" => (int)$per_page,
            "skip"  => (int)(($page - 1) * $per_page)
        ];
        $array = [];
        $products =  $this->mongo->users->find([], $options);
        $products = $products->toArray();
        foreach ($products as $key => $value) {
            $id = (array)$value["_id"];
            $res = [
                "id" => $id["oid"],
                "name" => $value['name'],
                "email" => $value['email'],
            ];
            array_push($array, $res);
        }
    echo "<pre>";
    print_r($array);
    }

    function createToken()
    {
    }
}
