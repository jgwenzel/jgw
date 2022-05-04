<?php
namespace DoneLogic\Gate\Block\Index;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use DoneLogic\Gate\Block\Index\Index;

class Vendors extends Index 
{
    protected $_vendorsPerPage = 10;

    /** @var $_isFiltered is set to true if any country/region/service filtering
     * has been done. This is used on template page to show intro content
     * when no filtering has been done (i.e. homepage only has intro)
     */
    protected $_isFiltered=false;
    /**
     * @var $_filteredVendors holds the filtered vendors collection so it
     * only needs to be built once.
     */
    protected $_filteredVendors=null;

    /**
     * @var $_vendorCount array holds the count of vendor for each country/region
     */
    protected $_vendorCount;
    /**
     * @var $_meta holds the meta tags array so it only needs constructing
     * once
     */
    protected $_meta;

    /**
     * Sets title, meta title, meta desciption and meta keywords
     * @uses getServiceDirectoryMeta() to get meta array
     */
    public function _prepareLayout() {  
       $meta = $this->getServiceDirectoryMeta();
       $this->pageConfig->getTitle()->set(__($meta['title']));
       $this->pageConfig->setDescription(__($meta['description']));
       $this->pageConfig->setKeywords(__($meta['keywords']));
       return parent::_prepareLayout();
    }
    /**
     * Get all vendors as a collection
     * @return collection
     */
    public function getVendors() {
        $vf = $this->_vendorsFactory->create();
        $vendors = $vf->getCollection();
        return $vendors;
    }

    /**
     * Get the meta array by building it from country/region/service params.
     * Array contains regular and og meta tags
     * Note about bad requests: checks to see if params results 
     * produces vendor collection and if not, uses default meta tags.
     * Used by:
     * 1) _prepareLayout() for regular meta tags
     * 2) templates/metatags/vendors_list_meta.phtml for og meta tags
     * @return array meta tags array
     */
    public function getServiceDirectoryMeta()
    {
        if(is_array($this->_meta)) {
            return $this->_meta;
        }
        if($this->getFilteredVendors()->getSize() < 1) {
            //then request is not valid
            $this->_meta = $this->getDefaultMeta();
            return $this->_meta;
        }
        $country_code = $this->_request->getParam('country');
        $region = $this->_request->getParam('region');
        $service = $this->_request->getParam('service');
        if(empty($country_code)) {
            //The ServicesVendor country_code is default chosen country
            $services_vendor = $this->getServicesVendor();
            $country_code = $services_vendor->getCountryCode();
        }
        $country = $this->getCountryName( $country_code );
        $title = '';
        $keywords = [];
        $base_url = $this->getBaseUrl();
        $url = $base_url . 'gate/vendors/view/';
        if(!empty($country)) {
            $url .= 'country/' . $country_code . '/';
            $title = $country;
            $keywords[] = $country;
            if(!empty($region)) {
                $url .= 'region/' . urlencode($region) . '/';
                if($country_code == 'US' || $country_code == 'CA') {
                    //don't include US or Canada in meta tags if region exists
                    $title = trim(urldecode($region));
                    $keywords = [];//reset
                } else {
                    $title .= " " . trim(urldecode($region));
                }
                $keywords[] = trim(urldecode($region));
            }
            if(!empty($service)) {
                $url .= 'service/' . urlencode($service) . '/';
                $title .= " " . trim(urldecode($service)) . " Services";
                $keywords[] = trim(urldecode($service));
                $keywords = array_merge($keywords, explode(" ", trim(urldecode($service))));
            } else {
                $title .= " Services Directory";
                $keywords[] = "Services";
            }
        }
        else {
            $title = 'Services Directory';
            $keywords = ["Bollard Services", "Bollard Installation", "Bollard Cover Installation"];
        }
        $keywords[] = "bollard";
        $keywords[] = "bollards";
        $keywords[] = "contractor";
        $keywords[] = "contractors";
        array_unique($keywords);
        $keywords_str = implode(",",$keywords);

        //use image of first vendor
        $image_url = '';
        $collection = $this->getFilteredVendors();
        $first_vendor = $collection->getFirstItem();
        if($first_vendor) {
            $image_url = $base_url . 'pub/media/' . $first_vendor->getImageUrl();
        }

        $meta = [];
        $meta['title'] = $title;
        $meta['description'] = $title;
        $meta['keywords'] = $keywords_str;
        $meta['og_title'] = $title;
        $meta['og_description'] = $title;
        $meta['og_image'] = $image_url;
        $meta['og_url'] = $url;
        $this->_meta = $meta;
        return $meta;
    }

    /**
     * Gets vendor collection by filters based on request params:
     * country, region and service
     * Orders collection by customer_id meaning newest customer listings
     * are first. Note: this puts customer_id == 0 listings last so
     * category == 'ADS' listing will be, and should be last as long as
     * its customer_id == 0. The category=='ADS' listing(s) are a way
     * to advertise that vendors can post a listing.
     * @var $_filteredVendors is set to hold result so this method constructs
     * the collection only once.
     * @var $_isFiltered is set to true if any country/region/service filtering
     * has been done. This is used on template page to show intro content
     * when no filtering has been done (i.e. homepage only has intro)
     * @return collection vendor collection
     */
    public function getFilteredVendors() {
        if(!empty($this->_filteredVendors)) {
            return $this->_filteredVendors;
        }
        $vendors = $this->getVendors();
        $vendors->addFieldToFilter('active',1);
        $country = $this->_request->getParam('country');
        $region = $this->_request->getParam('region');
        $service = $this->_request->getParam('service');
        $currentPage = $this->_request->getParam('p');
        if(!$currentPage || $currentPage < 2) {
            $currentPage = 1;
        }
        if(!empty($country) && strlen($country) > 1) {
            $vendors->addFieldToFilter('country_code',$country);
            $this->_isFiltered = true;
        }
        if(!empty($region) && strlen($region) > 1) {
            $vendors->addFieldToFilter('service_regions', array('like' => '%' . urldecode($region) . '%'));
            $this->_isFiltered = true;
        }
        if(!empty($service) && strlen($service) > 1) {
            $vendors->addFieldToFilter('services', array( 'like' => '%' . urldecode($service) . '%'));
            $this->_isFiltered = true;
        }
        $vendors->setOrder('customer_id','DESC');
        $this->_filteredVendors = $vendors;
        $vendors->setPageSize( $this->_vendorsPerPage );
        $vendors->setCurPage( $currentPage );
        return $vendors;
    }
    /** 
     * Is the vendor collection filtered?
     * @var $_isFiltered is set to true if any country/region/service filtering
     * has been done. This is used on template page to show intro content
     * when no filtering has been done (i.e. homepage only has intro)
     */
    public function isFiltered() {
        return $this->_isFiltered;
    }

    public function getVendorServices() {
        $vendors = $this->getVendors();
        $vendors->addFieldToFilter('active',1);
        $services = [];
        foreach($vendors as $vendor) {
            foreach( explode(",",$vendor->getServices()) as $service ) {
                if(!in_array($service, $services)) {
                    $services[] = $service;
                }
            }
        }
        return $services;
    }

    /**
     * Get an array of all the countries represented by vendors
     * [TODO: REMOVE? This function is currently not being used.]
     * @return array associative country_code => countryName
     */
    public function getVendorCountries() {
        $vendors = $this->getVendors();
        $vendors->addFieldToFilter('active',1);
        $countries = [];
        foreach($vendors as $vendor) {
            $country_code = $vendor->getCountryCode();
            if(!in_array($country_code, array_keys($countries))) {
                $countries[ $country_code ]  = $this->getCountryName( $country_code );
            }
        }

        return $countries;        
    }

    /**
     * Get the Services Array which is constructed as:
     * $array[ $country ][ $region ][ $service ]
     * @params are passed to put those elements first in result
     * @param string $country iso2 2 digit code
     * @param string $region region name
     * ORDERING/SORTING
     *    1) If $country or $country and $region are passed, those are put first
     *    2) The rest of the countries and regions are sorted alphabetically
     * @return array array[ countries ][ regions ][ services ]
     */
    public function getServicesArray( $country=null, $region=null ) {
        //$this->_messageManager->addError( 'country: ' . $country . ' region: ' . $region);
        $vendors = $this->getVendors();
        $vendors->addFieldToFilter('active',1);
        $services_array = [];
        $this->_vendorCount = [];
        foreach($vendors as $vendor) {
            $country_code = $vendor->getCountryCode();
            if(!in_array($country_code, array_keys($services_array))) {
                $services_array[$country_code]  = [];
                $this->_vendorCount[ $country_code ] = [];
            }
            $service_regions = explode(",",$vendor->getServiceRegions());
            foreach($service_regions as $service_region) {
                if($vendor->getCategory() !== 'ADS' && !isset($this->_vendorCount[ $country_code ][ $service_region])) {
                    $this->_vendorCount[ $country_code ][ $service_region] = 0;
                }
                if(!in_array( $service_region, $services_array[ $country_code ])) {
                    $services_array[ $country_code ][ $service_region ] = [];
                }
                if($vendor->getCategory() != 'ADS') {
                    $this->_vendorCount[ $country_code ][ $service_region] += 1;
                }
                $services = explode(",", $vendor->getServices());
                foreach($services as $service) {
                    if(!in_array( $service, $services_array[ $country_code ][ $service_region ] )) {
                        $services_array[ $country_code ][ $service_region ][] = $service;
                    }
                }
            }
        }
        
        $new_array = [];
        //Put passed params first and sort
        if(!empty($country) && isset($services_array[ $country ])) {
            $new_array[ $country ] = [];
            if(!empty($region) && isset($services_array[ $country ][ $region ])) {
                $new_array[ $country ][ $region ] = $services_array[ $country ][ $region ];
                $arr = $services_array[$country];
                ksort($arr);
                foreach( $arr as $service_region => $services ) {
                    if($service_region == $region) {
                        //we've already added it
                        continue;
                    }
                    $new_array[ $country ][ $service_region ] = $services; 
                }
            } 
            else {
                $arr = $services_array[ $country ];
                ksort($arr);
                $new_array[ $country ] = $arr;
            }
        }

        foreach( $services_array as $new_country => $service_regions ) {
            if($new_country == $country) {
                //we've already added it
                continue;
            }
            $new_array[ $new_country ] = [];
            $arr = $service_regions;
            ksort($arr);
            foreach($arr as $service_region => $services ) {
                $new_array[ $new_country ][$service_region] = $services;
            }
        }
        return $new_array;      
    }

    public function getVendorCount() {
        return $this->_vendorCount;
    }

    public function getPager() {
        $currentPage = intval($this->_request->getParam('p'));
        if($currentPage < 2) {
            $currentPage = 1;
        }
        $totalVendors = $this->getFilteredVendors()->getSize();
        $totalPages = ceil($totalVendors / $this->_vendorsPerPage);
        if($totalPages < 2) {
            return '';
        }
        $params = $this->getBackParams( $pager = false );
        $baseurl = '/gate/vendors/view/' . $params;
        $pager_links = '<div class="pager">Pages: ';
        for( $p=1 ; $p<=$totalPages ; $p++ ) {
            if($p == $currentPage) {
                $pager_links .= '[' . $p . '] ';
            } else {
                $pager_links .= '<a href="' . $baseurl . 'p/' . $p . '/">' . $p . '</a> ';
            }
        }
        $pager_links .= '</div>';
        return $pager_links;
    }
}