<?php
namespace DoneLogic\Core\Controller\Adminhtml\Records;
/**
 * DoneLogic/Core/Controller/Adminhtml/Records/Delete.php
 * @author John Wenzel johngwenzel@gmail.com
 * Delete Action
 */
use DoneLogic\Core\Controller\Adminhtml\Records;
 
class Delete extends Records
{
    public function execute()
    {
        $recordId = (int) $this->getRequest()->getParam('record_id');
 
        if ($recordId) {
            /** @var $recordModel \DoneLogic\Core\Model\Records */
            $recordModel = $this->_recordsFactory->create();
            $recordModel->load($recordId);
 
            // Check this news exists or not
            if (!$recordModel->getId()) {
                $this->_messageManager->addError(__('This record no longer exists.'));
            } else {
                try {
                    // Delete news
                    $recordModel->delete();
                    $this->_messageManager->addSuccess(__('The record has been deleted.'));
 
                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->_messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['record_id' => $recordModel->getId()]);
                }
            }
        }
    }
}