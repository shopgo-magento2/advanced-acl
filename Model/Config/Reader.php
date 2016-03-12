<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Configuration reader model
 */
class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * Vendor config directory path
     */
    const VENDOR_CONFIG_DIRECTORY_PATH = 'vendor/shopgo/advanced-acl-config/';

    /**
     * Var config directory path
     */
    const VAR_CONFIG_DIRECTORY_PATH = 'shopgo/advanced_acl/';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_rootDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_varDirectory;

    /**
     * @var string
     */
    protected $_vendorConfigFile;

    /**
     * @var string
     */
    protected $_varConfigFile;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var string
     */
    protected $_fileName;

    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @var \DomDocument
     */
    protected $_dom;

    /**
     * @var \DOMXPath
     */
    protected $_domXpath;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\Model\Config\Structure\Converter $converter
     * @param \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Config\Model\Config\Structure\Converter $converter,
        \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = '',
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    ) {
        $this->_filesystem  = $filesystem;
        $this->_fileName    = $fileName;
        $this->_authSession = $authSession;

        $this->validationState   = $validationState;
        $this->_domDocumentClass = $domDocumentClass;

        $this->_setRootDirectory();
        $this->_setVarDirectory();

        if ($this->_configFileExists()) {
            $this->_setDom();
            $this->_setDomXpath();
        }

        $this->_schemaFile = $schemaLocator->getSchema();

        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Set Root directory
     */
    protected function _setRootDirectory()
    {
        $this->_rootDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Set Var directory
     */
    protected function _setVarDirectory()
    {
        $this->_varDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::VAR_DIR);
    }

    /**
     * Get Vendor config file absolute path
     *
     * @return string
     */
    protected function _getVendorConfigFileAbsolutePath()
    {
        return $this->_rootDirectory->getAbsolutePath(
            self::VENDOR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );
    }

    /**
     * Get Var config file absolute path
     *
     * @return string
     */
    protected function _getVarConfigFileAbsolutePath()
    {
        return $this->_varDirectory->getAbsolutePath(
            self::VAR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );
    }

    /**
     * Get config file XML content
     *
     * @return string
     */
    protected function _getConfigFileXmlContent()
    {
        $config = '';

        if ($this->_vendorConfigFile) {
            $config = $this->_rootDirectory->readFile(
                self::VENDOR_CONFIG_DIRECTORY_PATH . $this->_fileName
            );
        }

        if (!$config && $this->_varConfigFile) {
            $config = $this->_varDirectory->readFile(
                self::VAR_CONFIG_DIRECTORY_PATH . $this->_fileName
            );
        }

        return $config;
    }

    /**
     * Set DOM
     */
    protected function _setDom()
    {
        $this->_dom = new $this->_domDocumentClass(
            $this->_getConfigFileXmlContent(),
            $this->validationState,
            [], null, null
        );
    }

    /**
     * Set DOM XPath
     */
    protected function _setDomXpath()
    {
        $this->_domXpath = new \DOMXPath($this->_dom->getDom());
    }

    /**
     * Check whether config file exists
     *
     * @return boolean
     */
    protected function _configFileExists()
    {
        $this->_vendorConfigFile = $this->_rootDirectory->isFile(
            self::VENDOR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );

        $this->_varConfigFile = $this->_varDirectory->isFile(
            self::VAR_CONFIG_DIRECTORY_PATH . $this->_fileName
        );

        return $this->_vendorConfigFile || $this->_varConfigFile;
    }

    /**
     * Validate DOM
     *
     * @return boolean
     */
    protected function _validateDom()
    {
        $result = true;
        $errors = [];

        if ($this->_dom && !$this->_dom->validate($this->_schemaFile, $errors)) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get DOM XPath value
     *
     * @param string $xpath
     * @return string
     */
    protected function _getDomXpathValue($xpath)
    {
        return $this->_domXpath->query($xpath);
    }

    /**
     * Get ACL for current admin user
     *
     * @return string|boolean
     */
    protected function _getAdminUserAcl()
    {
        $user = $this->_authSession->getUser();

        if (!$user) {
            return false;
        }

        $user = $user->getUsername();

        $element = $this->_getDomXpathValue(
            '//users/user[@name="*"]'
        );

        if ($element->item(0) !== null) {
            $excludedUsers = $this->_getDomXpathValue(
                '//users/user[@name="*"]/exclude'
            );

            if ($excludedUsers->item(0) !== null) {
                $_excludedUsers = [];

                foreach ($excludedUsers->item(0)->childNodes as $excUser) {
                    $_excludedUsers[$excUser->nodeValue] = '';
                }

                if (isset($_excludedUsers[$user])) {
                    return false;
                }
            }
        } else {
            $element = $this->_getDomXpathValue(
                '//users/user[@name="' . $user . '"]'
            );
        }

        return $element->item(0) !== null
            ? $element->item(0)
            : false;
    }

    /**
     * Get config xpath
     *
     * @param array $element
     * @return string
     */
    protected function _getConfigXpath($element)
    {
        $xpath = '/';

        foreach ($element as $_element => $data) {
            $attributesText = '';
            $valueText = '';

            switch (true) {
                case isset($data['attributes']):
                    foreach ($data['attributes'] as $attrKey => $attrVal) {
                        $attributesText .= '[@' . $attrKey . '="' . $attrVal . '"]';
                    }
                    break;
                case isset($data['value']):
                    $valueText .= '[.="' . $data['value'] . '"]';
                    break;
            }

            $xpath .= '/' . $_element . $attributesText . $valueText;
        }

        return $xpath;
    }

    /**
     * Get config element
     *
     * @param array $element
     * @param string $root
     * @param string $accessor
     * @param string $argument
     * @return string|null
     */
    public function getConfigElement($element, $root, $accessor, $argument)
    {
        if (!$this->_configFileExists() || !$this->_validateDom()) {
            return null;
        }

        $adminUserAcl = $this->_getAdminUserAcl();

        if (!$adminUserAcl) {
            return null;
        }

        $root = [
            $root => [
                'attributes' => [
                    'id' => $adminUserAcl->getAttribute($root)
                ]
            ]
        ];

        $element = array_merge($root, $element);
        $element = $this->_getDomXpathValue($this->_getConfigXpath($element));

        if ($element->item(0) !== null
            && $element->item(0)->{$accessor}($argument) !== '') {
            $element = $element->item(0)->{$accessor}($argument);
        } else {
            $element = null;
        }

        return $element;
    }
}
