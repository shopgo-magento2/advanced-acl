<?php
/**
 * Cache grid collection
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Cache\ResourceModel\Grid;

class Collection extends \Magento\Backend\Model\Cache\ResourceModel\Grid\Collection
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\Source\DisallowedCache
     */
    protected $_disallowedCache;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \ShopGo\AdvancedAcl\Model\Source\DisallowedCache $disallowedCache
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \ShopGo\AdvancedAcl\Model\Source\DisallowedCache $disallowedCache
    ) {
        $this->_disallowedCache = $disallowedCache->toOptionArray();

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
                if (isset($this->_disallowedCache[$type->getId()])) {
                    continue;
                }

                $this->addItem($type);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
