<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ActiveActions extends Column
{

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare edit Button
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')] = [
                'send'   => [
                    'href'   => $this->context->getUrl(
                        'rental/orders/edit',
                        [
                            'entity_id' => $item['order_id'],
                        ]
                    ),
                    'label'  => __('Edit'),
                    'hidden' => false,
                ],
                'update' => [
                    'href'   => $this->context->getUrl(
                        'rental/orders/update',
                        [
                            'entity_id' => $item['order_id'],
                            'current_status' => $item['status'],
                        ]
                    ),
                    'label'  => __('Set To Close'),
                    'hidden' => false,
                ],
            ];
        }

        return $dataSource;
    }
}
