<?php
namespace DoneLogic\Core\Controller\Adminhtml\Records;
 /**
 * DoneLogic/Core/Controller/Adminhtml/Records/Grid.php
 * @author John Wenzel johngwenzel@gmail.com
 * Grid Action
 */
use DoneLogic\Core\Controller\Adminhtml\Records;
 
class Grid extends Records
{
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}