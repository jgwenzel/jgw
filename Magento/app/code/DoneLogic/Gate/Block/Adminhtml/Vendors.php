<?php
namespace DoneLogic\Gate\Block\Adminhtml;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\Block\Widget\Grid\Container;

class Vendors extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_vendors';
        $this->_blockGroup = 'DoneLogic_Gate';
        $this->_headerText = __('Gate Vendors');
        $this->_addButtonLabel = __('Add New Vendor');
        parent::_construct();
    }
}