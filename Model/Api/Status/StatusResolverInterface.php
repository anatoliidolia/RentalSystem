<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Api\Status;

interface StatusResolverInterface
{
    /**
     * @param int    $orderId
     * @param int $status
     *
     * @return mixed
     */
    public function resolve(int $orderId, int $status): bool;

    /**
     * @param int    $orderId
     * @param string $status
     *
     * @return void
     */
    public function infoLogger(int $orderId, string $status): void;
}
