<?php
namespace DoneLogic\Gate\Model\ResourceModel;
/**
 * DoneLogic/Gate/Model/ResourceModel/Vendors.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Vendors extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('donelogic_gate', 'vendor_id');
    }
}