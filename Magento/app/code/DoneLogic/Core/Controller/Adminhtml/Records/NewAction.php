<?php
namespace DoneLogic\Core\Controller\Adminhtml\Records;
  /**
 * DoneLogic/Core/Controller/Adminhtml/Records/NewAction.php
 * @author John Wenzel johngwenzel@gmail.com
 * For dissambiguation, this class is NewAction() rather than New().
 * Forwards to Edit() where new record is Saved by the fact that
 * no record_id is present.
 */
use DoneLogic\Core\Controller\Adminhtml\Records;
 
class NewAction extends Records
{
    /**
     * Create new action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}