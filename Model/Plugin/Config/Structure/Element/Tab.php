<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Plugin\Config\Structure\Element;

use \Magento\Config\Model\Config\Structure\Element\Tab as ElementTab;

/**
 * Element tab plugin
 */
class Tab
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\System\Config
     */
    protected $_advAclModelSystemConfig;

    /**
     * @param \ShopGo\AdvancedAcl\Model\System\Config $advAclModelSystemConfig
     */
    public function __construct(
        \ShopGo\AdvancedAcl\Model\System\Config $advAclModelSystemConfig
    ) {
        $this->_advAclModelSystemConfig = $advAclModelSystemConfig;
    }

    /**
     * Check whether tab is visible
     *
     * @param ElementTab $subject
     * @param boolean $result
     * @return boolean
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsVisible(ElementTab $subject, $result)
    {
        $elementData = $subject->getData();

        $access = $this->_advAclModelSystemConfig
            ->getSystemConfigAccess([
                'tab' => ['attributes' => ['id' => $subject->getData()['id']]]
            ]);

        return $result && $access;
    }
}
