<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Block\Adminhtml\Menu\Field;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;

class TextSubFields extends AbstractBlock
{
    /**
     * @var mixed|string
     */
    private mixed $_elementName;
    /**
     * @var mixed|string
     */
    private mixed $_elementId;
    /**
     * @var mixed|string
     */
    private mixed $_elementValue;

    /**
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_elementName = $data['element_name'] ?? 'subtype_column';
        $this->_elementId = $data['element_id'] ?? 'subtype_column_id';
        $this->_elementValue = $data['element_value'] ?? '';
    }

    /**
     * Set "name" for <input> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName(string $value): self
    {
        $this->_elementName = $value;
        return $this;
    }

    /**
     * Set "id" for <input> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputId(string $value): self
    {
        $this->_elementId = $value;
        return $this;
    }

    /**
     * Set "value" for <input> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputValue(string $value): self
    {
        $this->_elementValue = $value;
        return $this;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        return sprintf(
            '<input type="text" name="%s" id="%s" value="%s" />',
            $this->_elementName,
            $this->_elementId,
            $this->escapeHtml($this->_elementValue)
        );
    }
}
