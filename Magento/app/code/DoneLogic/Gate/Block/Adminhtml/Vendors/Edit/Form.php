<?php
namespace DoneLogic\Gate\Block\Adminhtml\Vendors\Edit;
 
use Magento\Backend\Block\Widget\Form\Generic;
 /**
 * @author John Wenzel johngwenzel@gmail.com
 */
class Form extends Generic
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'    => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
}