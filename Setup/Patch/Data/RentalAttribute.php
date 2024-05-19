<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RentalAttribute implements DataPatchInterface
{

    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'is_rent_available', [
            'type'                    => 'int',
            'backend'                 => '',
            'frontend'                => '',
            'label'                   => 'Is Rent Available',
            'input'                   => 'boolean',
            'source'                  => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'default'                 => 0,
            'class'                   => '',
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => false,
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'used_in_product_listing' => true,
            'unique'                  => false,
            'apply_to'                => '',
        ]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'base_rent_price', [
            'type'                    => 'decimal',
            'backend'                 => '',
            'frontend'                => '',
            'label'                   => 'Base Rent Price',
            'input'                   => 'price',
            'class'                   => '',
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => false,
            'default'                 => '',
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'used_in_product_listing' => true,
            'unique'                  => false,
            'apply_to'                => '',
        ]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'rent_qty', [
            'type'                    => 'int',
            'backend'                 => '',
            'frontend'                => '',
            'label'                   => 'Rent QTY',
            'input'                   => 'text',
            'class'                   => '',
            'default'                 => 0,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => false,
            'validate_class'          => 'validate-digits',
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'used_in_product_listing' => true,
            'unique'                  => false,
            'apply_to'                => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
