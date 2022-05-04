<?php
namespace DoneLogic\Gate\Block\Adminhtml\Vendors\Edit\Tab;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use DoneLogic\Gate\Model\VendorsFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Directory\Model\Config\Source\Country;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    protected $_vendorsFactory;
    protected $_country;
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FormFactory $formFactory
     * @param VendorsFactory $vendorsFactory
     * @param Config $wysiwygConfig
     * @param Country $country
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FormFactory $formFactory,
        VendorsFactory $vendorsFactory,
        Config $wysiwygConfig,
        Country $country,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_formFactory = $formFactory;
        $this->_vendorsFactory = $vendorsFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_country = $country;

        parent::__construct($context, $coreRegistry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \DoneLogic\Gate\Model\VendorsFactory */
        $model = $this->_coreRegistry->registry('donelogic_gate');
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');
        $form->setFieldNameSuffix('vendor');

        $country_options=$this->_country->toOptionArray();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Gate Vendors')]
        );
        $vendor_id = $model->getId();
        if($vendor_id) {
            $fieldset->addField(
                'vendor_id',
                'hidden',
                ['name' => 'vendor_id']
            );

            //for AJAX
            $region_name = $model->getData('region_name');

            //for AJAX
            $service_regions = $model->getData('service_regions');
            if(is_array($service_regions)) {
                $service_regions = implode(",",$service_regions);
            }
            $service_regions = urlencode($service_regions);

        }
        else {
            $region_name = NULL;
            $service_regions = NULL;
        }
        $fieldset->addField(
            'company',
            'text',
            [
                'name'      => 'company',
                'label'     => __('Company'),
                'title'     => __('Company'),
                'required'  => true
            ]
        );
        $fieldset->addField(
            'customer_id',
            'text',
            [
                'name'      => 'customer_id',
                'label'     => __('Customer ID'),
                'title'     => __('Customer ID'),
                'required'  => true
            ]
        );
        $fieldset->addField(
            'category',
            'text',
            array(
                'name'      => 'category',
                'label'     => __('Category'),
                'title'     => __('Category'),
                'required'  => true
            )
        );
        if($this->isServicesVendor( $vendor_id )) {
            $fieldset->addField(
                'services',
                'textarea',
                array(
                    'name'      => 'services',
                    'label'     => __('All Services List'),
                    'title'     => __('All Services List'),
                    'required'  => true,
                    'note' => 'The list of services here defines all services available to choose from. Use commas with no trailing spaces.
                    Ex. cat one,cat two,cat three'
                )
            );
        }
        else {
            $fieldset->addField(
                'services',
                'multiselect',
                [
                    'name' => 'services[]',
                    'label' => __('Services'),
                    'title' => __('Services'),
                    'values' => $this->getServices(),
                    'required' => false
                ]
            );
        }
        $fieldset->addField(
            'address_line_1',
            'text',
            array(
                'name'      => 'address_line_1',
                'label'     => __('Address Line 1'),
                'title'     => __('Address Line 1'),
                'required'  => false
            )
        );
        $fieldset->addField(
            'address_line_2',
            'text',
            array(
                'name'      => 'address_line_2',
                'label'     => __('Address Line 2'),
                'title'     => __('Address Line 2'),
                'required'  => false
            )
        );
        $fieldset->addField(
            'city',
            'text',
            array(
                'name'      => 'city',
                'label'     => __('City'),
                'title'     => __('City'),
                'required'  => true
            )
        );
        $country = $fieldset->addField(
            'country_code',
            'select',
            array(
                'name'      => 'country_code',
                'id'        => 'country_code',
                'label'     => __('Country'),
                'title'     => __('Country'),
                'values'     => $country_options,
                'required'  => true
            )
        );
        $fieldset->addField(
            'region_name',
            'select',
            [
                'name' => 'region_name',
                'label' => __('Region'),
                'id' => 'region_name',
                'title' => __('Region'),
                'values' => ['--Please Select Country First--'], 
                'required' => true
            ]
        );
        $fieldset->addField(
            'postcode',
            'text',
            array(
                'name'      => 'postcode',
                'label'     => __('Zip/Postal Code'),
                'title'     => __('Zip/Postal Code'),
                'required'  => true
            )
        );
        $fieldset->addField(
            'service_regions',
            'multiselect',
            [
                'name' => 'service_regions',
                'label' => __('Service Regions'),
                'id' => 'service_regions',
                'title' => __('Service Regions'),
                'options' => ["<option value=''>--Please Select Country First--<option>"], 
                'required' => false
            ]
        );
        $fieldset->addField(
            'phone',
            'text',
            array(
                'name'      => 'phone',
                'label'     => __('Phone'),
                'title'     => __('Phone'),
                'required'  => false
            )
        );
        $fieldset->addField(
            'show_phone',
            'select',
            [
                'name' => 'show_phone',
                'label' => __('Phone Visibility'),
                'id' => 'show_phone',
                'title' => __('Phone Visibility'),
                'values' => [0 => __('Hide'), 1 => __('Show')], 
                'required' => false
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            array(
                'name'      => 'email',
                'label'     => __('Email'),
                'title'     => __('Email'),
                'required'  => true
            )
        );
        $fieldset->addField(
            'show_email',
            'select',
            [
                'name' => 'show_email',
                'label' => __('Email Visibility'),
                'id' => 'show_email',
                'title' => __('Email Visibility'),
                'values' => [0 => __('Hide'), 1 => __('Show')], 
                'required' => true
            ]
        );
        $fieldset->addField(
            'website',
            'text',
            array(
                'name'      => 'website',
                'label'     => __('Website'),
                'title'     => __('Website'),
                'required'  => false
            )
        );
        $fieldset->addField(
            'show_website',
            'select',
            [
                'name' => 'show_website',
                'label' => __('Website Visibility'),
                'id' => 'show_website',
                'title' => __('Website Visibility'),
                'values' => [0 => __('Hide'), 1 => __('Show')], 
                'required' => false
            ]
        );
        $fieldset->addField(
            'description',
            'textarea',
            array(
                'name'      => 'description',
                'label'     => __('Description'),
                'title'     => __('Description'),
                'required'  => false
            )
        );
        $fieldset->addField(
            'content',
            'textarea',
            array(
                'name'      => 'content',
                'label'     => __('Page Content'),
                'title'     => __('Page Content'),
                'required'  => false
            )
        );
        $fieldset->addField(
            'image_url',
            'image', 
            [
                'name' => 'image_url',
                'label' => __('Upload Image'),
                'title' => __('Upload Image'),
                'required' => true,
                'note' => 'Allow image type: jpg, jpeg, png',
                'class' => 'required-entry required-file',
            ]
        );
        $fieldset->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label' => __('Active?'),
                'id' => 'active',
                'title' => __('Active?'),
                'values' => [0 => __('No'), 1 => __('Yes')], 
                'required' => true
            ]
        );


    /* for add java script for country and region select */
    $country->setAfterElementHtml("   
    
    <script type=\"text/javascript\">
            require(['jquery', 'jquery/ui'],function($) {

                    // on intial check whether country code exists or not 
                        
                   $(window).on('load', function() {

                    var country_code = $('#vendor_country_code').val();
                    //var region_name = $('#vendor_region_name').val();

                    var region_name = '".$region_name."';
                    var service_regions = '".$service_regions."';

                        //alert('region_name '+region_name+' country_code '+country_code);

                        $.ajax({
                               url : '". $this->getUrl('gate/ajax/regionselect') . "country_code/' + $('#vendor_country_code').val()+'/region_name/'+region_name+'/service_regions/'+service_regions,
                               type: 'get',
                               dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                    $('#vendor_region_name').empty();
                                    $('#vendor_region_name').append(data.htmldata.select);
                                    $('#vendor_service_regions').empty();
                                    $('#vendor_service_regions').append(data.htmldata.multiselect);
                                }
                            });
                    });   

                    // onchange country this function called 

                   $(document).on('change', '#vendor_country_code', function(event){

                    var country_code = $('#vendor_country_code').val();

                    //alert(country_code);

                    $.ajax({
                            url : '". $this->getUrl('gate/ajax/regionselect') . "country_code/' + $('#vendor_country_code').val(),
                            type: 'get',
                            dataType: 'json',
                            showLoader:true,
                            success: function(data){
                                $('#vendor_region_name').empty();
                                $('#vendor_region_name').append(data.htmldata.select);
                                $('#vendor_service_regions').empty();
                                $('#vendor_service_regions').append(data.htmldata.multiselect);
                            }
                        });
                         
                   })
                }

            );
            </script>"
        );


        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Vendor Data');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Vendor Data');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    public function getServices() {

        $arr = $this->getAllServices();
        $services = [];
        foreach($arr as $name) {
            $services[] = ['value' => $name, 'label' => $name];
        }
        return $services;
    }

    public function getAllServices( $format='array') {
        $services_vendor = $this->getServicesVendor();
        $services = $services_vendor->getServices();
        if($format == 'array') {
            $services = explode(",",$services);
        }
        return $services;
    }

    public function getServicesVendorId() {
        $services_vendor = $this->getServicesVendor();
        $vendor_id = $services_vendor->getVendorId();
        return $vendor_id;
    }

    public function isServicesVendor( $vendor_id ) {
        $services_vendor_id = $this->getServicesVendorId();
        return ($vendor_id == $services_vendor_id);
    }

    public function getServicesVendor() {
        $vendors = $this->_vendorsFactory->create();
        $collection = $vendors->getCollection();
        $collection->addFieldToFilter('company','_SERVICES_');
        $collection->addFieldToFilter('category','SETTINGS');
        $services_vendor = $collection->getFirstItem();
        return $services_vendor;
    }
}

