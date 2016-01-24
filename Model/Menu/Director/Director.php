<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace ShopGo\AdvancedAcl\Model\Menu\Director;

class Director extends \Magento\Backend\Model\Menu\Director\Director
{
    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_advAclModelMenuConfig;

    /**
     * @param \Magento\Backend\Model\Menu\Builder\CommandFactory $factory
     * @param \ShopGo\AdvancedAcl\Model\Menu\Config $advAclModelMenuConfig
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Builder\CommandFactory $factory,
        \ShopGo\AdvancedAcl\Model\Menu\Config $advAclModelMenuConfig
    ) {
        $this->_advAclModelMenuConfig = $advAclModelMenuConfig;

        parent::__construct($factory);
    }

    /**
     * Build menu instance
     *
     * @param array $config
     * @param \Magento\Backend\Model\Menu\Builder $builder
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function direct(array $config, \Magento\Backend\Model\Menu\Builder $builder, \Psr\Log\LoggerInterface $logger)
    {
        $disabledMenuItems = [];
        
        foreach ($config as $data) {
            if (isset($data['parent']) && isset($disabledMenuItems[$data['parent']])) {
                $access = false;
            } else {
                $access = $this->_advAclModelMenuConfig->getMenuItemAccess([
                    'item' => ['attributes' => ['id' => $data['id']]]
                ]);
            }

            if ($access) {
                $builder->processCommand($this->_getCommand($data, $logger));
            } else {
                $disabledMenuItems[$data['id']] = '';
            }
        }
    }
}
