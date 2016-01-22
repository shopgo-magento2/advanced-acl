<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Cache;

/**
 * Cache configuration model
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache disallowed types node
     */
    const CACHE_DISALLOWED_TYPES = 'disallowed_types';

    /**
     * Cache type node
     */
    const CACHE_TYPE_NODE = 'type';

    /**
     * Cache additional node
     */
    const CACHE_ADDITIONAL = 'additional';

    /**
     * Cache additional media node
     */
    const CACHE_ADDITIONAL_MEDIA = 'media';

    /**
     * Cache additional static files node
     */
    const CACHE_ADDITIONAL_STATIC = 'static_files';

    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var \ShopGo\AdvancedAcl\Model\Source\DisallowedCache
     */
    protected $_disallowedCache;

    /**
     * @param \Magento\Framework\Config\ReaderInterface $configReader
     * @param \ShopGo\AdvancedAcl\Model\Source\DisallowedCache $disallowedCache
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $configReader,
        \ShopGo\AdvancedAcl\Model\Source\DisallowedCache $disallowedCache
    ) {
        $this->_configReader = $configReader;
        $this->_disallowedCache = $disallowedCache->toOptionArray();
    }

    /**
     * Get cache config xpath
     *
     * @param array $element
     * @return string
     */
    protected function _getCacheConfigXpath($element)
    {
        $xpath = '/';

        foreach ($element as $_element => $data) {
            $attributesText = '';
            $valueText = '';

            if (isset($data['attributes'])) {
                foreach ($data['attributes'] as $attrKey => $attrVal) {
                    $attributesText .= '[@' . $attrKey . '="' . $attrVal . '"]';
                }
            }
            if (isset($data['value'])) {
                $valueText .= '[.="' . $data['value'] . '"]';
            }

            $xpath .= '/' . $_element . $attributesText . $valueText;
        }

        return $xpath;
    }

    /**
     * Get element node value
     *
     * @param array $element
     * @return string
     */
    protected function _getElementNodeValue($element)
    {
        $value   = '';
        $element = $this->_configReader->getAclDomXpathValue(
            $this->_configReader->getConfigXpath($element)
        );

        if ($element->item(0) !== null
            && $element->item(0)->nodeValue !== '') {
            $value = $element->item(0)->nodeValue;
        }

        return $value;
    }

    /**
     * Get disallowed cache types config
     *
     * @param array $element
     * @return string
     */
    protected function _getDisallowedCacheTypesConfig($element)
    {
        $elementValue = $this->_getElementNodeValue($element);

        return !isset($this->_disallowedCache[$elementValue]);
    }

    /**
     * Get cache config
     *
     * @param string $type
     * @param array $element
     * @return mixed
     */
    protected function _getCacheConfig($type, $element)
    {
        $config = null;

        switch ($type) {
            case self::CACHE_DISALLOWED_TYPES:
                $config = $this->_getDisallowedCacheTypesConfig($element);
                break;
            case self::CACHE_ADDITIONAL:
                $config = $this->_getElementNodeValue($element);
                break;
            default:
                $config = $this->_configReader->getAclDomXpathValue(
                    $this->_configReader->getConfigXpath($element)
                );
        }

        return $config;
    }

    /**
     * Get cache page element access permission
     *
     * @param array $element
     * @return int|boolean
     */
    public function getCachePageElementAccess($element)
    {
        $access = true;

        if (!$this->_configReader->aclFileExists()
            || !$this->_configReader->validateAclDom()) {
            return $access;
        }

        $adminUserAcl = $this->_configReader->getAdminUserAcl();

        if (!$adminUserAcl) {
            return $access;
        }

        $type = key($element);

        $element = array_merge(
            [
                'cache' => [
                    'attributes' => [
                        'id' => $adminUserAcl->getAttribute('cache')
                    ]
                ]
            ],
            $element
        );

        $access = $this->_getCacheConfig($type, $element);

        return $access !== null ? $access : true;
    }
}
