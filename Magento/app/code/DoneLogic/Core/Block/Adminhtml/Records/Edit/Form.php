<?php
namespace DoneLogic\Core\Block\Adminhtml\Records\Edit;
 
use Magento\Backend\Block\Widget\Form\Generic;
 /**
 * DoneLogic/Core/Block/Adminhtml/Records/Edit/Form.php
 * @author John Wenzel johngwenzel@gmail.com
 * The main functionality in this class is _prepareForm() which
 * creates the form and configures the form attributes.
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