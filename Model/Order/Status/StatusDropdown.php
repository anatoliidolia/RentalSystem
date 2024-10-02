<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Order\Status;

use PeachCode\RentalSystem\Model\Config\Config;
use PeachCode\RentalSystem\Api\Data\StatusInterface;

class StatusDropdown implements StatusInterface{

    /**
     * @param Config $config
     */
    public function __construct(
        private readonly Config $config
    ) {}

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->config->getRentOrderStatusMapperArray();
    }
}
