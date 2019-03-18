<?php
namespace Src\Parsers;

use Src\Contracts\iParser;

class JsonParser implements iParser {    
    public function getData($argv) {        
        $filename = isset($argv[1]) ? $argv[1] : __DIR__ . '/../../leads.json';
        
        if (!file_exists($filename)) {
            throw new Exception("File: $filename does not exist for parsing");      
        }
        
        $data = file_get_contents($filename);       
        
        return json_decode($data);
    }
}
