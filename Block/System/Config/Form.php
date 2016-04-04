<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedAcl\Block\System\Config;

/**
 * System config form block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Form extends \Magento\Config\Block\System\Config\Form
{
    /**
     * @var \ShopGo\AdvancedAcl\Model\System\Config
     */
    protected $_advAclModelSystemConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Config\Block\System\Config\Form\Fieldset\Factory $fieldsetFactory
     * @param \Magento\Config\Block\System\Config\Form\Field\Factory $fieldFactory
     * @param \ShopGo\AdvancedAcl\Model\System\Config $advAclModelSystemConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Config\Block\System\Config\Form\Fieldset\Factory $fieldsetFactory,
        \Magento\Config\Block\System\Config\Form\Field\Factory $fieldFactory,
        \ShopGo\AdvancedAcl\Model\System\Config $advAclModelSystemConfig,
        array $data = []
    ) {
        $this->_advAclModelSystemConfig = $advAclModelSystemConfig;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $configFactory,
            $configStructure,
            $fieldsetFactory,
            $fieldFactory,
            $data
        );
    }

    /**
     * Initialize form
     *
     * @return $this
     */
    public function initForm()
    {
        $this->_initObjects();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($this->getSectionCode());
        if ($section && $section->isVisible($this->getWebsiteCode(), $this->getStoreCode())) {
            foreach ($section->getChildren() as $group) {
                $elements = [
                    'section' => ['attributes' => ['id' => $section->getId()]],
                    'group'   => ['attributes' => ['id' => $group->getId()]]
                ];

                if (isset($section->getData()['tab'])) {
                    $elements = array_merge(
                        ['tab' => ['attributes' => ['id' => $section->getData()['tab']]]],
                        $elements
                    );
                }

                $access = $this->_advAclModelSystemConfig->getSystemConfigAccess($elements);

                if ($access) {
                    $this->_initGroup($group, $section, $form);
                }
            }
        }

        $this->setForm($form);
        return $this;
    }

    /**
     * Initialize config group fields
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param \Magento\Config\Model\Config\Structure\Element\Group $group
     * @param \Magento\Config\Model\Config\Structure\Element\Section $section
     * @param string $fieldPrefix
     * @param string $labelPrefix
     * @return $this
     */
    public function initFields(
        \Magento\Framework\Data\Form\Element\Fieldset $fieldset,
        \Magento\Config\Model\Config\Structure\Element\Group $group,
        \Magento\Config\Model\Config\Structure\Element\Section $section,
        $fieldPrefix = '',
        $labelPrefix = ''
    ) {
        if (!$this->_configDataObject) {
            $this->_initObjects();
        }

        // Extends for config data
        $extraConfigGroups = [];

        /** @var $element \Magento\Config\Model\Config\Structure\Element\Field */
        foreach ($group->getChildren() as $element) {
            $elements = [
                'section' => ['attributes' => ['id' => $section->getId()]],
                'group'   => ['attributes' => ['id' => $group->getId()]],
                'field'   => ['attributes' => ['id' => $element->getId()]]
            ];

            if (isset($section->getData()['tab'])) {
                $elements = array_merge(
                    ['tab' => ['attributes' => ['id' => $section->getData()['tab']]]],
                    $elements
                );
            }

            $access = $this->_advAclModelSystemConfig->getSystemConfigAccess($elements);

            if (!$access) {
                continue;
            }

            if ($element instanceof \Magento\Config\Model\Config\Structure\Element\Group) {
                $this->_initGroup($element, $section, $fieldset);
            } else {
                $path = $element->getConfigPath() ?: $element->getPath($fieldPrefix);
                if ($element->getSectionId() != $section->getId()) {
                    $groupPath = $element->getGroupPath();
                    if (!isset($extraConfigGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject->extendConfig(
                            $groupPath,
                            false,
                            $this->_configData
                        );
                        $extraConfigGroups[$groupPath] = true;
                    }
                }
                $this->_initElement($element, $fieldset, $path, $fieldPrefix, $labelPrefix);
            }
        }
        return $this;
    }
}
