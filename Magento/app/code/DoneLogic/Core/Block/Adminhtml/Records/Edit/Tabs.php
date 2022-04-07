<?php
namespace DoneLogic\Core\Block\Adminhtml\Records\Edit;
/**
 * DoneLogic/Core/Block/Adminhtml/Records/Edit/Tabs.php
 * @author John Wenzel johngwenzel@gmail.com
 * Adds tabs in the admin menu.
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
        $this->setId('record_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Record Info'));
    }
 
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'record_core',
            [
                'label' => __('Core Data'),
                'title' => __('DoneLogic Core Data'),
                'content' => $this->getLayout()->createBlock(
                    'DoneLogic\Core\Block\Adminhtml\Records\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
 
        return parent::_beforeToHtml();
    }
}