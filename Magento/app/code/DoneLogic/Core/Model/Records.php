<?php
namespace DoneLogic\Core\Model;
/**
 * DoneLogic/Core/Model/Records.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\Model\AbstractModel;
 
class Records extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('DoneLogic\Core\Model\ResourceModel\Records');
    }
}