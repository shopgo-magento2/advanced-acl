<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Cache;

/**
 * Cache configuration model
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var \ShopGo\AdvancedAcl\Model\Source\DisallowedCache
     */
    protected $_disallowedCache;

    /**
     * @param \Magento\Framework\Config\ReaderInterface $configReader
     * @param \ShopGo\AdvancedAcl\Model\Source\DisallowedCache $disallowedCache
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $configReader,
        \ShopGo\AdvancedAcl\Model\Source\DisallowedCache $disallowedCache
    ) {
        $this->_configReader = $configReader;
        $this->_disallowedCache = $disallowedCache->toOptionArray();
    }

    /**
     * Get cache page element access permission
     *
     * @param array $element
     * @return int|boolean
     */
    public function getCachePageElementAccess($element)
    {
        $rootValue = reset($element);
        $root      = [key($element) => $rootValue];

        $rootAccess = $this->_configReader->getConfigElement(
            $root, 'cache', 'getAttribute', 'disabled'
        );

        if ($rootAccess == 1) {
            return !$rootAccess;
        }

        $access = $this->_configReader->getConfigElement(
            $element, 'cache', 'getAttribute', 'disabled'
        );

        return $access !== null ? !$access : true;
    }
}
