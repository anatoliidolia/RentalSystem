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
     * @param int $finalPrice
     *
     * @return boolean
     */
    public function sendEmail(Order $rentOrder,int $finalPrice): bool
    {
        try {

            $destinationEmail = $this->config->getRentOrderEmail();

            if($destinationEmail == '') return false;

            $templateVariables = $this->prepareVariables($rentOrder, $finalPrice);

            $customerEmail = $rentOrder->getCustomerEmail();

            $store = $this->storeManager->getStore()->getId();
            $transport = $this->transportBuilder->setTemplateIdentifier('rent_order')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars($templateVariables)
                ->setFrom('general')
                ->addTo($customerEmail)
                ->addBcc($destinationEmail)
                ->getTransport();

            $transport->sendMessage();
        } catch (Exception $e) {
            $this->logger->info($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param     $rentOrder Order
     * @param int $finalPrice
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareVariables(Order $rentOrder, int $finalPrice): array
    {

        return [
            'store' => $this->storeManager->getStore(),
            'order_id'=> $rentOrder->getId(),
            'customer_email' => $rentOrder->getCustomerEmail(),
            'total_items' => $rentOrder->getTotalItems(),
            'total_summ' => $finalPrice,
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
