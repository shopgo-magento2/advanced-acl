<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\System;

/**
 * System configuration model
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @param \Magento\Framework\Config\ReaderInterface $configReader
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $configReader
    ) {
       $this->_configReader = $configReader;
    }

    /**
     * Get system config access permission
     *
     * @param array $element
     * @return int|boolean
     */
    public function getSystemConfigAccess($element)
    {
        $access = $this->_configReader->getConfigElement(
            $element, 'system', 'getAttribute', 'disabled'
        );

        return $access !== null ? !$access : true;
    }
}
