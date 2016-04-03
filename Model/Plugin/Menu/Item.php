<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Plugin\Menu;

use Magento\Backend\Model\Menu\Item as MenuItem;

/**
 * Menu item plugin
 */
class Item
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\Menu\Config
     */
    protected $_advAclModelMenuConfig;

    /**
     * @param \ShopGo\AdvancedAcl\Model\Menu\Config $advAclModelSystemConfig
     */
    public function __construct(
        \ShopGo\AdvancedAcl\Model\Menu\Config $advAclModelMenuConfig
    ) {
        $this->_advAclModelMenuConfig = $advAclModelMenuConfig;
    }

    /**
     * Check whether menu item is allowed for current user
     *
     * @param MenuItem $subject
     * @param boolean $result
     * @return boolean
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllowed(MenuItem $subject, $result)
    {
        $menuItemId = $subject->getId();

        $access = $this->_advAclModelMenuConfig->getMenuItemAccess([
            'item' => ['attributes' => ['id' => $menuItemId]]
        ]);

        return $access;
    }
}
