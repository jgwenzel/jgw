<?php
namespace DoneLogic\Gate\Controller\Index;
 /**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Framework\App\Action\Action;

class Index extends Action
{
    /**
     * forward: index page not used
     */
    public function execute() {
        $this->_forward('gate/vendors/view');
    }
}