<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Adminhtml\Menu\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PeachCode\RentalSystem\Block\Adminhtml\Menu\Field\SubTypeColumn;

/**
 * Class Ranges
 */
class PercentMatrix extends AbstractFieldArray
{

    /**
     * @var SubTypeColumn
     */
    private SubTypeColumn $subTypeColumn;

    /**
     * Prepare rendering the new field by adding all the needed columns
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {

        $this->addColumn('start', [
            'label' => __('Start Date'),
            'renderer' => $this->getSubTypeRenderer()
        ]);

        $this->addColumn('end', [
            'label' => __('End Date'),
            'renderer' => $this->getSubTypeRenderer()
        ]);

        $this->addColumn('discount',
            ['label' => __('Discount Percent'),
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
     * @return SubTypeColumn
     * @throws LocalizedException
     */
    private function getSubTypeRenderer(): SubTypeColumn
    {
        $this->subTypeColumn = $this->getLayout()->createBlock(
                SubTypeColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
        );

        return $this->subTypeColumn;
    }
}
