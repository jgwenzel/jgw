<?php
namespace DoneLogic\Gate\Block\Adminhtml\Vendors;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
 
class Edit extends Container
{
    /**
     * Gate registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
 
    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'vendor_id';
        $this->_controller = 'adminhtml_vendors';
        $this->_blockGroup = 'DoneLogic_Gate';
 
        parent::_construct();
 
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete'));
    }
 
    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    //TODO: Remove this function?
    
    public function getHeaderText()
    {
        return ' ';
        $vendors = $this->_coreRegistry->registry('donelogic_gate');
        if ($vendors->getId()) {
            $company = $this->escapeHtml($vendors->getCompany());
            return __("Edit Company '%1'", $company);
        } else {
            return __('Add Company');
        }
    }
 
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('value') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'value');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'value');
                }
            };
        ";
 
        return parent::_prepareLayout();
    }
}