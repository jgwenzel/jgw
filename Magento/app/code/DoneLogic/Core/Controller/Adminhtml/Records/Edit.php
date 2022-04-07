<?php
namespace DoneLogic\Core\Controller\Adminhtml\Records;
 
use DoneLogic\Core\Controller\Adminhtml\Records;
 /**
 * DoneLogic/Core/Controller/Adminhtml/Records/Edit.php
 * @author John Wenzel johngwenzel@gmail.com
 * Edit Action
 */
class Edit extends Records
{
    /**
     * @return void
     */
    public function execute()
    {
        $recordId = $this->getRequest()->getParam('record_id');
 
        $model = $this->_recordsFactory->create();
 
        if ($recordId) {
            $model->load($recordId);
            if (!$model->getId()) {
                $this->_messageManager->addError(__('This record no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_coreRegistry->register('donelogic_core', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('DoneLogic_Core::core_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Records'));
 
        return $resultPage;
    }
}