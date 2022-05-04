<?php
namespace DoneLogic\Gate\Controller\Vendors;
 /**
 * @author John Wenzel johngwenzel@gmail.com
 */

use DoneLogic\Gate\Controller\Vendors\Vendors;

class View extends Vendors
{
    /**
     * Sets Theme and returns page
     * @return resultPageFactory->create()
     */
    public function execute() {
        $this->_designInterface->setDesignTheme('DoneLogic/BritePipe', 'frontend');
        return $this->_resultPageFactory->create();
    }
}