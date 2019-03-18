<?php
namespace Src\Services;

use Src\Contracts\iProcessor;
use Src\Contracts\iLogger;
use Src\Models\Lead;

class LeadProcessor implements iProcessor {
    private $dups = [
        'id' => [],
        'email' => [],
    ];

    private $logger;

    public function __construct(iLogger $logger) 
    {
        $this->logger = $logger;
    }

    public function process($leads) 
    {              
        $this->setDups($leads, 'id');
        $this->processDups($leads, 'id');

        $this->setDups($leads, 'email');
        $this->processDups($leads, 'email');

        return $leads;
    }

    private function setDups($leads, $key)
    {
        foreach($leads as $idx => $lead) {
            if ($lead instanceof Lead) {
                $leadKey = ('id' == $key) 
                ? $lead->getId() 
                : $lead->getEmail();
                
                $this->dups[$key][$leadKey][] = $idx;
            }
        }
    }

    private function processDups(&$leads, $key)
    {
        foreach($this->dups[$key] as $k => $duplicates) {
            $dupCount = count($duplicates);
            if ($dupCount > 1) {
                $mostRecent = null;
                $idxToKeep = 0;
                foreach($duplicates as $idx => $leadIdx) {
                    $lead = $leads[$leadIdx];
                    $entryDate = $lead->getEntryDate();
                    if ($mostRecent === null) {
                        $mostRecent = $entryDate;
                        $idxToKeep = $leadIdx;
                    } elseif ($entryDate > $mostRecent) {
                        $mostRecent = $entryDate;
                        $idxToKeep = $leadIdx;
                    } elseif ($entryDate == $mostRecent) {
                        $idxToKeep = ($idxToKeep > $leadIdx) ? $idxToKeep : $leadIdx;
                    }
                }
                
                $this->updateDuplicateLeads($leads, $idxToKeep, $duplicates, $k);
                
                /*
                 * 
                 * I WASNT SURE IF DUPLICATES SHOULD BE REMOVED COMPLETELY
                 * OR SHOULD BE UPDATED WITH THE CORRECT INFO AND LEFT IN
                 * AFTER RE-READING THE INSTRUCTIONS...SEEMS LIKE DUPLICATES SHOULD BE
                 * LEFT IN, BUT WITH UPDATED DATA
                 * IF, THAT IS INCORRECT, PLEASE COMMENT OUT THE ABOVE CODE
                 * AND UNCOMMENT THE CODE BELOW
                 *              
                 */
//                $this->removeBadLeads(
//                    $leads, 
//                    $idxToKeep, 
//                    $duplicates, 
//                    $k
//                );
            }
        }
    }
    
    private function updateDuplicateLeads(&$leads, $idxToKeep, $duplicates, $key) 
    {
        $source = $leads[$idxToKeep];

        $this->logger->info("DUPLICATE ENTRIES FOUND FOR: $key, NUMBER OF DUPLICATES: " . count($duplicates));
        $this->logger->info(
            "SOURCE RECORD: " . print_r($source->serialize(), true)
        );
        
        foreach($duplicates as $k => $v) {
            if ($v != $idxToKeep) {
                $destination = $leads[$v];                
                if ($destination instanceof Lead 
                        && $source instanceof Lead) {
                    
                    $this->logger->info(
                        "OUTPUT RECORD BEFORE UPDATE: " . print_r($destination->serialize(), true)
                    );
                    
                    $this->updateFieldValueIfDifferent($source, $destination, 'Id');
                    $this->updateFieldValueIfDifferent($source, $destination, 'Email');
                    $this->updateFieldValueIfDifferent($source, $destination, 'FirstName');
                    $this->updateFieldValueIfDifferent($source, $destination, 'LastName');
                    $this->updateFieldValueIfDifferent($source, $destination, 'Address');
                    $this->updateFieldValueIfDifferent($source, $destination, 'EntryDate');
                    
                    $this->logger->info(
                        "OUTPUT RECORD AFTER UPDATE: " . print_r($destination->serialize(), true)
                    );
                    
                    $leads[$v] = $destination;
                }
            }
        }
    }
    
    private function updateFieldValueIfDifferent(Lead $sourceLead, Lead $destinationLead, $functionName)
    {
        $get = "get$functionName";
        $set = "set$functionName";
        
        if ($sourceLead->$get() != $destinationLead->$get()) {
            
            if ($destinationLead->$get() instanceof \DateTime
                    && $sourceLead->$get() instanceof \DateTime) {
                $this->logger->info("UPDATED FIELD NAME $functionName: {$destinationLead->$get()->format(DATE_RFC3339)} TO {$sourceLead->$get()->format(DATE_RFC3339)}");                
            } else {
                $this->logger->info("UPDATED FIELD NAME $functionName: {$destinationLead->$get()} TO {$sourceLead->$get()}");
            }
            
            $destinationLead->$set(
                $sourceLead->$get()
            );                       
        }
    }

    private function removeBadLeads(&$leads, $idxToKeep, $duplicates, $key) {
        foreach($duplicates as $k => $v) {
            if ($v != $idxToKeep) {
                $lead = $leads[$v];
                unset($leads[$v]);
            }
        }
    }
}
