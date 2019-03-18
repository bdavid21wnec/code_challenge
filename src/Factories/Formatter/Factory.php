<?php
namespace Src\Factories\Formatter;

use Src\Contracts\iFactory;
use Src\Contracts\iFormatter;

class Factory implements iFactory {

    const FORMATTER_NAMESPACE = 'Src\Formatters';    
    
    public function create($type) {
        if (empty($type) || !is_string($type)) {
            throw new Exception("Type must not be empty and a string");
        }
        
        $className = implode(
                '\\', 
                [
                    self::FORMATTER_NAMESPACE,
                    ucfirst($type) . 'Formatter'
                ]
        );
        
        if (!$this->validate($className)) {
            throw new Exception("Class does not exist "
                    . "or does not implement " . iFormatter::class);
        }
        
        return new $className();
    }

    private function validate($className)
    {
        return class_exists($className)
            && (new \ReflectionClass($className))->implementsInterface(iFormatter::class);
    }
}
