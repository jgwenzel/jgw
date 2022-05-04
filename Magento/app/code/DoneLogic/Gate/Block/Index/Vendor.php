<?php
namespace DoneLogic\Gate\Block\Index;
/**
 * @author John Wenzel johngwenzel@gmail.com
 */
use DoneLogic\Gate\Block\Index\Index;

class Vendor extends Index
{
    /**
     * @var $_meta
     * holds meta tag array so it can be constructed once.
     */
    protected $_meta;

    /**
     * Sets title, meta title, meta desciption and meta keywords 
     * @uses getVendorMeta() to get meta array
     */
    public function _prepareLayout() {  
        $meta = $this->getVendorMeta();
        $this->pageConfig->getTitle()->set(__($meta['title']));
        $this->pageConfig->setDescription(__($meta['description']));
        $this->pageConfig->setKeywords(__($meta['keywords']));
        return parent::_prepareLayout();
     }

    /**
     * Get the meta array by building it from Vendor data.
     * Array contains regular and og meta tags
     * Used by:
     * 1) _prepareLayout() for regular meta tags
     * 2) templates/metatags/vendors_view_meta.phtml for og meta tags
     * @return array meta tags array
     */
    public function getVendorMeta()
    {
        if(is_array($this->_meta)) {
            //it's already built
            return $this->_meta;
        }
        $vendor = $this->getVendor();
        if(!$vendor) {
            return $this->getDefaultMeta();
        }
        $category = $vendor->getCategory();
        $company = $vendor->getCompany();
        $country_code = $vendor->getCountryCode();
        $country = $this->getCountryName( $country_code );
        $region = $vendor->getRegionName();
        $city = $vendor->getCity();
        $service_regions = $vendor->getServiceRegions();
        $service_regions_array = explode(",",$service_regions);
        $services = $vendor->getServices();
        $services_array = explode(",", $services);
        $services_words_array = explode(" ",str_replace(","," ",$services));
        $desc_array = explode('.', $vendor->getDescription());
        $describe = array_shift($desc_array);
        $describe =  str_replace("\n"," ",$describe);
        if($category == 'ADS') {
            $region = $this->getParam('region');
            if(empty($region)) {
                $region = 'Any Region';
            }
            $title = $company . ' in ' . $region;
        } else {
            $title = $company . ' in ' . $city . ', ' . $region;
        }
        $description = $title . ' services include ' . str_replace(",",", ",$services) . '. ' . $describe . '.';

        $keywords = array($company, $city, $region );
        $keywords = array_merge($keywords, $services_array);
        $keywords = array_merge($keywords, $services_words_array);

        $base_url = $this->getBaseUrl();
        $url = $base_url . 'gate/vendors/vendor/id/' . $vendor->getVendorId() . '/' . str_replace("+","-",(urlencode($company)));

        $keywords = array_unique($keywords);
        $keywords_str = implode(",",$keywords);

        $image_url = $base_url . 'pub/media/' . $vendor->getImageUrl();

        $meta = [];
        $meta['title'] = $this->escapeHtml($title);
        $meta['description'] = $this->escapeHtml($description);
        $meta['keywords'] = $this->escapeHtml(strtolower($keywords_str));
        $meta['og_title'] = $this->escapeHtml($title);
        $meta['og_description'] = $this->escapeHtml($description);
        $meta['og_image'] = $image_url;
        $meta['og_url'] = $url;
        $this->_meta = $meta;
        return $meta;
    }

    /**
     * Get the edit vendor listing link if customer is logged in and has listing
     * Otherwise return empty string
     * @return string link or empty
     */
    public function getEditLink() {
        $vendor = $this->getVendor();
        if(!$vendor) {
            return '';
        }
        $customer_id = $this->getCustomerId();
        //if customer_id is 0, it could be admin or not logged in so we
        //don't want to allow editing
        if($customer_id > 0 && $customer_id == $vendor->getCustomerId()) {
            $link = '<span class="big"><i class="bi-pencil-fill"></i> <a href="/gate/vendors/editvendor/">Edit Your Listing</a></span>';
            return $link;
        }
        return '';
    }
}