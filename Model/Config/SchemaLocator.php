<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Config;

use Magento\Framework\Module\Dir;

/**
 * Schema locator
 */
class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Module name
     */
    const MODULE_NAME = 'ShopGo_AdvancedAcl';

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema = null;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema = null;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param string $fileName
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        $fileName = ''
    ) {
        $etcDir = $moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR, self::MODULE_NAME
        );
        $this->_schema = $etcDir . $fileName;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->_perFileSchema;
    }
}
