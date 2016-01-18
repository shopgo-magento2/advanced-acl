<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model\System;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * System configuration model
 */
class Config extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * ACL file path
     */
    const ACL_FILE_PATH = 'shopgo/advanced_acl/system_config.xml';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_varDirectory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var boolean
     */
    protected $_isAclFile;

    /**
     * @var boolean
     */
    protected $_isAclValid;

    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @var \DomDocument
     */
    protected $_aclDom;

    /**
     * @var \DOMXPath
     */
    protected $_aclDomXpath;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\Model\Config\Structure\Converter $converter
     * @param \ShopGo\AdvancedAcl\Model\System\Config\SchemaLocator $schemaLocator
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
        Config\SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'system_config.xml',
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    ) {
        $this->_filesystem  = $filesystem;
        $this->_authSession = $authSession;

        $this->validationState   = $validationState;
        $this->_domDocumentClass = $domDocumentClass;

        $this->_setVarDirectory();

        if ($this->_isAclFile = $this->_aclFileExists()) {
            $this->_setAclDom();
            $this->_setAclDomXpath();
        }

        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_isAclValid = $this->_validateAclDom();

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
     * Set Var directory
     */
    protected function _setVarDirectory()
    {
        $this->_varDirectory = $this->_filesystem
            ->getDirectoryRead(DirectoryList::VAR_DIR);
    }

    /**
     * Check whether ACL file exists
     *
     * @return boolean
     */
    protected function _aclFileExists()
    {
        return $this->_varDirectory->isFile(
            self::ACL_FILE_PATH
        );
    }

    /**
     * Get ACL file absolute path
     *
     * @return string
     */
    protected function _getAclFileAbsolutePath()
    {
        return $this->_varDirectory->getAbsolutePath(
            self::ACL_FILE_PATH
        );
    }

    /**
     * Get ACL file absolute path
     *
     * @return string
     */
    protected function _getAclFileXmlContent()
    {
        return $this->_varDirectory->readFile(
            self::ACL_FILE_PATH
        );
    }

    /**
     * Set ACL DOM
     */
    protected function _setAclDom()
    {
        $this->_aclDom = new $this->_domDocumentClass(
            $this->_getAclFileXmlContent(),
            $this->validationState,
            [],
            null,
            null
        );
    }

    /**
     * Set ACL DOM XPath
     */
    protected function _setAclDomXpath()
    {
        $this->_aclDomXpath = new \DOMXPath($this->_aclDom->getDom());
    }

    /**
     * Get ACL DOM XPath value
     *
     * @param string $xpath
     * @return string
     */
    protected function _getAclDomXpathValue($xpath)
    {
        return $this->_aclDomXpath->query($xpath);
    }

    /**
     * Validate ACL DOM
     *
     * @return boolean
     */
    protected function _validateAclDom()
    {
        $result = true;
        $errors = [];

        if ($this->_aclDom && !$this->_aclDom->validate($this->_schemaFile, $errors)) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get ACL for current admin user
     *
     * @return string|boolean
     */
    protected function _getAdminUserAcl()
    {
        $user = $this->_authSession->getUser()->getUsername();

        $element = $this->_getAclDomXpathValue(
            '//users/user[@name="*"]'
        );

        if ($element->item(0) !== null) {
            $excludedUsers = $this->_getAclDomXpathValue(
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
            $element = $this->_getAclDomXpathValue(
                '//users/user[@name="' . $user . '"]'
            );
        }

        return $element->item(0) !== null
            ? $element->item(0)->getAttribute('system')
            : false;
    }

    /**
     * Get system config XPath
     *
     * @param array $elements
     * @return string
     */
    protected function _getSystemConfigXpath($elements)
    {
        $xpath = '/';

        foreach ($elements as $element => $attributes) {
            $attributesText = '';

            foreach ($attributes as $attrKey => $attrVal) {
                $attributesText .= '[@' . $attrKey . '="' . $attrVal . '"]';
            }

            $xpath .= '/' . $element . $attributesText;
        }

        return $xpath;
    }

    /**
     * Get system config access permission
     *
     * @param array $elements
     * @return int|boolean
     */
    public function getSystemConfigAccess($elements)
    {
        $access = true;

        if (!$this->_isAclFile || !$this->_isAclValid) {
            return $access;
        }

        $adminUserAcl = $this->_getAdminUserAcl();

        if (!$adminUserAcl) {
            return $access;
        }

        $elements = array_merge(
            array('system' => array('id' => $adminUserAcl)),
            $elements
        );

        $element = $this->_getAclDomXpathValue(
            $this->_getSystemConfigXpath($elements)
        );

        return $element->item(0) !== null
            ? !$element->item(0)->getAttribute('disabled')
            : $access;
    }
}
