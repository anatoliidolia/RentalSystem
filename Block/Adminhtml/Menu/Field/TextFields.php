<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Adminhtml\Menu\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PeachCode\RentalSystem\Block\Adminhtml\Menu\Field\TextSubFields;

/**
 * Class Ranges
 */
class TextFields extends AbstractFieldArray
{

    /**
     * Prepare rendering the new field by adding all the needed columns
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {

        $this->addColumn('location', [
            'label' => __('Store Pickup Locations'),
            'renderer' => $this->getSubTypeRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $subType = $row->getSubType();
        if ($subType !== null) {
            $options['option_' . $this->getSubTypeRenderer()->calcOptionHash($subType)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return TextSubFields
     * @throws LocalizedException
     */
    private function getSubTypeRenderer(): TextSubFields
    {
        return $this->getLayout()->createBlock(
                TextSubFields::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
        );
    }
}
