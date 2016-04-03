<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Exception\LocalizedException;

/**
 * System config data save before observer
 */
class SystemConfigSaveBefore implements ObserverInterface
{
    /**
     * Config structure model
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * Advanced ACL system config model
     *
     * @var \ShopGo\AdvancedAcl\Model\System\Config
     */
    protected $_advAclModelSystemConfig;

    /**
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \ShopGo\AdvancedAcl\Model\System\Config $advAclModelSystemConfig
     */
    public function __construct(
        \Magento\Config\Model\Config\Structure $configStructure,
        \ShopGo\AdvancedAcl\Model\System\Config $advAclModelSystemConfig
    ) {
        $this->_configStructure = $configStructure;
        $this->_advAclModelSystemConfig = $advAclModelSystemConfig;
    }

    /**
     * Check whether a system configuration field is accessable
     *
     * @param array $elements
     * @return void
     * @throws LocalizedException
     */
    protected function _checkSystemConfigFieldAccess($elements)
    {
        $access = $this->_advAclModelSystemConfig
            ->getSystemConfigAccess($elements);

        if (!$access) {
            throw new LocalizedException(__('Access to some system configuration fields is now allowed!'));
        }
    }

    /**
     * Handle config data before save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $configData = $observer->getEvent()['config_data']->getData();
        $configPath = explode('/', $configData['path']);

        $elements = [
            'section' => ['attributes' => ['id' => $configPath[0]]],
            'group'   => ['attributes' => ['id' => $configPath[1]]],
            'field'   => ['attributes' => ['id' => $configPath[2]]]
        ];

        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section  = $this->_configStructure->getElement($configPath[0]);
        if (isset($section->getData()['tab'])) {
            $elements = array_merge(
                ['tab' => ['attributes' => ['id' => $section->getData()['tab']]]],
                $elements
            );
        }

        //@TODO: Not a pretty sight!
        //This is an expensive way to achieve a fallback mechanism.
        //Will look for a better way to do it later.
        $_elements = [];
        foreach ($elements as $elementName => $elementData) {
            $_elements[$elementName] = $elementData;
            $this->_checkSystemConfigFieldAccess($_elements);
        }
    }
}
