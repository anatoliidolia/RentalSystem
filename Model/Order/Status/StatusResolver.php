<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Order\Status;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use PeachCode\RentalSystem\Model\Api\Status\StatusResolverInterface;
use PeachCode\RentalSystem\Logger\Logger;
use PeachCode\RentalSystem\Model\Order;

class StatusResolver implements StatusResolverInterface
{

    /**
     * @param Order  $order
     * @param Logger $logger
     */
    public function __construct(
        private readonly Order $order,
        private readonly Logger $logger
    ) {}

    /**
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function resolve(int $orderId, int $status): bool
    {

        $order = $this->order->loadById($orderId);

        $order->setData('status', $status+1);

        $order->save();

        // TODO: need to add try catch
        return true;
    }

    /**
     * @param int    $orderId
     * @param string $status
     *
     * @return void
     */
    public function infoLogger(int $orderId, string $status): void
    {
        $this->logger->info(
            "Order #%1 status %2", [$orderId, $status]
        );
    }
}
