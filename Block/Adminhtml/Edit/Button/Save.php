<?php

namespace PeachCode\RentalSystem\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

class Save extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'your_form_ui_component_name.your_form_ui_component_name',
                                'actionName' => 'customSave',
                                'params' => [
                                    false,
                                ],
                            ],
                        ],
                    ],
                ],
                'form-role' => 'save',
            ],
            'on_click' => '',
            'sort_order' => 90,
        ];
    }
}
