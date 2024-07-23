<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use PeachCode\RentalSystem\Model\Order;

class EmailSender implements ObserverInterface{

    /**
     * @param \PeachCode\RentalSystem\Model\Email\EmailSender $emailSender
     */
    public function __construct(
        private readonly \PeachCode\RentalSystem\Model\Email\EmailSender $emailSender,
    ) {
    }

    /**
     * Send email and update order
     *
     * @param Observer $observer
     *
     * @return void
     * @throws Exception
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getData('order');

        if ($order instanceof Order) {
            $sender = $this->emailSender->sendEmail($order);
            $order->setEmailSent($sender);
            $order->save();
        }
    }
}
