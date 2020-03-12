# TYPO3 Extension `backend_debug`

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/GeorgRinger/19.99)

This extension helps admins working in the backend. Currently implemented feature:

## Features

### Show field name next to title

Make it easy to know the database name of a field by just displaying its name next to the title. 
This feature is enabled only for admins.

![Show fieldname](/Resources/Public/Documentation/fieldname.png)

This feature is implemented in the TYPO3 Core since version 9.1 ([Changelog](https://docs.typo3.org/c/typo3/cms-core/master/en-us/Changelog/9.1/Feature-83461-ShowFieldnameNextToTitleInDebugMode.html)). To activate it, you have to set 
`$GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] = true;`.

## Installation

Use composer with `composer require georgringer/backend-debug`.
