<?php
namespace DoneLogic\Gate\Block\Adminhtml\Vendors\Edit;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
 
class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('vendor_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Vendor Info'));
    }
 
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'vendor_gate',
            [
                'label' => __('Vendors'),
                'title' => __('Vendors'),
                'content' => $this->getLayout()->createBlock(
                    'DoneLogic\Gate\Block\Adminhtml\Vendors\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
 
        return parent::_beforeToHtml();
    }
}