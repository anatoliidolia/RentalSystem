<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Adminhtml\Menu\Field;

use Magento\Framework\View\Element\Html\Select;

class SubTypeColumn extends Select
{
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     *
     * @return $this
     */
    public function setInputName(string $value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value): static
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * Prepare array for admin panel
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        return [
            ['label' => '0', 'value' => '0'],
            ['label' => '1', 'value' => '1'],
            ['label' => '2', 'value' => '2'],
            ['label' => '5', 'value' => '5'],
            ['label' => '10', 'value' => '10'],
            ['label' => '20', 'value' => '20'],
            ['label' => '50', 'value' => '50'],
            ['label' => '100', 'value' => '100'],
        ];
    }
}
