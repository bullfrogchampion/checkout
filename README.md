Bullfrogchampion Checkout Extension
=====================
An Enterprise ready, easily customisable one page checkout solution for Magento.

Facts
-----
- version: 1.0.0
- extension key: Bullfrogchampion_Checkout
- [extension on GitHub](https://github.com/bullfrogchampion/checkout)

Description
-----------
This extension provides an easy to use one page checkout solution. This has been developed to be as easily customisable
as possible, by reducing and eliminating as much code as possible.

Requirements
------------
- PHP >= 5.2.0
- Mage_Core
- Mage_Checkout

Compatibility
-------------
- Magento >= 1.4

Installation Instructions
-------------------------
Always install on a development version of your site first!

1. Install the extension using composer (composer require "bullfrogchampion/checkout" "*") (Installation with modman or Magento connect also supported)
2. Clear the cache, logout from the admin panel and then login again.
3. Configure and activate the extension under System -> Configuration -> Sales -> Bullfrogchampion Checkout.

Uninstallation
--------------
1. Uninstall using composer (composer remove "bullfrogchampion/checkout") (uninstallation using modman or Magento Connect also supported)

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/bullfrogchampion/checkout/issues).

Road map
--------
* ~~HTTPS support~~
* Enterprise Features
    * ~~Gift Cards~~
    * Reward Points
    * Store Credit
* Gift Options
* Compatibility with https://github.com/webcomm/magento-boilerplate
* Rebuild totals table to handle the getItemHtml of the core checkout
* Refactor the JavaScript to give better structure and to remove all event handlers defined in the markup
* Refactor the CSS into sensible SCSS
* Replace all tabs with 4 spaces

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Jeremy Champion
[http://www.jeremychampion.com](http://www.jeremychampion.com)

Initial extension by [Sinabs](http://www.sinabs.fr)

License
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2015 Jeremy Champion