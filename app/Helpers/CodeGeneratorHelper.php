<?php

namespace App\Helpers;

class CodeGeneratorHelper 
{

    public function generateCode($length = 6, $type = "number")
    {
        $combination = "";
        for ($i = 0; $i < $length ; $i++) { 
            $randomValue = "";            
            if($type == "number") {
                $randomValue = rand(0, 9);
            } else {
                $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $randomValue = $characters[rand(0, strlen($characters))];
            }
            $combination .= $randomValue;  
        }
        return $combination;
    }

}