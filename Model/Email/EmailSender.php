<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Email;

use DateTime;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Model\Order;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use PeachCode\RentalSystem\Logger\Logger;

class EmailSender
{

    /**
     * @param Logger                $logger
     * @param TransportBuilder      $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface     $localeDate
     * @param Config                $config
     */
    public function __construct(
        private readonly Logger $logger,
        private readonly TransportBuilder                                     $transportBuilder,
        private readonly StoreManagerInterface                                $storeManager,
        private readonly TimezoneInterface $localeDate,
        private readonly Config $config,
    ) {
    }

    /**
     * TODO: need to configure email Template
     *
     * @param     $rentOrder Order
     *
     * @return boolean
     */
    public function sendEmail(Order $rentOrder): bool
    {
        try {
            $isActive = $this->config->getRentOrderEmail();

            if(!$isActive) return false;

            $templateVariables = $this->prepareVariables($rentOrder);

            $customerEmail = $rentOrder->getCustomerEmail();

            $store = $this->storeManager->getStore()->getId();
            $transport = $this->transportBuilder->setTemplateIdentifier($this->config->getRentOrderEmailTemplate())
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars($templateVariables)
                ->setFrom('general')
                ->addTo($customerEmail)
                ->addBcc($this->config->getRentOrderEmailSendTo())
                ->getTransport();

            $transport->sendMessage();
        } catch (Exception $e) {
            $this->logger->info($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Prepare variables for email template
     *
     * @param     $rentOrder Order
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareVariables(Order $rentOrder): array
    {

        return [
            'store' => $this->storeManager->getStore(),
            'order_id'=> $rentOrder->getId(),
            'customer_email' => $rentOrder->getCustomerEmail(),
            'total_items' => $rentOrder->getTotalItems(),
            'total_summ' => $rentOrder->getTotalSumm(),
            'created_at' => $this->formatDate($rentOrder->getCreatedAt()),
            'address_html' => $rentOrder->getHtmlAddress(),
            'order' => $rentOrder,
        ];

    }

    /**
     * Retrieve formatting date
     *
     * @param DateTime|string|null $date
     * @param int                  $format
     * @param bool                 $showTime
     * @param string|null          $timezone
     *
     * @return string
     * @throws Exception
     */
    public function formatDate(
        DateTime|string $date = null,
        int $format = \IntlDateFormatter::SHORT,
        bool $showTime = false,
        string $timezone = null
    ): string {
        $date =  new DateTime($date ?? '');
        return $this->localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }
}
