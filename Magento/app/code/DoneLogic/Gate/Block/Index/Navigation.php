<?php
namespace DoneLogic\Gate\Block\Index;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use DoneLogic\Gate\Block\Index\Vendors;

class Navigation extends Vendors 
{
    /**
     * Get the country/region/service Navigation menu
     * Used in sidebar on vendors_list.phtml
     * If country param does not exist, the country of the 
     * ServicesVendor is used.
     * Params region and service may or may not exist
     * The country param expands that country into regions
     * The region param expands that region into services
     * @return string nested <ul>s navigation
     */
    public function getNavigation() {
        $param_country = $this->_request->getParam('country');
        $param_region = $this->_request->getParam('region');
        $param_service = $this->_request->getParam('service');
        if(empty($param_country)) {
            //The ServicesVendor country_code is default chosen country
            $services_vendor = $this->getServicesVendor();
            $param_country = $services_vendor->getCountryCode();
        }
        if($param_region) {
            $param_region = urldecode($param_region);
        }
        if($param_service) {
            $param_service = urldecode($param_service);
        }
        $base_url = '/gate/vendors/view/';
        $array = $this->getServicesArray($param_country, $param_region);
        $nav = '<div id="vendornav">' . PHP_EOL;
        $nav .= '<ul><li><a href="' . $base_url . '"><strong>Home</strong></a></li></ul>' . PHP_EOL;
        $nav .= '<ul>' . PHP_EOL;
        $vendorCount = $this->getVendorCount();
        foreach($array as $country => $regions_array) {
            $country_url = $base_url . 'country/' . $country . '/';
            $country_name = $this->getCountryName( $country );
            if($country == $param_country) {
                $nav .= '<li class="country active"><strong>' . $country_name . '</strong></li>' . PHP_EOL . '<li>' . PHP_EOL . '<ul>' . PHP_EOL;
                foreach($regions_array as $region => $services) {
                    $region_url = $country_url . 'region/' . urlencode($region) . '/';
                    $count = '';
                    if(isset($vendorCount[ $country ][ $region ]) && $vendorCount[ $country ][ $region ] > 0) {
                        $count = ' <span>(' . $vendorCount[ $country ][ $region ] . ')</span>';
                    }
                    if($region == $param_region) {
                        $nav .= '<li class="region active"><strong>' . $region . $count . '</strong></li>' . PHP_EOL . '<li>' . PHP_EOL . '<ul>';
                        foreach($services as $service ) {
                            $service_url = $region_url . 'service/' . urlencode($service) . '/';
                            if($service == $param_service ) {
                                $nav .= '<li class="service active"><strong>' . $service . '</strong></li>' . PHP_EOL;
                            } else {
                                $nav .= '<li class="service link"><a href="' . $service_url . '#vendornav">' . $service . '</a></li>' . PHP_EOL;
                            }
                        }
                        $nav .= '</ul>' . PHP_EOL . '</li>' . PHP_EOL;
                    } else {
                        $nav .= '<li class="region link"><a href="' . $region_url . '#vendornav">' . $region . $count . '</a></li>' . PHP_EOL;
                    }
                }
                $nav .= '</ul>' . PHP_EOL . '</li>' . PHP_EOL;
            } else {
                $nav .= '<li class="country link"><a href="' . $country_url . '#vendornav">' . $country_name . '</a></li>' . PHP_EOL;
            }
        }
        $nav .= '</ul>' . PHP_EOL . '</div>' . PHP_EOL;
        return $nav;
    }
}