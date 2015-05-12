=== EZ PayPal ===
Contributors: manojtd
Donate link: http://buy.thulasidas.com/ezpaypal
Tags: paypal, paypal ipn, e-commerce, shopping cart, payment gateway, digital goods, downloadable products, paypal integration, wordpress paypal integration, woocommerce
Requires at least: 3.3
Tested up to: 4.2
Stable tag: 6.61

EZ PayPal gets you started with your online business. Use PayPal IPN, sell digital goods with instant download, and no carts. Official PayPal Partner.

== Description ==

*EZ PayPal* is the simplest possible way to sell your digital goods online. It helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. The whole flow is fully automated and designed to run unattended. Do you have an application, PHP package, photograph, PDF book (or any other downloadable item) to sell? Find the set up of a shopping cart system (such as woocommerce) too overwhelming? *EZ PayPal* may be the right solution for you, especially if you want to sell downloadable, digital goods. *EZ PayPal* can, however, handle physical goods as well, if you want to sell one item at a time without an add to cart, check out workflow.

*EZ PayPal* now features a thoroughly modern slick interface built on the twitter bootstrap framework, re-designed data model, and much more robust workflow. *Please note that V6+ database schema is not backward compatible with V5.xx. Consider backing up your database before updating.*

= Live Demo =

**EZ PayPal admin interface is feature-rich, user-friendly and functional. Please visit this fully operational [live demo site](http://demo.thulasidas.com/ezpaypal "Play with EZ PayPal Admin Interface") to see what it can do for you.**

Take a look at the features listed below to appreciate the vast array of tools this little PayPal/IPN implementation offers.

= Features =

1. Minimal setup and administration load: *EZ PayPal* gets you started with your online shop within minutes, rather than hours and days.
2. Generous help and hints during setup: Whenever you need help, the information and hint is only a click away in *EZ PayPal*. (In fact, it is only a mouseover away.)
3. Automatic validation of admin and setup entries to minimize errors: *EZ PayPal* catches all the usual data entry errors so that you can afford to be a bit sloppy.
4. Very little programming knowledge required: *EZ PayPal* is written for creative people who have some digital products to sell. So it doesn't call for any deep computing knowledge. The most you will have to do is perhaps to set permission to a couple of folders.
5. IPN handling: *EZ PayPal* handles all the complex PayPal instant notification and data transfer to prevent unauthorized access.
6. Automatic download pages: Buyers are automatically redirected to a customized download page.
7. Self-service download link retrieval: If the PayPal information is not yet received by your server, *EZ PayPal* shows your buyer a page where he can retrieve his purchase link. (This feature reduced my support load by 90%).
8. Automated emails: In addition to the download page, *EZ PayPal* sends an automated email with the download link to your buyer as well. Just in case...
9. Easy Inventory maintenance: Easy to add new products to your inventory.
10. Automatic generation of an online shop: Once the first product is added, you can already see it on your *EZ PayPal* online shop.
11. Sandbox mode: *EZ PayPal* gives you the option to choose PayPal sandbox mode so that you can check your setup before going live.
12. Timely help: *EZ PayPal* sports a context-sensitive help system, so that you get timely help as you need it.
13. WordPress integration: This application can be run as a WordPress plugin or as a fully standalone web application with its own database layer as you so choose. What's more, you can switch to the standalone mode from the WordPress plugin admin page of this application, while still using the WordPress authentication mechanism and database.

*EZ PayPal* is available as a [Premium WordPress plugin](http://buy.thulasidas.com/ezpaypal "Get EZ PayPal Pro for $19.95") which also works as a standalone web application. The standalone package is appropriate if you have multiple websites selling your products, but want to keep your sales consolidated.

= Security Features =

Since *EZ PayPal* deals with money, it takes the security and integrity of your data very seriously. It also puts serious roadblocks to prevent unauthorized access to your server.

1. All login type actions are implemented in such a way as to virtually eliminate the possibility of SQL injection or SQL-based DoS attacks.
2. The set up scripts do not let you set up your installation twice. In fact, they won't even display the setup information the second time you run it. Reinstallation will require database manipulation.
3. Only one admin user is permitted. You cannot add another admin user. You can edit and modify the admin details only after entering the current password.
4. There is no interface to recover your password once you install your system. No amount of database hacking will recover it. So please be careful to note it down in some secure location. Password may be reset using one-time emial link though.
5. Strong validation of all user entries exposed to the world at the client-side, server-side as well at the database layer.

= Pro Features =

In addition to the fully functional Lite version, there is a Pro Version with many more features. These features are highlighted by a red icon in the menus of the lite version.

If the following features are important to you, consider buying the Pro version.

1. *HTML Emails*: In the *Pro* version, you can send impressive HTML email to your customers based on your own design rather than the boring plain text messages.
2. *PDT Handling*: Payment Data Transfer posts the data to your return page, which the *Pro* version makes use of, in preparing the return page. This can be critical when there is a delay in PayPal posting the IPN messages to the listener.
3. *Address Handling*: The *Pro* version can optionally store the customer address details in a database table, if you need it.
4. *IPN Relay*: If you have other IPN listeners, the *Pro* version of *EZ PayPal* can relay the IPN messages to them, so that they receive the same IPN messages, enabling integration with multiple systems.
5. *Template Editor*: The email body, thank you page and download display are all editable in the *Pro* version.
6. *Logo and Branding*: EZ PayPal *Pro* gives you options to set your own logo on your shop/return pages and even on PayPal checkout page. You can also change the color scheme of these pages.
7. *Automatic handling of refunds and disputes*: When you issue a refund on the PayPal website, the corresponding sale in your database will be set to inactive. And if a buyer registers a dispute, he (and you) will get a friendly email message stating that the dispute is being reviewed and handled.
8. *E-Check handling*: The *Pro* version recognizes e-check payments and sends a mail to the buyer regarding the delay for the check clearance.
9. *Sales Editor*: You can load a single sale or a bunch of sales on to a friendly interface and change their data. For instance, it will let you change the download expiry date and resend a download notification message -- one of the frequent support requests from the buyers.
10. *Email Tools*: You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity.
11. *Product Version Support*: The *Pro* version supports versioning of your products. It will keep track of the version sold to your buyers and your current versions. So, if you want to send a product and version specific upgrade notice, you can do it with *Pro* version.
12. *Batch Upload*: EZ PayPal *Pro* gives an easy way (drag-and-drop) to upload your product files (when you release new versions, for instance), and keeps track of their versions.
13. *Product Updates*: Your customers can initiate product update checks. If the version they purchased is older than the current version on your shop, they can download the latest version. By default, the first update is provided free of cost, and the subsequent ones are chargeable at $0.95. This update policy is configurable on a product-by-product basis.
14. *Multi-Currency Support*: You can choose you currency on a per-product basis, and your auto-generated shop page will list the product with the right currency symbol.
15. *Product Meta Data Support*: Meta data is extra, free-form data you can attach to a product description. You can retrieve it and display it on you product description and download pages. The *Pro* version gives you a convenient editor to generate and modify product meta data.
16. *Upgradable Products*: You can define products that are upgradeable. For instance, you can sell a short eBook at an introductory price. If your buyer likes it, he has the option of buying the full book by paying the difference. (WIP)
17. *Troubleshooting Tools*: EZ PayPal *Pro* has the ability to email you detailed IPN log. It can also store the raw IPN data in the database for future investigation.
18. *Sales Statistics and Charts*: Maximize your sales by analyzing your sales. This reporting package makes slicing and dicing your sales and affiliate data a snap, so that you can spot opportunities.
19. *Subscription Module*: If you want to add subscription products (support contract, text links, newsletters etc), this module will make it a snap.
20. *Server Side Sales Processing*: Your sales are rendered in a searchable, sortable table (using bootstrap Datatables). In the *Pro* version, the table processing is done at your database server rather than the web browser, tremendously improving the performance once you have thousands of sales.
21. *Images Upload*: In the *Pro* version, you can upload your image assets to your server using a simple drag and drop interface and use them in your email and download templates, or as logos.
22. *Popup PayPal Window*: In the *Pro* version, you have an option to open the PayPal transaction page in a nice, compact window so that your buyers do not leave your shop to complete a purchase.
23. *Skinnable Shop and Return Pages*: In the *Pro* version, you can select the color schemes of your shop, return and update pages (as well as admin pages) from nine different skins.
24. *Flexible Shop Layout*: In EZ PayPal *Pro*, you can select either the default table view or a lis/grid view for your e-shop display. The list/grid view is switchable on the frontend as well.
If you buy the *Pro* version, you will get an upgrade notice (using the last item listed above) when the features that are Work-in-Progress are completed. You will be able to update free of charge.

= Optional Packages =

*EZ PayPal* is designed to be extensible. The following add-on extensions are ready or being tested.

1. [ezSupport](http://buy.thulasidas.com/ezsupport-module "ezSupport for only $7.95"): Every complex software project, once deployed, generates significant support load. Most of the support questions are frivolous, where the end-user presents silly issues that are easily resolved by a cursory look at the documentation. How do we ask the end-user to RTFM without antagonizing them? I found that it could be done by switching to a paid support model. I started charging 95 cents per support questions, and my support load went down by two orders of magnitude. This ezSupport package is built on the excellent osTicket program. It works hand in hand with EZ PayPal and provides you with a configurable support system. If you already have the [Pro](http://buy.thulasidas.com/ezpaypal "Get EZ PayPal Pro for $19.95") version of EZ PayPal, you can easily add [ezSupport](http://buy.thulasidas.com/ezsupport-module "ezSupport module for only $7.95"). [*[More Information](http://www.thulasidas.com/packages/ezsupport "Read more about ezSupport")*]
2. [ezAffiliates](http://buy.thulasidas.com/ezaffiliates-module "ezAffiliates for only $4.95"): Create your own affiliate network and go viral by turing your satisfied customers into your advertising affiliatees. This package, built on the publicly available Affiliates-for-All, integrates perfectly with to automate affiliate sales tracking and commission computation and more. If you already have the [Pro](http://buy.thulasidas.com/ezpaypal "Get EZ PayPal Pro for $19.95") version of EZ PayPal, you can easily add [ezAffiliates](http://buy.thulasidas.com/ezaffiliates-module "ezAffiliates module for only $4.95"). [*[More Information](http://www.thulasidas.com/packages/ezaffiliates "Read more about ezAffiliates")*]

In the pipeline are the following optional extensions:

1. ezTextLinks: Do you have a high page-rank site? Do you get a lot of requests for text links? They can be significantly more lucrative (by a factor of 100, in my case) than contextual ads such as AdSense. The returns can be even greater if you can deal with your advertisers directly, rather than via providers like Text Link Ads that take 50% of your revenue. ezTextLinks will handle payment, activate and expire links, send reminder emails and handle renewals etc. The plugin version is available as Easy Text Links in [Lite](http://wordpress.org/plugins/easy-text-links "Lite version of Easy Text Links") and [Pro](http://buy.thulasidas.com/easy-text-links "Manage your text links like a pro for $7.95") variants.

1. Express Checkout: Express Checkout is a way to integrate PayPal even more seamlessly with your web site by letting your buyer complete the purchase process without leaving your product page.

The reporting module and the subscription module that were sold separately have now been integrated with the [Pro](http://buy.thulasidas.com/ezpaypal "Get EZ PayPal Pro for $19.95") version of this package.

== Upgrade Notice ==

Admin page compatibility checks and improvements.

== Screenshots ==

1. Dashboard, showing the beautifully designed admin interface.
2. Tour and Help, to quickly get started with EZ PayPal.
3. Editable product listing on your admin page. The table is sortable and searchable.
4. Managing your product categories, showing how to edit an attribute.
5. Configuration screen. Note the help popover showing what the option means and does.
6. Managing your account profile, password etc.
7. Your automatically generated e-shop. Your products are listed in a neat, sortable, searchable table with a "Buy Now" button.
8. The screen resulting from a "Buy Now" button click.
9. The return screen after a successful purchase. This is the download page where your buyer is automatically forwarded to.
10. The return screen if the IPN message hasn't been posted. If the PayPal information is not yet received by your server, your buyer sees this screen where he can retrieve his purchase link. (This feature reduced my support load by 90%).
11. Advanced shop configuration.
12. Your e-shop, using a dark theme and grid view.
13. Sales charts in the Pro version.
14. Sales summary and details.
15. Subscription summary and details in the Pro version.
16. Template editor in the Pro version.

== Installation ==

To install it as a WordPress plugin, please use the plugin installation interface.

1. Search for the plugin EZ PayPal from your admin menu Plugins -> Add New.
2. Click on install.

It can also be installed from a downloaded zip archive.

1. Go to your admin menu Plugins -> Add New, and click on "Upload Plugin" near the top.
2. Browse for the zip file and click on upload.

Once uploaded and activated,

1. Visit the EZ PayPal plugin admin page to configure it.
2. Take a tour of the plugin features from the EZ PayPal admin menu Tour and Help.

If you would like to temporarily switch to the standalone mode of the plugin, click on the "Standalone Mode" button near the top right corner of EZ PayPal admin screens. You can install it permanently in standalone mode (using its own database and authentication) by uploading the zip archive to your server.

1. Upload the contents of the archive `ezpaypal` to your server.
2. Browse to the location where your uploaded the package (`http://yourserver/ezpaypal`, for instance) using your web browser, and click on the green "Launch Installer" button.
3. Follow wizard to visit the admin page, login, configure basic options and define products.

Before you upload products (in step 3), you may have to create a product storage directory for *EZ PayPal*. Again, the interface will prompt you with the command to execute.

== Frequently Asked Questions ==

= This program is quite complex. Do you have more documentation? =

You will find a help button on almost every admin screen of *EZ PayPal* near the top right side of every panel. Clicking on it will bring up a nice dialog box with context-sensitive help.

To get started, you might want to use the admin menu item "Tour and Help." The tour will walk you through the features, and the help text on the page will give you everything you need to get started.

= Can I use this plugin to ship physical goods? =

Although EZ PayPal is designed to handle downloadable digital goods or virtual services, I have implemented basic support for physical goods in the Pro version. To use it, add meta-data to the product.
1. Click on the Products menu item.
2. Add a new product or edit an existing one.
3. Click on the *Edit Meta Data* button to bring up the meta-data editor.
4. Create a new key-value pair with key (entry in the fist column) as `shipping` and the value (entry in the second column) to be the shipping charges. Also, ensure that you have defined the product to require the buyers to give their shipping address.

= I have trouble uploading my products. What do I do? =

Your product files are uploaded into a directory with a random name (so that a potential hacker will have hard time guessing it). It is likely that your web server doesn't have the privileges to create or modify this folder and files within. Click on the Show button on your Admin Control Panel to see what the directory name is. Then create the directory with that name, and apply `chmod 777` to make it writeable.

= How do I manage products? =

To edit your products, use the menu item Products. It will list your products in an editable table. You can  click on any value in the table and edit it in place. If you would like to see all the attributes of the product, click on the edit button in the last column.

In order to add a new product, click on the green "Add New Product" button and type in your values in the product creation screen.

= Why do I get error message saying something about direct access to plugin files? =

This plugin admin interface is designed with a loosely coupled architecture, which means it interacts with the WordPress core only for certain essential services (login check, plugin activation status, database access etc). Loosely coupled systems tend to be more robust and flexible than tightly integrated ones because they make fewer assumptions about each other. My plugin admin pages are fairly independent, and do not pollute the global scope or leak the style directives or JavaScript functions. In order to achieve this, they are loaded in iFrames within the WordPress admin interface.

Your web server needs direct access to the plugin files to load anything in an iFrame. Some aggressive security settings block this kind of access, usually through an `.htaccess` file in your `wp-content` or `plugins` folders, which is why this plugin gives a corresponding error message if it detects inability to access the files (checked through a `file_get_contents` call on a plugin file URL). But some systems implement further blocks specifically on `file_get_contents` or on iFrames with specific styles (using `mod_securty` rules, for instance), which is why the plugin provides a means to override this auto-detection and force the admin page.

= Is the direct access to plugin files a security hole? =

Note that it is only your own webserver that needs direct access to the PHP files. The reason for preventing such access is that a hacker might be able to upload a malicious PHP (or other executable script) to your web host, which your webserver will run if asked to. Such a concern is valid only on systems where you explicitly permit unchecked file uploads. For instance, if anyone can upload any file to your media folder, and your media folder is not protected against direct access and script execution, you have given the potential hacker an attack vector.

In this plugin, its media/banner upload folder has a multiple layers protection:
1. Only users logged in as admin can ever see the upload interface.
2. The upload script accepts only media file types.
3. The backend AJAX handler also checks for safe file types.
4. The media storage locations are protected against script execution.

So allowing your webserver to serve the plugin admin files in an iFrame is completely safe, in my judgement.

== Change Log ==

= Future Plans =

= History =

* V6.61: Admin page compatibility checks and improvements. [May 13, 2015]
* V6.60: Compatibility with WordPress 4.2. [April 25, 2015]
* V6.56: New option to display product image on the checkout page. [April 24, 2015]
* V6.55: Improvements in the admin dashboard. [April 15, 2015]
* V6.54: Improvements in the admin dashboard. [April 14, 2015]
* V6.53: Launching a demo site. [April 11, 2015]
* V6.52: Turning off caching of category list to avoid stale category names. [April 8, 2015]
* V6.51: Some more usability improvements. [April 6, 2015]
* V6.50: Implementing list/grid view for the e-shop (Pro feature). Displaying product description/image as a modal dialog on the e-shop. Option to display product description/image upon Buy Now button click. Numerous enhancements in metadata editor, sales table display etc. [April 6, 2014]
* V6.40: Fixing a style that may have caused the admin page not to appear on some blogs. [April 4, 2015]
* V6.39: More compatibility checks. [April 2, 2015]
* V6.38: Fixing a charting module for more accurate display, and other minor changes. [Mar 28, 2015]
* V6.37: Removing a spurious label in return.php that may have caused problems in older PHP installations. [Mar 27, 2015]
* V6.36: Making the shop and return pages skinnable, fixing an error in assets upload (Pro features). [Mar 23, 2015]
* V6.35: Compatibility check on the plugin admin page. [Mar 20, 2015]
* V6.34: Fixing a bug that prevented the proper display of the return page. [Mar 18, 2014]
* V6.33: Code to suppress some notices. New feature in the Pro version -- alternate product. [Mar 16, 2015]
* V6.32: Improvements in the login check functions. Adding a Pro feature to popup the PayPal transaction window. [Mar 9, 2015]
* V6.31: Code cleanup. [Mar 7, 2015]
* V6.30: Fixes in the sales update module. [Mar 6, 2015]
* V6.29: Changes in the update module [Mar 5, 2015]
* V6.28: Adding better email error handling. [Feb 27, 2015]
* V6.27: Using wp_email as default. Adding AJAX return code in file uploads. [Feb 27, 2015]
* V6.26: Bug fix in download link generation. [Feb 26, 2015]
* V6.25: Improvements in shortcode handling. [Feb 25, 2015]
* V6.24: Fixes in database prefix handling and enhancements in subscription products. [Feb 24, 2015]
* V6.23: Minor bug fixes in the sales table and ezSupport modules. [Feb 23, 2015]
* V6.22: Fixing the public URLs generated by EZ PayPal for some blog installations. [Feb 14, 2015]
* V6.21: Fixing a name collision with a WP Jetpack plugin. Fixes to the update module and hardening the assets folder. [Feb 3, 2015]
* V6.20: Suppressing product ID in the public shop page. Fixing minor AJAX errors on some systems. Misc refactoring changes. [Jan 31, 2015]
* V6.17: Minor fixes in the setup scripts. [Jan 27, 2014]
* V6.16: Switching to the http_response_code function for status headers. [Jan 26, 2015]
* V6.15: Fixing W3 validation errors for clean HTML5. [Jan 23, 2015]
* V6.14: Fixing some obscure errors using CORS headers. [Jan 22, 2015]
* V6.13: Hardening the plugin folder by adding index.php to subfolders. Removing .htaccess that may create some issues. [Jan 21, 2015]
* V6.12: Fixing product updates section. [Jan 15, 2015]
* V6.11: Emergency bug fix in purchase handling. Please update. [Jan 15, 2015]
* V6.10: Fixes to the dispute and subscription handlers. Dev complete now. [Jan 14, 2015]
* V6.03: More post deployment fixes. [Jan 13, 2015]
* V6.02: More post deployment fixes. [Jan 11, 2015]
* V6.01: Post deployment fixes. [Jan 11, 2015]
* V6.00: Complete rewrite and redesign of the package. It now features a modern admin interface based on the twitter bootstrap framework. [Jan 2, 2015]
* V5.70: Adding a secondary PayPal email option for high-value transactions so as to minimize PayPal fees. [Oct 15, 2014]
* V5.62: Improvements in the graphics. [Sep 29, 2014]
* V5.60: Compatibility with WP4.0, documentation changes. [Sep 7, 2014]
* V5.50: Adding the ability to store and display addresses. [Aug 15, 2014]
* V5.40: New feature. Setup IPN forwarders. [Aug 12, 2014]
* V5.30: Basic support for physical goods. Improvements in product-meta data editor. [Aug 9, 2014]
* V5.24: Tighter checks on emails and transaction ids for security. [Jul 19, 2014]
* V5.22: Making product update price, update section, SMTP mail etc. user-configurable. [May 31, 2014]
* V5.00: Numerous refactoring changes. Internationalization. [Apr 25, 2014]
* V4.76: Porting the reporting engine to the plugin version. [Dec 30, 2013]
* V4.75: Suppressing notices/warnings from some PEAR email functions. [Dec 16, 2013]
* V4.73: Documentation changes. Modifying the validator for product_code to allow -/_ characters. [Nov 29, 2013]
* V4.72: Fixes to DB details confusion between stand-alone and WP versions. [Nov 26, 2013]
* V4.71: Security fixes on some Pro files. [Nov 20, 2013]
* V4.70: Buy Now button now takes only one click to go to PayPal. [Nov 19, 2013]
* V4.63: Bug fixes to persist storage location and product under modification. [Nov 19, 2013]
* V4.62: Minor fix to suppress a warning. [Nov 18, 2013]
* V4.61: Adding integration between Easy Text Links and stand-alone EzPayPal. [Nov 6, 2013]
* V4.60: Changing to MySQLi in preparation for PHP5.5+. [Oct 13, 2013]
* V4.51: Separating download page and email template editors. [Sep 26, 2013]
* V4.50: New template editor -- grouping related templates (email/text/html) together. [Sep 2, 2013]
* V4.40: Including HTTP1.1 headers as specified by PayPal. [Aug 23, 2013]
* V4.27: Handling product link expiry. [Mar 23, 2013]
* V4.26: Exposing product link expiry. [Mar 12, 2013]
* V4.23: Using form submit (instead of JavaScript) in product delivery module. Sanitizing tooltips. [Feb 21, 2013]
* V4.22: A few more bug fixes. [Feb 18, 2013]
* V4.21: A couple of bug fixes. [Feb 18, 2013]
* V4.20: Proper use of SESSION variables. [Feb 18, 2013]
* V4.19: Serious bug fix. [Feb 15, 2013]
* V4.18: Bug fix in short code handling and toning down aggressive security checks. [Feb 13, 2013]
* V4.17: Adding a Quick Start help page. [Feb 4, 2013]
* V4.16: Refactoring to auto-deactivate the lite version, if needed. [Dec 23, 2012]
* V4.15: Minor fixes, testing with WP3.5. [Dec 22, 2012]
* V4.14: Using business name in validating PayPal transactions. [Dec 5, 2012]
* V4.13: Minor fixes. [Nov 7, 2012]
* V4.12: Documentation changes. [Nov 6, 2012]
* V4.11: Bug fixes in setup and shop display. [Oct 29, 2012]
* V4.10: Adding the plugin version within the standalone package. [Oct 19, 2012]
* V4.00: Rolling out ezSupport module. [Oct 16, 2012]
* V3.91: Bug fix / maintenance release. [Oct 3, 2012]
* V3.90: Rolling out a new pro feature product meta data editor. And adding batch mail support. [Oct 2, 2012]
* V3.80: Adding in-app update check and install. [Sep 28, 2012]
* V3.71: In WP plugin version, the storage location is moved to wp_upload_dir. [Sep 19, 2012]
* V3.70: Rolling out the optional add-on module ezSupport - for paid support. [Sep 13, 2012]
* V3.60: Showing an error message if the product storage location cannot be created automatically. [Sep 8, 2012]
* V3.56: Tabbed interface for the Admin Page. [Sep 2, 2012]
* V3.55: Enhancements from user feedback: Partial currency-name support, auto-creation of storage folder etc. [Aug 29, 2012]
* V3.53: Allowing all-digit product codes. [Aug 27, 2012]
* V3.52: Refunding a transaction now marks the sale as Dead/Refunded. [Aug 26, 2012]
* V3.51: Minor fixes ported from the plugin version improvements. [Aug 18, 2012]
* V3.43: Documentation changes. Bug fix in HTML mail template selection. [July 18, 2012]
* V3.42: Changing the length of one DB field to support older versions of MySQL. [July 17, 2012]
* V3.32: Minor enhancement in ez-update.php and a bug fix in the pro feature emailTools.php. [July 14, 2012]
* V3.31: Bug fixes in the pro feature salesEditor.php. [July 5, 2012]
* V3.30: Adding editable select in the product definition screen. [July 5, 2012]
* V3.22: More documentation, coding improvements, priming the Pro version, links to online docs and manual in the readme.txt file. [July 4, 2012]
* V3.21: Adding more help files. [July 2, 2012]
* V3.20: Tests complete. Initial WP release. [June 30, 2012]
* V3.13: Implemented an auto-generated page ez-shop as IPN listener and delivery. [June 28, 2013]
* V3.12: Automated Initial installation. [June 27, 2012]
* V3.11: Initial testing complete. Forking WP version. [June 21, 2012]
* V3.10: The plugin version (*Easy PayPal*) is dev complete.
* V3.03: Adding some documentation. [June 17, 2012]
* V3.02: Bug fixes: empty file in product definition should not delete existing file definition in the DB. New file uploaded should trigger the deletion of the existing file. [May 24, 2012]
* V3.01: Adding validation of email address in delivery and update modules. [May 22, 2012]
* V3.00: Ready to cut over on buy.thulasidas.com, with affiliate support. [May 19, 2012]
* V2.80: Product update handler: Dev and local tests complete. [May 19, 2012]
* V2.70: New Pro feature: Product updates. [May 17, 2012]
* V2.60: New Pro feature: Email IPN logs to Webmaster. [May 10, 2012]
* V2.50: Test complete. Ready to cutover. RC1. [May 9, 2012]
* V2.40: Deployed on my server. Remote testing of ezPayPal (not ezAffiliates) complete. [May 5, 2012]
* V2.30: Local testing complete. About to deploy it on the server. [May 4, 2012]
* V2.20: EZ Affiliates fully integrated. [May 1, 2012]
* V2.13: Login session timeout implemented. [Apr 23, 2012]
* V2.12: Minor bug fixes in the new features. Ready to be deployed now. [Apr 22, 2012]
* V2.11: Completed the Migration Tools. [Apr 21, 2012]
* V2.10: Added Pro tools: emailTools, salesEditor. Pro features: dispute handler, extra templates, options on html templates, versioning. Integration with ezAffiliates package.
* V2.00: Numerous improvements. Prepping the Plugin version.
* V1.10: Numerous improvements. Prepping the Pro version.
* V1.00: Initial Release
