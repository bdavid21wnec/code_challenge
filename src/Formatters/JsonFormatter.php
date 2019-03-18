<?php
namespace Src\Formatters;

use Src\Contracts\iFormatter;

class JsonFormatter implements iFormatter {    
    public function format($data) {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        if (false === $json) {
            throw new Exception("Failed to Format Json");
        }
        
        return $json;
    }
}
