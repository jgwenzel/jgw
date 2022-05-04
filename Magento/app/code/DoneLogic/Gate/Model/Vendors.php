<?php
namespace DoneLogic\Gate\Model;
/**
 * DoneLogic/Gate/Model/Vendors.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\Model\AbstractModel;
 
class Vendors extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('DoneLogic\Gate\Model\ResourceModel\Vendors');
    }
}