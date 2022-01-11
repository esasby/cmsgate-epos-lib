<?php


namespace esas\cmsgate\epos;


use esas\cmsgate\epos\protocol\EposInvoiceAddRs;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\Hooks;
use esas\cmsgate\OrderStatus;
use esas\cmsgate\wrappers\OrderWrapper;

class HooksEpos extends Hooks
{
    public function onInvoiceAddSuccess(OrderWrapper $orderWrapper, EposInvoiceAddRs $resp) {
        $orderWrapper->saveExtId($resp->getInvoiceId());
        $orderWrapper->updateStatusWithLogging(OrderStatus::pending());
    }

    public function onInvoiceAddFailed(OrderWrapper $orderWrapper, EposInvoiceAddRs $resp) {
        $orderWrapper->updateStatusWithLogging(OrderStatus::failed());
    }

    public function onCallbackStatusPayed(OrderWrapper $orderWrapper, EposInvoiceGetRs $resp) {
        $orderWrapper->updateStatusWithLogging(OrderStatus::payed());
    }

    public function onCallbackStatusCanceled(OrderWrapper $orderWrapper, EposInvoiceGetRs $resp) {
        $orderWrapper->updateStatusWithLogging(OrderStatus::canceled());
    }

    public function onCallbackStatusPending(OrderWrapper $orderWrapper, EposInvoiceGetRs $resp) {
        $orderWrapper->updateStatusWithLogging(OrderStatus::pending());
    }

    public function onCallbackStatusFailed(OrderWrapper $orderWrapper, EposInvoiceGetRs $resp) {
        $orderWrapper->updateStatusWithLogging(OrderStatus::failed());
    }
}