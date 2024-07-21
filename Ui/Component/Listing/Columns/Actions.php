<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * Prepare edit Button
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['send'] = [
                    'href' => $this->context->getUrl(
                        'rental/orders/edit',
                        [
                            'entity_id' => $item['order_id'],
                        ]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
        }

        return $dataSource;
    }
}
