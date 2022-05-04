<?php
namespace DoneLogic\Gate\Controller\Adminhtml\Vendors;
 /**
 * DoneLogic/Gate/Controller/Adminhtml/Vendors/Grid.php
 * @author John Wenzel johngwenzel@gmail.com
 * Grid Action
 */
use DoneLogic\Gate\Controller\Adminhtml\Vendors;
 
class Grid extends Vendors
{
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}