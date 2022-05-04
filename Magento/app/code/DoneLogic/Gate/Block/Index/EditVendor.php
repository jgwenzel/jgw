<?php
namespace DoneLogic\Gate\Block\Index;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */

use DoneLogic\Gate\Block\Index\Index;

class EditVendor extends Index
{
    /**
     * getFormAction()
     * Used as action attribute in form
     */
    public function getFormAction() {
        return '/gate/vendors/save';
    }

    /** 
     * getFormData()
     * If existing, retrieves customer's vendor listing, otherwise
     * returns default data.
     * @var Uses Customer Id of logged in Customer to get vendor listing.
     * Technically, customer can have only one listing.
     * Some fields like 'content' are not shown for submission as
     * it is too complicated for vendors to enter html.
     * @return array $data (donelogic_gate table data or default)
     */
    public function getFormData() {
        $data = [];
        $cid = $this->getCustomerId();

        if($cid > 0) {
            $vendors = $this->_vendorsFactory->create();
            $collection = $vendors->getCollection();
            $collection->addFieldToFilter('customer_id',$cid);
            $data = $collection->getFirstItem();
        }

        if(empty($data)) {
            $data = [
                'customer_id' => $cid,
                'services' => [],
                'service_regions' => [],
                'company' => '',
                'address_line_1' => '',
                'address_line_2' => '',
                'city' => '',
                'country_code' => 'US',
                'region_name' => '',
                'postcode' => '',
                'phone' => '',
                'show_phone' => 0,
                'email' => '',
                'show_email' => 0,
                'website' => '',
                'show_website' => 0,
                'description' => '',
                'image_url' => ''
            ];
        }

        if(isset($data['services']) && !is_array($data['services'])) {
            $arr = explode(',',$data['services']);
            $data['services'] = $arr;
        }
        if(isset($data['service_regions']) && !is_array($data['service_regions'])) {
            $arr = explode(',',$data['service_regions']);
            $data['service_regions'] = $arr;
        }
        return $data;
    }
    /**
     * getCountryOptions
     * @param string $selected_country: optional
     * @return string option array as a string for <select>
     */
    public function getCountryOptions( $selected_country='' ) {
        $country_options = $this->_country->toOptionArray();
        $opts = '<option value="">--Select Country--</option>';
        foreach($country_options as $country) {
            if($selected_country == $country['value']) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            $opts .= '<option value="' . $country['value'] . '"' . $selected . '>' . $country['label'] . '</option>';
        }
        return $opts;
    }
    /**
     * getServicesOptions()
     * gets an options array as a string for a <select multiple>
     * @param array $selected_services: optional
     * $selected_services may be passed to mark those options as selected
     * @return string: the options array as a string
     */
    public function getServicesOptions( $selected_services=[] ) {

        if(!is_array($selected_services)) {
            $selected_services = [];
        }
        $arr = $this->getAllServices();

        $multi_opts = "<option value=''>--Select Services--</option>";

        foreach($arr as $name) {
            //for multi select
            if(in_array( $name, $selected_services )) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            $multi_opts .= "<option value='".$name."'".$selected.">" .$name. "</option>";
        }
        return $multi_opts;
    }
    /**
     * getAllServices()
     * Get all the services from the entry which holds the services.
     * We refer to this entry as the SERVICES VENDOR.
     * This entry must exist.
     * The entry must have company == '_SERVICES_'
     * The entry must have category == 'SETTINGS'
     * The entry must have services; a comma delimited list of services.
     * These services are editable in a text area for the entry
     * All other vendors utilize this list in a multiselect.
     * @return array $services
     * [see Index/getServicesVendor() for more information]
     */
    public function getAllServices() {
        $vendors = $this->_vendorsFactory->create();
        $collection = $vendors->getCollection();
        $collection->addFieldToFilter('company','_SERVICES_');
        $collection->addFieldToFilter('category','SETTINGS');
        $data = $collection->getFirstItem();
        $str = $data->getServices();
        $services_array = explode(",",$str);
        return $services_array;
    }
}