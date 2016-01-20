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
     * @param \Magento\Framework\Config\ReaderInterface $config
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $configReader
    ) {
       $this->_configReader = $configReader;
    }

    /**
     * Get system config XPath
     *
     * @param array $elements
     * @return string
     */
    protected function _getSystemConfigXpath($elements)
    {
        $xpath = '/';

        foreach ($elements as $element => $attributes) {
            $attributesText = '';

            foreach ($attributes as $attrKey => $attrVal) {
                $attributesText .= '[@' . $attrKey . '="' . $attrVal . '"]';
            }

            $xpath .= '/' . $element . $attributesText;
        }

        return $xpath;
    }

    /**
     * Get system config access permission
     *
     * @param array $elements
     * @return int|boolean
     */
    public function getSystemConfigAccess($elements)
    {
        $access = true;

        if (!$this->_configReader->aclFileExists()
            || !$this->_configReader->validateAclDom()) {
            return $access;
        }

        $adminUserAcl = $this->_configReader->getAdminUserAcl();

        if (!$adminUserAcl) {
            return $access;
        }

        $elements = array_merge(
            ['system' => ['id' => $adminUserAcl]],
            $elements
        );

        $element = $this->_configReader->getAclDomXpathValue(
            $this->_getSystemConfigXpath($elements)
        );

        if ($element->item(0) !== null
            && $element->item(0)->getAttribute('disabled') !== '') {
            $access = !$element->item(0)->getAttribute('disabled');
        }

        return $access;
    }
}
