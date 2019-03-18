<?php

namespace Src\Services;

use Src\Contracts\iManager;
use Src\Contracts\iLogger;
use Src\Contracts\iFormatter;
use Src\Models\Lead;
use Src\Contracts\iModel;
use Src\Contracts\iProcessor;

class LeadsManager implements iManager {   
    private $logger;
    private $processor;
    private $formatter;
    private $data = [];
    
    public function __construct(
        iLogger $logger,
        iProcessor $processor, 
        iFormatter $formatter
    )
    {
        $this->logger = $logger;
        $this->processor = $processor;
        $this->formatter = $formatter;
    }

    public function setData($data)
    {
        if (!isset($data->leads)) {
            $this->logger->error("Missing Leads key from data set");
            throw new Exception("Missing Leads key from data set");
        }

        foreach($data->leads as $idx => $lead) {
            if (!$this->isValid($lead)) {
                $this->logger->error("Invalid Lead at index: $idx");
                continue;
            }

            $this->data[] = (new Lead())
                    ->setId($lead->_id)
                    ->setEmail($lead->email)
                    ->setFirstName($lead->firstName)
                    ->setLastName($lead->lastName)
                    ->setAddress($lead->address)
                    ->setEntryDate($lead->entryDate);
        }
        
        return $this;
    }

    public function process()
    {
        $ret = [];
        
        $this->logger->info("STARTED PROCESSING LEADS");
        $leads = $this->processor->process($this->data);
        $this->logger->info("FINISHED PROCESSING LEADS");
        
        // reformat deduped leads back into original format
        foreach($leads as $lead) {
            if ($lead instanceof iModel) {
                $ret['leads'][] = $lead->serialize();
            }
        }

        return $this
                ->formatter->format($ret);
    }

    private function isValid(\stdClass $data)
    {
        return isset($data->_id)
            && isset($data->email)
            && isset($data->firstName)
            && isset($data->lastName)
            && isset($data->address)
            && isset($data->entryDate);
    }
}
