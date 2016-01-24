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
    protected $_advAclModelCacheConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \ShopGo\AdvancedAcl\Model\Cache\Config $advAclModelCacheConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CacheConfig $advAclModelCacheConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_advAclModelCacheConfig = $advAclModelCacheConfig;
    }

    /**
     * Check if cache additional block is enabled
     *
     * @return boolean
     */
    public function isCacheAdditionalAllowed()
    {
        $access = $this->_advAclModelCacheConfig->getCachePageElementAccess([
            'additional' => []
        ]);

        return $access;
    }

    /**
     * Check if clean JS/CSS cache is enabled
     *
     * @return boolean
     */
    public function isCleanMediaAllowed()
    {
        $access = $this->_advAclModelCacheConfig->getCachePageElementAccess([
            'additional' => [],
            'item'       => ['attributes' => ['id' => 'media']]
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
        $access = $this->_advAclModelCacheConfig->getCachePageElementAccess([
            'additional' => [],
            'item'       => ['attributes' => ['id' => 'static_files']]
        ]);

        return $access;
    }
}
