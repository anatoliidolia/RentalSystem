<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\ResourceModel\Item\Grid;

use Magento\Framework\Serialize\SerializerInterface;
use PeachCode\RentalSystem\Model\Config\Config;

class StatusFilter{

    /**
     * @param Config              $config
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly Config $config,
        private readonly SerializerInterface $serializer,
    ) {}

    /**
     * @param string $status
     *
     * @return int
     */
    public function filterStatus(string $status) : int
    {
        $statuses = $this->config->getRentOrderStatusMapper();

        $statusesConfig = $this->serializer->unserialize($statuses);

        foreach ($statusesConfig as $range) {
            if($range['status_text'] == $status){
                return (int)$range['status_id'];
            }
        }

        return 0;
    }
}
