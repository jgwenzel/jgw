<?php
namespace DoneLogic\Core\Block\Adminhtml;
/**
 * DoneLogic/Core/Block/Adminhtml/Records.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\Block\Widget\Grid\Container;
 
class Records extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_records';
        $this->_blockGroup = 'DoneLogic_Core';
        $this->_headerText = __('DoneLogic Core Data');
        $this->_addButtonLabel = __('Add New Record');
        parent::_construct();
    }
}