<?php

declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Adminhtml\Menu\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Ranges
 */
class StatusMatrix extends AbstractFieldArray
{

    /**
     * Prepare rendering the new field by adding all the needed columns
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {

        $this->addColumn('status_text', [
            'label' => __('Status Text'),
            'type' => 'text',
        ]);

        $this->addColumn('status_id', [
            'label' => __('Status ID'),
            'type' => 'number',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
