=== Easy PayPal ===
Contributors: manojtd
Donate link: http://buy.thulasidas.com/easy-paypal
Tags: paypal, paypal ipn, e-commerce, shopping cart, payment gateway, digital goods, ipn, paypal integration
Requires at least: 3.3
Tested up to: 4.1
Stable tag: 5.50
License: GPL2 or later

Easy PayPal gets you started with your online business. Use PayPal IPN to sell digital goods without a shopping cart or complicated e-commerce setup.

== Description ==

*This plugin is going to be totally revamped and re-released as EZ PayPal (V6.00) by mid Jan 2015. The new version will sport a modern admin interface based on the twitter bootstrap framework, many more options and features. The current Easy PayPal V5.50 is the last archival release before the new version.*

*Easy PayPal* is the simplest possible way to sell your digital goods online. It helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. The whole flow is fully automated and designed to run unattended using PayPal IPN integration. There is no shopping cart or complicated e-commerce setup.

Do you have an application, PHP package, photograph, PDF book (or any other downloadable item) to sell? Find the set up of a shopping cart system too overwhelming? *Easy PayPal* may be the right solution for you.

Aiming at ease of use, *Easy PayPal* makes its admin page as simple as possible, helping the user concentrate on improving their products rather than maintaining them in the plugin.

= Features =

1. Minimal setup and administration load: *Easy PayPal* gets you started with your online shop within minutes, rather than hours and days.
2. Generous help and hints during setup: Whenever you need help, the information and hint is only a click away in *Easy PayPal*. (In fact, it is only a mouseover away.)
3. Automatic validation of admin and setup entries to minimize errors: *Easy PayPal* catches all the usual data entry errors so that you can afford to be a bit sloppy.
4. Very little programming knowledge required: *Easy PayPal* is written for creative people who have some digital products to sell. So it doesn't call for any deep computing knowledge. The most you will have to do is perhaps to set permission to a couple of folders.
5. IPN handling: *Easy PayPal* handles all the complex PayPal instant notification and data transfer to prevent unauthorized access.
6. Automatic download pages: Buyers are automatically redirected to a customized download page.
7. Self-service download link retrieval: If the PayPal information is not yet received by your server, *Easy PayPal* shows your buyer a page where he can retrieve his purchase link. (This feature reduced my support load by 90%).
8. Automated emails: In addition to the download page, *Easy PayPal* sends an automated email with the download link to your buyer as well. Just in case...
9. Easy Inventory maintenance: Easy to add new products to your inventory.
10. Automatic generation of an online shop: Once the first product is added, you can already see it on your *Easy PayPal* online shop.
11. Sandbox mode: *Easy PayPal* gives you the option to choose PayPal sandbox mode so that you can check your setup before going live.
12. Timely help: *Easy PayPal* sports a context-sensitive help system, so that you get timely help as you need it.
13. Now available in your own language using machine translation curtsey of Google and Microsoft.

*Easy PayPal* is available as a [Premium WordPress plugin](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95") which can also run as a standalone web application.

= Security Features =

Since *Easy PayPal* deals with money, it takes the security and integrity of your data very seriously. It also puts serious roadblocks to prevent unauthorized access to your server.

1. All login type actions are implemented in such a way as to virtually eliminate the possibility of SQL injection attacks.
2. The `setup.php` (which you are advised to remove after successful installation) script doesn't let you set up your installation twice. In fact, it won't even display the setup information the second time you run it. Reinstallation will require database manipulation.
3. Only one admin user is permitted. You cannot add another admin user.
4. There is no interface to recover your password once you install your system. No amount of database hacking will recover it. So please be careful to note it down in some secure location.
5. Strong validation of all user entries exposed to the world.

= Pro Features =

In addition to the fully functional Lite plugin version, there is a [Pro Version](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95") with many more features. If the following features are important to you, consider buying the *Pro* version. The *Pro* version is a completely rewritten application based on the twitter bootstrap framework. It will be ported as the lite version here in early Jan 2015. The description below is a bit out of date.

1. *Data Security*: The *Pro* version takes special measures to set up data verification links to ensure your sales data is safe and not susceptible to corruption. In technical terms, it checks for the existence of InnoDB in your MySQL installation, and uses it if found, setting up foreign keys to ensure referential integrity, and indices to guarantee performance. The Lite version uses the default MyISAM engine, fast and simple, but not exactly secure.
2. *IPN Logs*: In the pro version, you can choose to receive the log file for each IPN transaction via email.
3. *HTML Emails*: In the *Pro* version, you can send impressive HTML email to your customers rather than the boring plain text messages.
4. *Template Editor*: The email body, thank you page and download display are all editable in the *Pro* version.
5. Automatic handling of refunds and disputes. When you issue a refund on the PayPal website, the corresponding sale in your database will be set to inactive. And if a buyer registers a dispute, he (and you) will get a friendly email message stating that the dispute is being reviewed and handled.
6. E-Check handling. The *Pro* version recognizes e-check payments and sends a mail to the buyer regarding the delay for the check clearance.
7. *Sales Editor*: You can load a single sale or a bunch of sales on to a friendly interface and change their data. For instance, it will let you change the download expiry date and resend a download notification message -- one of the frequent support requests from the buyers.
8. *Unprocessed Sales Handler*: The Sales Editor also lets you load sales that were not handled, for instance, if PayPal didn't validate the IPN post-back. *ezPayPal* records *all* PayPal messages so that you can verify and ship even those sales at a later stage.
9. *Email Tools*: You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity.
10. *Product Version Support*: The *Pro* version supports versioning of your products. It will keep track of the version sold to your buyers and your current versions. So, if you want to send a product and version specific upgrade notice, you can do it with *Pro* version.
11. *Batch Upload*: The *Pro* version gives an easy way to upload your product files (when you release new versions, for instance), and keeps track of their versions.
12. *Additional Tools*: The *Pro* version also gives you a bunch of tools (php example files) that can help you migrate your existing sales data or product definitions.
13. *Data Migration*: Using this *Pro* tool, your database tables can be automatically upgraded to the later version without losing your sales info and other settings.
14. *DB Backup*: The *Pro* version has an option to generate a backup of your sales info to download to a safe location.
15. *DB Restore*: It also provides a means to restore (of course) a previously backed up data file, overwriting (or appending to, as you wish) the existing sales info.
16. *Security Audit*: The *Pro* version provides you with a tool to check your settings and installation for possible security issues.
17. *Product Updates*: Your customers can initiate product update checks. If the version they purchased is older than the current version on your shop, they can download the latest version. Bu default, the first update is provided free of cost, and the subsequent ones are chargeable at $0.95. In later versions, this update policy will be configurable on a product-by-product basis.
18. *Multi-Currency Support*: You can choose you currency on a per-product basis, and your auto-generated shop page will list the product with the right currency symbol.
19. *Tabbed Admin Interface*: In the Pro version, the admin page is tabbed with the Product Definition tab on top because we expect you to navigate to it most often after your ezPayPal is set up.

Do you have multiple web sites selling digital products? Would you like to consolidate and manage your sales in one central location? Then the Stand-Alone version of this package may be more appropriate. The [Pro Version](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95") of the standalone package (which can consolidate sales from multiple websites on a server) gives you all the features listed above and more.

= Optional Packages =

*Easy PayPal* is designed to be extensible. The following add-on extensions are ready.

1. [Reporting Engine](http://buy.thulasidas.com/ezpaypal "Easy PayPal Pro bundled with ezReports for only $19.95"): Maximize your sales by analyzing your sales. This reporting package makes slicing and dicing your sales data as easy as pie, so that you can spot opportunities. The reporting engine is now built into the new EZ PayPal Pro (V6.0+).
2. [Subscription Module](http://buy.thulasidas.com/ezpaypal "Easy PayPal Pro bundled with ezSubscribe for only $19.95"): If you want to add subscription products (support contract, text links, newsletters etc), this module will make it a snap. The subscription module also is now built into the new EZ PayPal Pro (V6.0+).

If you have the [Standalone Pro](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95") version of this plugin, you can have even fancier add-on modules.
2. [ezSupport](http://buy.thulasidas.com/ezsupport-module "Paid support module for EZ PayPal for $7.95"): Every complex software project, once deployed, generates significant support load. Most of the support questions are frivilous, where the end-user presents silly issues that are easily resolved by a cursory look at the documentation. How do we ask the end-user to RTFM without antagonizing them? I found that it could be done by switching to a paid support model. I started charging 95 cents per support questions, and my support load went down by two orders of magnitude. This ezSupport package is built on the excellent osTicket program. It works hand in hand with ezPayPal and provides you with a configurable support system. If you already have the [Standalone Pro](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95") version of ezPayPal, you can easily add [ezSupport](http://buy.thulasidas.com/ezsupport-module "ezSupport module for only $7.95"). [*[More Information](http://www.thulasidas.com/packages/ezsupport "Read more about ezSupport")*]
1. [ezAffiliates](http://buy.thulasidas.com/ezaffiliates "ezPayPal Pro bundled with ezAffiliates for only $12.95"): (*This module is not yet ready for EZ PayPal V6.0.*) Create your own affiliate network and go viral by turing your satisfied customers into your advertising affiliatees. This package, built on the publicly available Affiliates-for-All, integrates perfectly with to automate affiliate sales tracking and commission computation and more. If you already have the [Standalone Pro](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95") version of ezPayPal, you can easily add [ezAffiliates](http://buy.thulasidas.com/ezaffiliates "ezAffiliates module for only $4.95"). [*[More Information](http://www.thulasidas.com/packages/ezaffiliates "Read more about ezAffiliates")*]

In the pipeline are the following optional extensions:

1. ezTextLinks: Do you have a high page-rank site? Do you get a lot of requests for text links? They can be significantly more lucrative (by a factor of 100, in my case) than contextual ads such as AdSense. The returns can be even greater if you can deal with your advertisers directly, rather than via providers like Text Link Ads that take 50% of your revenue. ezTextLinks will handle payment, activate and expire links, send reminder emails and handle renewals etc. The plugin version is available as Easy Text Links in [Lite](http://wordpress.org/plugins/easy-text-links "Lite version of Easy Text Links") and [Pro](http://buy.thulasidas.com/easy-text-links "Manage your text links like a pro for $7.95") variants.

== Upgrade Notice ==

Preparing for the major update EZ PayPal V6.00 coming in a few days.

== Screenshots ==

1. Admin page layout, giving you access to all options and product definitions, with context sensitive help.
2. Your products automatically displayed by Easy PayPal.
3. Detailed and context-sensitive page help.
4. Tabbed Admin page for the Pro version.
5. Additional tools available in the Pro version.
6. The template editor to customize your emails and download pages.
7. The download page where your buyer is automatically forwarded to.
8. If the PayPal information is not yet received by your server, your buyer sees this screen where he can retrieve his purchase link. (This feature reduces the support load 90%).
9. The automatic email that your buyer will receive in addition to being forwarded to the download page.
10. A sample report chart.
11. A subscription product definition.

== Installation ==

1. Use WordPress plugin install interface to upload the contents of the archive `easy-paypal` to your server.
2. Use the WordPress Admin page under Settings -> Easy PayPal to make changes.
3. Upload your products using this interface.

Before you upload products (in step 3), you may have to create a product storage directory for *ezPayPal*. Again, the interface will prompt you with the command to execute.

Note that you have to use pretty permalinks for the auto-generated shop front to work. Take a look at the URL address of the shop front (in the address bar on your web browser). Does it say something like http://your.blog/?p=123? If so, go to your WordPress dashboard, Settings -> Permalinks and select any setting other than the (ugly) default one.

= Shortcodes =

Once a product is added to your repository, your e-shop is ready to be displayed on your blog. The display is controlled using WordPress ShortCodes. In order to show the whole shop, create a new page or post with the short code `[ezshop]`. This will present your whole e-shop in a neat tabular form. Note that by default, a page with the slug ez-shop is created for you. Please do not delete this page even if you create other e-shop pages.

Each product can be displayed as a "Buy Now" kind of link with the short code `[ezshop buy='prodID' link='yes']Buy this product now![/ezshop]. This will insert a link, which when clicked, will take your reader to a PayPal page to buy the product.

If you drop the `link='yes'` part, the page will transfer the reader to PayPal without waiting for user click.

You can display your product links using `[ezshop show='prodID' link='yes']Buy this product now![/ezshop]. This part is not fully developed yet, but it will show page describing your product benefits, with a count-down timer which will take your reader to PayPal in fifteen seconds.

= Installing Extensions =

Installing the extension modules is equally easy. To install [the reporting engine](http://buy.thulasidas.com/ezreports "Get the reporting engine for Easy PayPal Pro for $4.95"), for instance, just click on the Reports link on top right hand side part of your ezPayPal screen (on its WordPress Admin page) and follow the wizard-like instructions.

= More Documentation =

In your plugin folder, under `docs`, you will comprehensive documentation of the plugin. It is designed in such a way that you will be able to call up the relevant pages from the pages in a context-sensitive way.

== Frequently Asked Questions ==

= This plugin is quite complex. Do you have more documentation? =

In your plugin folder, under `docs`, there is the beginning of a full documentation. It is designed in such a way that you will be able to call up the relevant pages from the pages in a context-sensitive way. Future releases will progressively add more complete documentation.

The documentation is also hosted at my site in [HTML](http://buy.thulasidas.com/docs "HTML Documentation of ezPayPal") and [PDF](http://buy.thulasidas.com/docs/ezpp.pdf "Printable PDF Manual") formats.

= How can I contact the plugin author if I need help? =

This plugin uses a paid support model in order to manage the support load. Each [support ticket](http://support.thulasidas.com "Ask a support question") will be charged at $0.95 for the Lite version (and for the Pro version after a short free support period). The support ticket is valid for 72 hours.

= How do I customize the look and feel of the download page? =

Please edit the included file `custom.php` and uncomment the custom header and footer functions. You can then provide your own code for these functions, following the examples of the `ezppHeader()` and `ezppFooter()` functions in `htmlHelper.php`.

= I have trouble uploading my products. What do I do? =

Your product files are uploaded into a directory with a random name (so that a potential hacker will have hard time guessing it). It is likely that your web server doesn't have the privileges to create or modify this folder and files within. Click on the Show button on your Admin Control Panel to see what the directory name is. Then create the directory with that name, and apply `chmod 777` to make it writeable.

= I have installed, unloaded a product. Now what? =

Please see the "Installation" section in this readme. It will tell you how to display your products and e-shop on your blog.

= How do I manage products? =
= Looks like I can define only one product?! =

To add a new product, type in a product code in the "Product Code" field. You can then specify the rest of the product details.

To edit an existing product, select it using the drop-down menu (or type in its product code.) The product will be loaded and you can edit its details, just like defining a new product.

Note that the drop-down menu is editable. To add a new product, just type in a new Product Code even when the drop-down menu looks like a menu. This is the point that confused some of my users, leading them to think that they couldn't define new products.

= My shop front says "Product not found." But I have defined my products. What's going on? =

This happens when your permalink structure has not been prettified, in which case WordPress doesn't use the page slug for the auto-generated shop page. To confirm, look at the URL address of the shop front (in the address bar on your web browser). Does it say something like http://your.blog/?p=123? If so, go to your WordPress dashboard, Settings -> Permalinks and select any setting other than the (ugly) default one.

= My shop page seems messed up. Formatting and line widths are all wrong. How can I fix it? =

The format may get messed up when your theme tries to smart format the page by adding `<p>` or `<br />` tags. If you can disable it for your theme (by adding a `[raw]` tag for instance, as with the themes by MySiteMyWay), please do so for the auto-generated ez-shop page. You may also have to deactivate the [`wpautop()`](http://codex.wordpress.org/Function_Reference/wpautop "Info from WordPress") filter, probably by using [this plugin](http://wordpress.org/extend/plugins/wpautop-control/ "Plugin recommended in WordPress Codex").

= I see some text ([raw] and [/raw]) around my shop page. How can I get rid of it? =

For themes from MySiteMyWay, this plugin adds these short tags in an attempt to prevent auto-formatting. If you switched to a new theme, you may have these tags visible. You can get rid of them by simply editing the auto-generated ez-shop page.

= I change the currency in the product definition, but my sale page shows Dollar sign. Why? =

Full multi-currency support is available only in the [Pro Version](http://buy.thulasidas.com/ezpaypal "Get Easy PayPal Pro for $19.95"). I tried porting it to the lite version, but it turned out to be too complicated.

= I don't like the auto-generated shop page. Can you modify it? =

The ez-shop page is not meant to be a public page. It is a page needed for the plugin to receive messages from PayPal and handle them. It is also a quick page to show you that the plugin is working. Please create a pretty page with links using short codes as described below.

Each product can be displayed as a "Buy Now" kind of link with the short code `[ezshop buy='product_code']Buy this product now![/ezshop]`. This will insert a link, which when clicked, will take your reader to a PayPal page to buy the product.

Please do not delete the ez-shop page though; it is needed as the IPN listener to receive messages from PayPal and process them.

== Change Log ==

= Future Plans =

A major release (V6.00) is planned in early 2015. It is a complete rewrite of the whole ezPayPal package, sporting a modern, responsive admin interface based on the twitter bootstrap framework, and in-place editing of options and entities using AJAX.

= History =

* V5.50: Preparing for the major update EZ PayPal V6.00 coming in a few days. [Jan 10, 2015]
* V5.31: Improvements in the graphics. [Jan 2, 2015]
* V5.30: Compatibility with WP4.0, documentation changes. [Sep 7, 2014]
* V5.24: Updating a few language files, minor documentation changes. [Jun 18, 2014]
* V5.23: Making the storage location relative to fix an issue on Windows systems. [Jun 16, 2014]
* V5.21: Minor documentation and admin interface changes. [Jun 3, 2014]
* V5.20: Some bug fixes. Documentation and admin interface changes. [Jun 2, 2014]
* V5.10: Internationalizing strings. Compatibility with WordPress V3.90. [Apr 25, 2014]
* V5.01: Adding content length to transaction verification request. [Apr 14, 2014]
* V5.00: Adding a translation interface. [Mar 21, 2014]
* V4.30: Compatibility with WordPress 3.8. [Dec 18, 2013]
* V4.22: Setting up a validator for product_code to allow only alphanumeric and -/_ characters. [Nov 30, 2013]
* V4.21: Help documentation changes, removing the large PDF file from the repository. [Nov 29, 2013]
* V4.20: Bug fixes to persist storage location and product under modification. Major changes to the shop page to help the user. Buy Now button now takes only one click to go to PayPal. [Nov 19, 2013]
* V4.12: Minor fix to suppress a warning. [Nov 18, 2013]
* V4.11: Bug fixes to suppress notices. [Nov 13, 2013]
* V4.10: Compatibility with WordPress V3.7. [Nov 9, 2013]
* V4.00: Compatibility checks with WordPress V3.6. Including HTTP1.1 headers as specified by PayPal. [Aug 23, 2013]
* V3.90: Introducing internationalization using Google/Microsoft Translate Widgets. [May 20, 2013]
* V3.83: Fixes for compatibility with Easy Text Links Pro. [May 10, 2013]
* V3.82: Minor fix in checking for the existence of the Pro version. [May 6, 2013]
* V3.81: Handling product-specific link expiry. [Mar 29, 2013]
* V3.80: Adding support for [raw] shorttag. Improvements to session handling and delivery module [Feb 25, 2013]
* V3.79: Using form submit (instead of JavaScript) in product delivery module. Sanitizing tooltips. [Feb 21, 2013]
* V3.78: Proper use of SESSION variables. [Feb 18, 2013]
* V3.77: Serious bug fix. [Feb 15, 2013]
* V3.76: Bug fix in short code handling and toning down aggressive security checks. [Feb 13, 2013]
* V3.75: Adding a Quick Start help page. [Feb 4, 2013]
* V3.74: Bug fixes (Fatal error: Call-time pass-by-reference has been removed). [Jan 28, 2013]
* V3.73: Minor fixes, testing with WP3.5. [Dec 22, 2012]
* V3.72: Using business name in validating PayPal transactions. [Dec 5, 2012]
* V3.71: Documentation changes, updating screenshots. [Nov 6, 2012]
* V3.70: Bug fixes in shop display. [Oct 29, 2012]
* V3.61: Documentation changes only. [Oct 2, 2012]
* V3.60: Admin page changes to include support option. [Sep 28, 2012]
* V3.58: Updating the PDF documentation to be in sync with the HTML docs. [Sep 25, 2012]
* V3.57: The storage location is moved to wp_upload_dir so that a plugin update will not wipe out the inventory. [Sep 19, 2012]
* V3.56: Showing an error message if the product storage location cannot be created automatically. [Sep 8, 2012]
* V3.55: Showing a warning about Permalinks for the auto-generated shop page to work. Also closing comments on it. [Sep 8, 2012]
* V3.54: Minor interface and documentation improvements. [Sep 7, 2012]
* V3.53: Enhancements from user feedback: Partial currency-name support, auto-creation of storage folder etc. [Aug 29, 2012]
* V3.52: Allowing all-digit product codes. Taking care of some debug notices from WordPress debug mode. [Aug 27, 2012]
* V3.51: Documentation changes. [Aug 18, 2012]
* V3.50: Refinements: deactivation_hook, batch and template interface improvements in Pro. [Aug 17, 2012]
* V3.44: Adding the folder name in a troubleshooting hint. [Aug 15, 2012]
* V3.43: Fixing an error in the plugins_url name. The CSS and images are located fine now. [July 28, 2012]
* V3.42: Documentation changes. Bug fix in HTML mail template selection. [July 18, 2012]
* V3.41: Changing the length of one DB field to support older versions of MySQL. [July 17, 2012]
* V3.40: Adding Sandbox testing (previously a Pro feature) to the lite version. Using WP table prefix, if it exists. [July 17, 2012]
* V3.33: Testing compatibility with WP 3.4. [July 11, 2012]
* V3.31: Minor bug fixes. [July 6, 2012]
* V3.30: Adding editable select in the product definition screen. [July 5, 2012]
* V3.22: More documentation, coding improvements, priming the Pro version, links to online docs and manual in the readme.txt file. [July 4, 2012]
* V3.21: Adding more help files. [July 2, 2012]
* V3.20: Tests complete. Initial WP release. [June 30, 2012]
* V3.13: Implemented an auto-generated page ez-shop as IPN listener and delivery. [June 28, 2013]
* V3.12: Automated Initial installation. [June 27, 2012]
* V3.11: Initial testing complete. Forking WP version. [June 21, 2012]
* V3.10: The plugin version (*Easy PayPal*) is dev complete.
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
