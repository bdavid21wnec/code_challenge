<?php
namespace Src\Services;

use Src\Contracts\iLogger;

class Logger implements iLogger 
{
    private static $instance = null;
    
    private $logs = [];
    
    // stuff for singleton in php
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
    
    public static function getInstance()
    {
        if (!isset(static::$instance) )
        {
            static::$instance = new static();
        }

        return static::$instance;
    }
    
    public function error($msg) 
    {
        $this->log($msg, '[ERROR]');
    }
    
    public function info($msg) 
    {
        $this->log($msg, '[INFO]');
    }

    public function getLogs() 
    {
        return implode("\n", $this->logs);
    }
    
    private function log($msg, $level)
    {
        $time = '['.$this->getTime().']';
        $log = ($level ? $level.$time."\t".$msg : $time."\t".$msg)."\n";        
        $this->logs[]= $log;
        
        echo $log;
    }
    
    private function getTime()
    {
        return date(self::TIME_FORMAT);
    }
}
