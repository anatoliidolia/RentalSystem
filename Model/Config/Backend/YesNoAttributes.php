<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Model\Config\Backend;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class YesNoAttributes implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * YesNoAttributes constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $collection = $this->collectionFactory->create()->addVisibleFilter();

        foreach ($collection as $item) {
            if ($item->getFrontendInput() == "boolean") {
                $options[] = [
                    'value' => $item->getAttributeCode(),
                    'label' => $item->getAttributeCode()
                ];
            }
        }

        return $options;
    }
}
