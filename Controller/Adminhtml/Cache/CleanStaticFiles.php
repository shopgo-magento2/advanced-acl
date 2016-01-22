<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Controller\Adminhtml\Cache;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use ShopGo\AdvancedAcl\Model\Cache\Config as CacheConfig;

class CleanStaticFiles extends \Magento\Backend\Controller\Adminhtml\Cache\CleanStaticFiles
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\Cache\Config
     */
    protected $_cacheConfig;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param CacheConfig $cacheConfig
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CacheConfig $cacheConfig
    ) {
        parent::__construct(
            $context,
            $cacheTypeList,
            $cacheState,
            $cacheFrontendPool,
            $resultPageFactory
        );

        $this->_cacheConfig = $cacheConfig;
    }

    /**
     * Clean static files cache
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $access = $this->_cacheConfig->getCachePageElementAccess([
            CacheConfig::CACHE_ADDITIONAL => [],
            CacheConfig::CACHE_ADDITIONAL_STATIC => []
        ]);

        if ($access) {
            $this->_objectManager->get('Magento\Framework\App\State\CleanupFiles')->clearMaterializedViewFiles();
            $this->_eventManager->dispatch('clean_static_files_cache_after');
            $this->messageManager->addSuccess(__('The static files cache has been cleaned.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('adminhtml/*');
    }
}
