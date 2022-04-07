<?php
namespace DoneLogic\Core\Controller\Adminhtml\Records;
  /**
 * DoneLogic/Core/Controller/Adminhtml/Records/Save.php
 * @author John Wenzel johngwenzel@gmail.com
 * Save Action
 */
use DoneLogic\Core\Controller\Adminhtml\Records;
 
class Save extends Records
{
    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();
 
        if ($isPost) {
            $recordsModel = $this->_recordsFactory->create();
            $recordsId = $this->getRequest()->getParam('record_id');
 
            if ($recordsId) {
                $recordsModel->load($recordsId);
            }
            $formData = $this->getRequest()->getParam('record');
            $recordsModel->setData($formData);
 
            try {
                // Save news
                $recordsModel->save();
 
                // Display success message
                $this->_messageManager->addSuccess(__('The record has been saved.'));
 
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['record_id' => $recordsModel->getId(), '_current' => true]);
                    return;
                }
 
                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->_messageManager->addError($e->getMessage());
            }
 
            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['record_id' => $recordsId]);
        }
    }
}