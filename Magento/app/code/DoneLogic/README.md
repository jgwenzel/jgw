Vendor: DoneLogic
Module: DoneLogic_Core
Module: DoneLogic_Gtag

TAGLINE: Get Google Tag Manager Gtags into your Magento Application.

Author: John Wenzel johngwenzel@gmail.com
Github: https://github.com/jgwenzel
Website: https://donelogic.com

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

3) Enable modules:

    cd [MAGENTO ROOT]
    php bin/magento module:enable DoneLogic_Core
    php bin/magento module:enable DoneLogic_Gtag

4) Run installation:

    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento cache:clean

If you get compilation errors, especially about injectors, you may have to
clear the following directories:

//-----------------------------------------------------------------------------
// WARNING: sudo rm -r [dir] MUST BE USED WITH CARE! 
//  This removes all the files recursively in [dir]. If you aren't familiar 
//  with it, please Google and learn more before proceeding. Used incorrectly, 
//  you can remove the wrong files, or worse yet, REMOVE ALL YOUR FILES!
//-----------------------------------------------------------------------------

    cd [MAGENTO_ROOT]
    sudo rm -r pub/static/*
    sudo rm -r var/view_preprocessed/*
    sudo rm -r generated/*/*


