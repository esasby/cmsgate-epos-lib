<?php


namespace esas\cmsgate\epos\hro\client;


use esas\cmsgate\hro\HROFactory;
use esas\cmsgate\hro\HROManager;

class CompletionPanelEposHROFactory implements HROFactory
{
    /**
     * @return CompletionPanelEposHRO
     */
    public static function findBuilder() {
        return HROManager::fromRegistry()->getImplementation(CompletionPanelEposHRO::class, CompletionPanelEposHRO_v1::class);
    }
}