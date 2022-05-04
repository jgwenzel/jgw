Vendor: DoneLogic
Module: DoneLogic_Core
Module: DoneLogic_Gate
Module: DoneLogic_Gtag

Author: John Wenzel johngwenzel@gmail.com
Github: https://github.com/jgwenzel
Website: https://donelogic.com

Please report errors, bugs or comments to johngwenzel@gmail.com
_______________________________________________________________________________
PREAMBLE:

USE AT YOUR OWN RISK. 

No claims or guarantees are offered for this code package. 

It is suggested that only experienced Magento developers
utilize this package, or any part of it.

_______________________________________________________________________________
LICENSE:

You are free to use and alter this code package for non-commerical
purposes. For commercial useage, please contact John Wenzel by emailing johngwenzel@gmail.com.
_______________________________________________________________________________

MODULE: DoneLogic_Core
This module creates a database table called donelogic_core with a simple schema

    record_id, name, value, description, updated_at, created_at

This table holds name/value pairs for use by other modules. Records may be 
added, edited or deleted through Core Data in Admin.

Core Config in Admin allows you to enable/disable module, which in this 
context, only means to disable frontend renderings, if any. Core currently has 
no frontend renderings, so changing this Yes/No value has no effect.
_______________________________________________________________________________

MODULE: DoneLogic_Gtag
NOTE: This module depends upon DoneLogic_Core.

When Gtag values are set through DoneLogic/Core, this module will render the 
head and body snippets on the frontend, right before (head_snippet) and after 
(body_snippet) the body tag.

If the module is disabled programatically through Admin->Gtag Config, the Gtags 
will not be rendered in the frontend. Instead, a simple comment will be 
rendered in order to alert the viewer of the source that the module is 
disabled.

* See GTAG SETUP below after INSTALLATION
_______________________________________________________________________________

MODULE: DoneLogic_Gate
NOTE: This module is a standalone and does not require DoneLogic_Core

Gate is a vendor services directory. It presents itself as a page with
country/region/service navigation in the sidebar. 

* When a link is clicked, the list of vendors is filtered and appears on the 
page in main content.

* One may click on vendor links to see the vendor listing expanded.

* Customers may submit a vendor listing for your approval. You approve by
setting Active=Yes in Admin->DoneLogic->Gate Vendors. Here, you may edit
all vendor listings or add new vendor listings.

* See GATE SETUP below after INSTALLATION

NOTE: Disabling this module must be done from command line.

_______________________________________________________________________________


INSTALLATION
These modules have not yet been packaged, but you can install them in developer
mode directly in the cli.

YOU SHOULD BE IN DEVELOPER MODE TO USE THESE INSTALLATION INSTRUCTIONS

1) As the Magento user, put the DoneLogic directory in [MAGENTO_ROOT]/app/code 
directory. (It may help to read step 2 before proceeding.) Once moved, the
directories should look like:

    [MAGENTO_ROOT]/app/code/DoneLogic
    [MAGENTO_ROOT]/app/code/DoneLogic/Core
    [MAGENTO_ROOT]/app/code/DoneLogic/Gtag
    [MAGENTO_ROOT]/app/code/DoneLogic/Gate

2) Make sure ownership and permissions are correct. This example is for apache2
where www-data is the apache2 user and mage is a Magento User we created. mage 
is in the www-data group, and owns the Magento files (or should). To fix 
ownership:

    cd [MAGENTO_ROOT]/app/code
    sudo chown -R mage:www-data DoneLogic
 
To fix permissions:

    cd [MAGENTO_ROOT]/app/code
    sudo find DoneLogic -type f -exec chmod 664 {} \;
    sudo find DoneLogic -type d -exec chmod 775 {} \;

3) Enable modules. If you are enabling all, then it would look like:

    cd [MAGENTO ROOT]
    php bin/magento module:enable DoneLogic_Core
    php bin/magento module:enable DoneLogic_Gtag
    php bin/magento module:enable DoneLogic_Gate

NOTE: Gate can standalone, Core can standalone, but Gtag requires Core.

//-----------------------------------------------------------------------------
// WARNING: rm -r [dir] MUST BE USED WITH CARE! 
//  This removes all the files recursively in [dir]. If you aren't familiar 
//  with it, please Google and learn more before proceeding. Used incorrectly, 
//  you can remove the wrong files, or worse yet, REMOVE ALL YOUR FILES!
//-----------------------------------------------------------------------------

4A) Run installation in developer mode:

    rm -r generated/*/*
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento cache:flush

4B) Run installation in production mode:

    php bin/magento maintenance:enable
    rm -r generated/*/*
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy
    php bin/magento cache:flush
    php bin/magento maintenance:disable

If you get compilation errors, especially about injectors, try the following:

    rm -rf generated/*/*
    php bin/magento cache:flush

if you still have problems you may have to clear the following directories
and do step 4A or step 4B again:

    cd [MAGENTO_ROOT]
    rm -r pub/static/*
    rm -r var/view_preprocessed/*
    rm -r generated/*/*

_______________________________________________________________________________

GATE SETUP
Setting up DoneLogic Gate

Upon Installation of DoneLogic Gate, do the following:
1) Login into Admin and go to DoneLogic -> Gate Vendors
2) Edit this listing called "_SERVICES_"
3) Add you services as explained
4) make inacive by selecting Active->No
5) Save
6) Add a vendor from backend and frontend and be sure all works
7) To add one on front end, you need to login as a customer.
8) Also, if ADS entry exists, see CONFIGURING ADS LISTING below 
for usage.

DO NOT DELETE THIS LISTING OR THIS DESCRIPTION. 
MAKE INACTIVE SO IT IS NOT VISIBLE ON FRONTEND.
COMPANY MUST BE _SERVICES_.
CATEGORY MUST BE SETTINGS.
COUNTRY VALUE SERVES AS THE DEFAULT COUNTRY FOR YOUR DIRECTORY.
ADD/EDIT/REMOVE SERVICES BY EDITING THIS LISTING.

ABOUT SETTINGS CATEGORY LISTING CALLED _SERVICES_
[As of now, the _SERVICES_ listing is the only SETTINGS CATEGORY listing.]

The _SERVICES_ listing is used to set the array of services that new vendors may choose from. It MUST exist. It must have all the desired services in a comma separated list with no spaces next to the commas. 

Example:
Service One,Service Two,Service Three

Notice that the services can have spaces, but no spaces between them, just a comma.

Be sure to have capitalization correct the first time. If you change it, or change anything for that matter, you may have to go through and adjust each vendors services.

Also, many fields here do not matter but exist because they are required by our forms (e.g. city, region, email...).

ABOUT VENDOR CATEGORY LISTINGS
A regular listing has category=='VENDOR', and that is the default in the
database table.
  * Listings are sorted by customer_id descending, meaning the latest
       customers listings come first.
  * Note that form fields are stripped of all tags on frontend customer
       form so no html is possible.
  * There exists a field called "content" in the database table that was
       originally intended to handle html content that would be displayed
       on the vendor view page. This functionality does not yet exist. We
       don't currently see a demand for customers wanting to use html.
  * There is no image delete on frontend, only replace.
  * The email field in listings is unique, so for admin with multiple
        listings, dummy emails set to hidden are suggested. 
  * On backend, admin must enter customer ID manually. This may be changed
        in the future.
  * A customer can only have one listing. All edit and view links expect
        they have only one.
  * More to be added as it comes to mind...

ABOUT ADS CATEGORY LISTINGS
The ADS Listing has the category == 'ADS' and descriptive name for company.
This lising serves to advertise that one can submit a listing.
This will show up in every region that it has as a service_region.
It is not counted in the Region (count) links in the navigation.
The description should  describe how and why to get a listing.
It should be active==1 if it is to show on front end.
With a low customer_id==0, it is designed to sort last in the list.
The ADS Listing should have the same country as the default set in _SETTINGS_,
or else it wouldn't show in the default country.
You can have a different ADS listing for each country, and/or region.
The service_regions determine where listings show up, not the region itself.

The functionality of the ADS category may be extended in the future.

_______________________________________________________________________________

GTAG SETUP
Setting up DoneLogic Gtag

Once installed, login to Admin and go to Admin->DoneLogic->Core Data

Then for each gtag_body and gtag_head, click edit and paste the snippet you get
at google here.

Once both are saved, go to System->Tools->Cache Management and click Flush 
Magento Cache.

_______________________________________________________________________________

CORE SETUP

There's reallly nothing to setup with DoneLogic Core, although you will be
using it's database table when you setup Gtag.