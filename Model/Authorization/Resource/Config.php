<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Authorization\Resource;

/**
 * Authorization resource configuration model
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var \ShopGo\AdvancedAcl\Model\Menu\Config
     */
    protected $_advAclModelMenuConfig;

    /**
     * @param \Magento\Framework\Config\ReaderInterface $configReader
     * @param \ShopGo\AdvancedAcl\Model\Menu\Config $advAclModelMenuConfig
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $configReader,
        \ShopGo\AdvancedAcl\Model\Menu\Config $advAclModelMenuConfig
    ) {
        $this->_configReader = $configReader;
        $this->_advAclModelMenuConfig = $advAclModelMenuConfig;
    }

    /**
     * Get authorization resource access permission
     *
     * @param array $element
     * @return int|boolean
     */
    public function getAuthResourceAccess($element)
    {
        $menuElement = [
            'item' => [
                'attributes' => [
                    'resource' => $element['resource']['attributes']['id']
                ]
            ]
        ];

        $access = $this->_advAclModelMenuConfig->getMenuItemAccess($menuElement, true);

        if ($access === null) {
            $access = $this->_configReader->getConfigElement(
                $element, 'authorization', 'getAttribute', 'disabled'
            );
        }

        return $access !== null ? !$access : true;
    }
}
