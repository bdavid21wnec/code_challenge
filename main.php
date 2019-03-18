<?php
// used once to autoload and support namespaces
require_once 'vendor/autoload.php';

use Src\Contracts\iManager;
use Src\Services\Logger;
use Src\Factories\Formatter\Factory as FormatterFactory;
use Src\Factories\Parsers\Factory as ParserFactory;
use Src\Services\LeadsManager;
use Src\Services\LeadProcessor;

try {
    $parser = (new ParserFactory())->create(iManager::JSON_FORMAT);
    $data = $parser->getData($argv);
    $logger = Logger::getInstance();
    $formatter = (new FormatterFactory())->create(iManager::JSON_FORMAT);
    $leadManager = new LeadsManager($logger, new LeadProcessor($logger), $formatter);
    $processed = $leadManager
                    ->setData($data)
                    ->process();
    file_put_contents('processed_leads.json', $processed);
    echo $processed;
} catch (Exception $e) {
    $logger->error($e->getMessage());
}