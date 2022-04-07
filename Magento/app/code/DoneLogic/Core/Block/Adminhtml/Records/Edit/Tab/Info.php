<?php
namespace DoneLogic\Core\Block\Adminhtml\Records\Edit\Tab;
/**
 * DoneLogic/Core/Block/Adminhtml/Records/Edit/Tab/Info.php
 * @author John Wenzel johngwenzel@gmail.com
 * The main functionality in this class is _prepareForm() which
 * is used to edit/add Records in donelogic_core database table. 
 */
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
 
class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \DoneLogic\Core\Model\RecordsFactory */
        $model = $this->_coreRegistry->registry('donelogic_core');
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('record_');
        $form->setFieldNameSuffix('record');
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Core Data')]
        );
 
        if ($model->getId()) {
            $fieldset->addField(
                'record_id',
                'hidden',
                ['name' => 'record_id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Name'),
                'title'     => __('Name'),
                'required'  => true
            ]
        );
        $fieldset->addField(
            'value',
            'textarea',
            array(
                'name'      => 'value',
                'label'     => __('Value'),
                'title'     => __('Value'),
                'required'  => true
            )
        );
        $fieldset->addField(
            'description',
            'textarea',
            array(
                'name'      => 'description',
                'label'     => __('Description'),
                'title'     => __('Description'),
                'required'  => false
            )
        );
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Records Core');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Records Core');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}