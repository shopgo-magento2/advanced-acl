<?php
/**
 * Cache grid collection
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Cache\ResourceModel\Grid;

class Collection extends \Magento\Backend\Model\Cache\ResourceModel\Grid\Collection
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\Cache\Config
     */
    protected $_advAclModelCacheConfig;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \ShopGo\AdvancedAcl\Model\Cache\Config $advAclModelCacheConfig
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \ShopGo\AdvancedAcl\Model\Cache\Config $advAclModelCacheConfig
    ) {
        $this->_advAclModelCacheConfig = $advAclModelCacheConfig;

        parent::__construct($entityFactory, $cacheTypeList);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            foreach ($this->_cacheTypeList->getTypes() as $type) {
                $access = $this->_advAclModelCacheConfig->getCachePageElementAccess([
                    'types' => [],
                    'type'  => ['attributes' => ['id' => $type->getId()]]
                ]);

                if ($access) {
                    $this->addItem($type);
                }
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
