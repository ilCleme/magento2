<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product inventory data
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

class Inventory extends \Magento\Backend\Block\Widget
{
    protected $_template = 'catalog/product/tab/inventory.phtml';

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\CatalogInventory\Model\Source\Stock
     */
    protected $_stock;

    /**
     * @var \Magento\CatalogInventory\Model\Source\Backorders
     */
    protected $_backorders;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Model\Source\Backorders $backorders
     * @param \Magento\CatalogInventory\Model\Source\Stock $stock
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Model\Source\Backorders $backorders,
        \Magento\CatalogInventory\Model\Source\Stock $stock,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_stock = $stock;
        $this->_backorders = $backorders;
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function getBackordersOption()
    {
        if ($this->_catalogData->isModuleEnabled('Magento_CatalogInventory')) {
            return $this->_backorders->toOptionArray();
        }

        return array();
    }

    /**
     * Retrieve stock option array
     *
     * @return array
     */
    public function getStockOption()
    {
        if ($this->_catalogData->isModuleEnabled('Magento_CatalogInventory')) {
            return $this->_stock->toOptionArray();
        }

        return array();
    }

    /**
     * Return current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return \Magento\CatalogInventory\Model\Stock\Item
     */
    public function getStockItem()
    {
        return $this->getProduct()->getStockItem();
    }

    public function isConfigurable()
    {
        return $this->getProduct()->isConfigurable();
    }

    public function getFieldValue($field)
    {
        if ($this->getStockItem()) {
            return $this->getStockItem()->getDataUsingMethod($field);
        }

        return $this->_storeConfig->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field);
    }

    public function getConfigFieldValue($field)
    {
        if ($this->getStockItem()) {
            if ($this->getStockItem()->getData('use_config_' . $field) == 0) {
                return $this->getStockItem()->getData($field);
            }
        }

        return $this->_storeConfig->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field);
    }

    public function getDefaultConfigValue($field)
    {
        return $this->_storeConfig->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field);
    }

    /**
     * Is readonly stock
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getInventoryReadonly();
    }

    public function isNew()
    {
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Check Whether product type can have fractional quantity or not
     *
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->getProduct()->getTypeInstance()->canUseQtyDecimals();
    }

    /**
     * Check if product type is virtual
     *
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->getProduct()->getIsVirtual();
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
