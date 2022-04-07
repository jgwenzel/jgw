<?php
namespace DoneLogic\Core\Block\Adminhtml\Records;
/**
 * DoneLogic/Core/Block/Adminhtml/Records/Edit.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
 
class Edit extends Container
{
    /**
     * Core registry
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
        $this->_objectId = 'record_id';
        $this->_controller = 'adminhtml_records';
        $this->_blockGroup = 'DoneLogic_Core';
 
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
    //JGW: get rid of this function
    /*
    public function getHeaderText()
    {
        return ' ';
        $records = $this->_coreRegistry->registry('donelogic_core');
        if ($records->getId()) {
            $recordsTitle = $this->escapeHtml($records->getTitle());
            return __("Edit News '%1'", $recordsTitle);
        } else {
            return __('Add News');
        }
    }
    */
 
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