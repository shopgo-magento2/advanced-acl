Advanced ACL
============


#### Contents
*   [Synopsis](#syn)
*   [Overview](#over)
*   [Installation](#install)
*   [Tests](#tests)
*   [Contributors](#contrib)
*   [License](#lic)


## <a name="syn"></a>Synopsis

A module that extends Magento's ACL and adds more features to it.

## <a name="over"></a>Overview

Advanced ACL extends some ACL features such as system configuration access, by allowing users to specify config fields and groups that they would like to show.
The module allows users to specify admin panel menu items that they would like to show or hide.
It also allows users to disable access to some cache types and additional cache page actions.

## <a name="install"></a>Installation

Below, you can find two ways to install the advanced acl module.

### 1. Install via Composer (Recommended)
First, make sure that Composer is installed: https://getcomposer.org/doc/00-intro.md

Make sure that Packagist repository is not disabled.

Run Composer require to install the module:

    php <your Composer install dir>/composer.phar require shopgo/advanced-acl:*

### 2. Clone the advanced-acl repository
Clone the <a href="https://github.com/shopgo-magento2/advanced-acl" target="_blank">advanced-acl</a> repository using either the HTTPS or SSH protocols.

### 2.1. Copy the code
Create a directory for the advanced acl module and copy the cloned repository contents to it:

    mkdir -p <your Magento install dir>/app/code/ShopGo/AdvancedAcl
    cp -R <advanced-acl clone dir>/* <your Magento install dir>/app/code/ShopGo/AdvancedAcl

### Update the Magento database and schema
If you added the module to an existing Magento installation, run the following command:

    php <your Magento install dir>/bin/magento setup:upgrade

### Verify the module is installed and enabled
Enter the following command:

    php <your Magento install dir>/bin/magento module:status

The following confirms you installed the module correctly, and that it's enabled:

    example
        List of enabled modules:
        ...
        ShopGo_AdvancedAcl
        ...

## <a name="tests"></a>Tests

TODO

## <a name="contrib"></a>Contributors

Ammar (<ammar@shopgo.me>)

## <a name="lic"></a>License

[Open Source License](LICENSE.txt)
