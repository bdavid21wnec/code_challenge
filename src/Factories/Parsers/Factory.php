<?php
namespace Src\Factories\Parsers;

use Src\Contracts\iFactory;
use Src\Contracts\iParser;

class Factory implements iFactory {
    
    const FORMATTER_NAMESPACE = 'Src\Parsers';    
    
    public function create($type) {
        if (empty($type) || !is_string($type)) {
            throw new Exception("Type must not be empty and a string");
        }
        
        $className = implode(
                '\\', 
                [
                    self::FORMATTER_NAMESPACE,
                    ucfirst($type) . 'Parser'
                ]
        );
        
        if (!$this->validate($className)) {
            throw new Exception("Class does not exist "
                    . "or does not implement " . iParser::class);
        }
        
        return new $className();
    }

    private function validate($className)
    {
        return class_exists($className)
            && (new \ReflectionClass($className))->implementsInterface(iParser::class);
    }
}
