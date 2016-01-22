<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Block\Cache;

use ShopGo\AdvancedAcl\Model\Cache\Config as CacheConfig;

class Additional extends \Magento\Backend\Block\Cache\Additional
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\Cache\Config
     */
    protected $_cacheConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \ShopGo\AdvancedAcl\Model\Cache\Config $cacheConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CacheConfig $cacheConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_cacheConfig = $cacheConfig;
    }

    /**
     * Check if clean JS/CSS cache is enabled
     *
     * @return boolean
     */
    public function isCleanMediaAllowed()
    {
        $access = $this->_cacheConfig->getCachePageElementAccess([
            CacheConfig::CACHE_ADDITIONAL => [],
            CacheConfig::CACHE_ADDITIONAL_MEDIA => []
        ]);

        return $access;
    }

    /**
     * Check if clean static files cache is enabled
     *
     * @return boolean
     */
    public function isCleanStaticFilesAllowed()
    {
        $access = $this->_cacheConfig->getCachePageElementAccess([
            CacheConfig::CACHE_ADDITIONAL => [],
            CacheConfig::CACHE_ADDITIONAL_STATIC => []
        ]);

        return $access;
    }
}
