<?php
namespace DoneLogic\Core\Model\ResourceModel\Records;
/**
 * DoneLogic/Core/Model/ResourceModel/Records/Collection.php
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'DoneLogic\Core\Model\Records',
            'DoneLogic\Core\Model\ResourceModel\Records'
        );
    }
}