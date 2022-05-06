The bulk of the SendEmail module comes from RAKESH JESADIYA. I thank
him for posting it.

https://www.rakeshjesadiya.com/send-mail-from-custom-module-magento-2/

I call it in a different way though, because the $this->helper method
has been deprecated, Instead, I inject the class into my block php
like so:

This is in DoneLogic\Gate\Block\Index\Index.php

    namespace DoneLogic\Gate\Block\Index;

    ...
    use DoneLogic\SendEmail\Helper\Data as MailHelper;
    ...

    class Index extends Template 
    {
        ...
        protected $_mailHelper;

        public function __construct(
            ...
            MailHelper $mailHelper,
            $data = []
        ) 
        {
            parent::__construct($context, $data);
            
            ...
            $this->_mailHelper = $mailHelper;
        }

        ...other methods...

            /**
            * @return void
            */
        /**
        * @return void
        */
        public function runTriggers() {
            if($this->isLoggedIn()) {
                $result = $this->_request->getParam('submit');
                if($result == 'yes') {
                    $name = $this->_request->getParam('name');
                    if($name) {
                        $company = urldecode($name);
                        $message = 'Good News! ' . $this->escapeHtml($company) . ' has added or updated a Service Directory Listing.';
                    } else {
                        $message = 'A vendor has added or updated a Service Directory Listing. Company name was not set in Block/Vendors::runTriggers().';
                    }
                    $this->_mailHelper->sendMail( $message, 'admin');
                }
            }
        }
    }

Then, in my template .phtml files, I call $this->runTriggers();

So, the email is triggered by certain params in url. For my purposes this 
works. I just wanted to be notified if someone filled out the form in
the Service Directory module.

Note also that I changed SendMail() to accept a parameter $message, and
an email address. If 'admin' is sent, it uses the general admin setting.

See DoneLogic/SendEmail/Helper/Data::SendMail() method to learn more.