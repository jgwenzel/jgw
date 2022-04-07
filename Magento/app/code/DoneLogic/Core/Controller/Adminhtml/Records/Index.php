<?php
namespace DoneLogic\Core\Controller\Adminhtml\Records;
  /**
 * DoneLogic/Core/Controller/Adminhtml/Records/Index.php
 * @author John Wenzel johngwenzel@gmail.com
 * Index Action
 */
use DoneLogic\Core\Controller\Adminhtml\Records;
 
class Index extends Records
{
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
 
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('DoneLogic_Core::core_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Core Data'));
 
        return $resultPage;
    }
}