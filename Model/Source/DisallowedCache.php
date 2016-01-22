<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Source;

/**
 * Cache source model
 *
 * @codeCoverageIgnore
 */
class DisallowedCache implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get disallowed cache types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'full_page' => [
                'label' => __('Full Page Cache'),
                'cache_dir' => 'page_cache'
            ]
        ];
    }

    /**
     * Get cache by dir
     *
     * @param string $dir
     * @return array
     */
    public function getCacheByDir($dir)
    {
        $cache = [];

        foreach ($this->toOptionArray() as $id => $_cache) {
            if (strpos($dir, $_cache['cache_dir']) !== false) {
                $cache[$id] = $_cache;
                break;
            }
        }

        return $cache;
    }
}
