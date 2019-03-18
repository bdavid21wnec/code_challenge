<?php
namespace Src\Contracts;

interface iLogger {    
    const TIME_FORMAT = 'd/m/Y - H:i:s';
    
    public function info($msg);
    
    public function error($msg);
    
    public function getLogs();
}
