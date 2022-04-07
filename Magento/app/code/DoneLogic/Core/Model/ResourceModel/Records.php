<?php
namespace DoneLogic\Core\Model\ResourceModel;
/**
 * DoneLogic/Core/Model/ResourceModel/Records.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Records extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('donelogic_core', 'record_id');
    }
}