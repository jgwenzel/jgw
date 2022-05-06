<?php
namespace DoneLogic\Gate\Block\Index;
/**
 * @author John Wenzel johngwenzel@gmail.com
 * Used by view/layout/customer_account_index.xml for customer dashboard
 */
use DoneLogic\Gate\Block\Index\Index;

class CustomerVendor extends Index
{
    public function getEditLink() {
        $vendor = $this->getVendor();
        if(!$vendor) {
            return '';
        }
        if($this->getCustomerId() == $vendor->getCustomerId()) {
            $link = '<span class="big"><i class="bi-pencil-fill"></i> <a href="/gate/vendors/editvendor/">Edit Your Listing</a></span>';
            return $link;
        }
        return '';
    }

    //shows in customer dashboard
    public function getDashboardContent() {
        $vendor = $this->getCustomerVendor();
        if(!$vendor) {
            $content = 'You do not have a Service Directory Listing. If you are a contractor, you should get one. It\'s <strong>Free Advertising!</strong>';
            return $content;
        }

        $content = '<h2>Listing At-a-Glance</h2>
            <img src="'. $this->getMediaUrl($vendor->getImageUrl()) . '" alt="' . $this->escapeHtmlAttr($vendor->getCompany()) . '" style="width:100px; height:auto;" />
            <h3>' . $this->escapeHtml($vendor->getCompany()) . '</h3>
            <span>Phone: ' . $this->escapeHtml($vendor->getPhone()) . ' (' . ($vendor->getShowPhone() == 1? 'public':'private') . ')</span><br />
            <span>Website: ' . $this->escapeHtml($vendor->getWebsite()) . ' (' . ($vendor->getShowWebsite() == 1? 'public':'private') . ')</span><br />
            <span>Email: ' . $this->escapeHtml($vendor->getEmail()) . ' (' . ($vendor->getShowEmail() == 1? 'public':'private') . ')</span><br />
            <span>Active?: ' . ($vendor->getActive() == 1? 'Listing is Active':'Listing is not yet Active. Please contact us if it\'s been over a couple days since
            you submitted listing.') . '</span>';
        return $content;
    }

    //shows in customer dashboard
    public function getCustomerVendorEditLink() {
        $vendor = $this->getCustomerVendor();
        if(!$vendor) {
            $link = '<a href="/gate/vendors/editvendor"><span>Create a Service Directory Listing</span></a><br />';
        }
        else {
            $link = '<a href="/gate/vendors/editvendor"><span>Update Your Service Directory Listing</span></a><br />';
        }
        return $link;

    }
    
    //shows in customer dashboard
    public function getCustomerVendorViewLink() {
        $vendor = $this->getCustomerVendor();
        if(!$vendor) {
            //It this is null, it breaks dashboard so we send empty span
            $link = '<span></span>';
        }
        else 
        {
            $vendor_id = $vendor->getVendorId();
            $company = urlencode($vendor->getCompany());
            $link = '<a href="/gate/vendors/vendor/vendor_id/' . $vendor_id . '/' . $company . '"><span>View Your Service Directory Listing</span></a><br />';
        }
        return $link;
    }
}