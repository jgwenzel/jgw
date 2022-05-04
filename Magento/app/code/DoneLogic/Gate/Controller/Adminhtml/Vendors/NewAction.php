<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Vendors;
  /**
 * DoneLogic/Gate/Controller/Adminhtml/Vendors/NewAction.php
 * @author John Wenzel johngwenzel@gmail.com
 * For dissambiguation, this class is NewAction() rather than New().
 * Forwards to Edit() where new vendor is Saved by the fact that
 * no vendor_id is present.
 */
use DoneLogic\Gate\Controller\Adminhtml\Vendors;
 
class NewAction extends Vendors
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