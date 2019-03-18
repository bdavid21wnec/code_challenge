<?php
namespace Src\Contracts;

interface iManager {
    const JSON_FORMAT = 'json';
    
    public function setData($data);
    
    public function process();    
}
