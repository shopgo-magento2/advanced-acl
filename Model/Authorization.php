<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Model;

class Authorization implements \Magento\Framework\AuthorizationInterface
{
    /**
     * ACL policy
     *
     * @var \Magento\Framework\Authorization\PolicyInterface
     */
    protected $_aclPolicy;

    /**
     * ACL role locator
     *
     * @var \Magento\Framework\Authorization\RoleLocatorInterface
     */
    protected $_aclRoleLocator;

    /**
     * Advanced ACL authorization resource config model
     *
     * @var \ShopGo\AdvancedAcl\Model\Authorization\Resource\Config
     */
    protected $_advAclModelAuthResourceConfig;

    /**
     * @param \Magento\Framework\Authorization\PolicyInterface $aclPolicy
     * @param \Magento\Framework\Authorization\RoleLocatorInterface $roleLocator
     * @param \ShopGo\AdvancedAcl\Model\Authorization\Resource\Config $advAclModelAuthResourceConfig
     */
    public function __construct(
        \Magento\Framework\Authorization\PolicyInterface $aclPolicy,
        \Magento\Framework\Authorization\RoleLocatorInterface $roleLocator,
        \ShopGo\AdvancedAcl\Model\Authorization\Resource\Config $advAclModelAuthResourceConfig
    ) {
        $this->_aclPolicy = $aclPolicy;
        $this->_aclRoleLocator = $roleLocator;
        $this->_advAclModelAuthResourceConfig = $advAclModelAuthResourceConfig;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        $advAclAccess = $this->_advAclModelAuthResourceConfig->getAuthResourceAccess([
            'resource' => ['attributes' => ['id' => $resource]]
        ]);

        $access = $this->_aclPolicy->isAllowed(
            $this->_aclRoleLocator->getAclRoleId(),
            $resource, $privilege
        );

        return $access && $advAclAccess;
    }
}
