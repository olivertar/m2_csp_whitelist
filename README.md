# M2 CSP Whitelist

This module has been developed for Magento >= 2.4.8

## Installation

The extension must be installed via `composer`. To proceed, run these commands in your terminal:

```
composer require orangecat/cspwhitelist
php bin/magento module:enable Orangecat_CspWhitelist
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
