<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Vendors;
  /**
 * @author John Wenzel johngwenzel@gmail.com
 * Index Action
 */
use DoneLogic\Gate\Controller\Adminhtml\Vendors;
 
class Index extends Vendors
{
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
 
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('DoneLogic_Gate::gate_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Gate Vendors'));
 
        return $resultPage;
    }
}