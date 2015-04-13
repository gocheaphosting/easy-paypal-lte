<?php
if (!empty($no_visible_elements)) {
  ?>
  <a href="#" class="btn btn-warning goPro" style="float:right" data-toggle="tooltip" title="Get the Pro Version Now for $19.95!"> <i class="glyphicon glyphicon-shopping-cart"></i> Buy EZ PayPal Now!</a>
  <?php
}
$features2 = "<p>Since the Pro version has more features than could be listed on one screen, here is the list continued.</p>

<ol start=12>
  <li><em>Batch Upload</em>: The <em>Pro</em> version gives an easy way (drag-and-drop) to upload your product files (when you release new versions, for instance), and keeps track of their versions.</li>
  <li><em>Product Updates</em>: Your customers can initiate product update checks. If the version they purchased is older than the current version on your shop, they can download the latest version. By default, the first update is provided free of cost, and the subsequent ones are chargeable at $0.95. This update policy is configurable on a product-by-product basis.</li>
  <li><em>Multi-Currency Support</em>: You can choose you currency on a per-product basis, and your auto-generated shop page will list the product with the right currency symbol.</li>
  <li><em>Product Meta Data Support</em>: Meta data is extra, free-form data you can attach to a product description. You can retrieve it and display it on you product description and download pages. The Pro version gives you a convenient editor to generate and modify product meta data.</li>
  <li><em>Upgradable Products</em>: You can define products that are upgradeable. For instance, you can sell a short eBook at an introductory price. If your buyer likes it, he has the option of buying the full book by paying the difference. (WIP)</li>
  <li><em>Troubleshooting Tools</em>: The <em>Pro</em> version has the ability to email you detailed IPN log. It can also store the raw IPN data in the database for future investigation.</li>
<li><em>Sales Statistics and Charts</em>: Maximize your sales by analyzing your sales. This reporting package makes slicing and dicing your sales and affiliate data a snap, so that you can spot opportunities.</li>
<li><em>Subscription Module</em>: If you want to add subscription products (support contract, text links, newsletters etc), this module will make it a snap.</li>
<li><em>Server Side Sales Processing</em>: Your sales are rendered in a searchable, sortable table (using bootstrap datatable). In the <em>Pro</em> version, the table processing is done at your database server rather than the web browser, tremendously improving the performance once you have thousands of sales.
<li><em>Image Assets Manager</em>: In the <em>Pro</em> version, you can upload your image assets to your server using a simple drag and drop interface and use them in your email and download templates, or as logos. While editing the templates, the images can be included using an intuitive gallery-like interface.</li>
<li><em>Popup PayPal Window</em>: In the <em>Pro</em> version, you have an option to open the PayPal transaction page in a nice, compact window so that your buyers do not leave your shop to complete a purchase.</li>

</ol>";
?>
<h2>EZ PayPal<br>
  <small>Your Own E-Shop</small>
</h2>
<?php
if (empty($no_visible_elements)) {
  EZ::showService();
}
?>
<p><em> EZ PayPal</em> is the simplest possible way to sell your digital goods online. It helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. The whole flow is fully automated and designed to run unattended.</p>

<p>Do you have an application, PHP package, photograph, PDF book (or any other downloadable item) to sell? Find the set up of a shopping cart system too overwhelming? <em> EZ PayPal</em> may be the right solution for you. Take a look at the following lists to appreciate the vast array of features this little PayPal/IPN implementation offers.</p>

<h2 class="btn btn-primary btn-help" data-content="<ol>
    <li>Minimal setup and administration load: <em> EZ PayPal</em> gets you started with your online shop within minutes, rather than hours and days.</li>
    <li>Generous help and hints during setup: Whenever you need help, the information and hint is only a click away in <em> EZ PayPal</em>. (In fact, it is only a mouseover away.)</li>
    <li>Automatic validation of admin and setup entries to minimize errors: <em> EZ PayPal</em> catches all the usual data entry errors so that you can afford to be a bit sloppy.</li>
    <li>Very little programming knowledge required: <em> EZ PayPal</em> is written for creative people who have some digital products to sell. So it doesn't call for any deep computing knowledge. The most you will have to do is perhaps to set permission to a couple of folders.</li>
    <li>IPN handling: <em> EZ PayPal</em> handles all the complex PayPal instant notification and data transfer to prevent unauthorized access.</li>
    <li>Automatic download pages: Buyers are automatically redirected to a customized download page.</li>
    <li>Self-service download link retrieval: If the PayPal information is not yet received by your server, <em> EZ PayPal</em> shows your buyer a page where he can retrieve his purchase link. (This feature reduced my support load by 90%).</li>
    <li>Automated emails: In addition to the download page, <em> EZ PayPal</em> sends an automated email with the download link to your buyer as well. Just in case...</li>
    <li>Easy Inventory maintenance: Easy to add new products to your inventory.</li>
    <li>Automatic generation of an online shop: Once the first product is added, you can already see it on your <em> EZ PayPal</em> online shop.</li>
    <li>Sandbox mode: <em> EZ PayPal</em> gives you the option to choose PayPal sandbox mode so that you can check your setup before going live.</li>
    <li>Timely help: <em> EZ PayPal</em> sports a context-sensitive help system, so that you get timely help as you need it.</li>
    <li>WordPress integration: This application comes with built-in WordPress integration. It works as a WordPress plugin if uploaded to the <code>wp-content/plugins</code> folder of your blog. What's more, you can switch to the standalone mode from the WordPress plugin admin page of this application, while still using the WordPress authentication mechanism and database.</li>
    </ol>"><i class='glyphicon glyphicon-thumbs-up'></i> Features</h2>


<h2 class="btn btn-primary btn-help" data-content="<p>Since <em> EZ PayPal</em> deals with money, it takes the security and integrity of your data very seriously. It also puts serious roadblocks to prevent unauthorized access to your server.</p>

    <ol>
    <li>All login type actions are implemented in such a way as to virtually eliminate the possibility of SQL injection or SQL-based DoS attacks.</li>
    <li>The set up scripts do not let you set up your installation twice. In fact, they won't even display the setup information the second time you run it. Reinstallation will require database manipulation.</li>
    <li>Only one admin user is permitted. You cannot add another admin user.  You can edit and modify the admin details only after entering the current password.</li>
    <li>There is no interface to recover your password once you install your system. No amount of database hacking will recover it. So please be careful to note it down in some secure location. Password may be reset using one-time emial link though.</li>
    <li>Strong validation of all user entries exposed to the world at the client-side, server-side as well at the database layer.</li>
    </ol>"><i class='glyphicon glyphicon-lock'></i> Security Features</h2>


<h2 class="btn btn-primary btn-help" data-content="<p>In addition to the fully functional Lite version, there is a <a href='http://buy.thulasidas.com/ezpaypal' title='Get EZ PayPal Pro for $19.95' class='goPro'>Pro Version</a> with many more features. These features are highlighted by a red icon in the menus of the lite version.</p>

    <p>If the following features are important to you, consider buying the <em>Pro</em> version.</p>

    <ol>
    <li><em>HTML Emails</em>: In the <em>Pro</em> version, you can send impressive HTML email to your customers rather than the boring plain text messages.</li>
    <li><em>PDT Handling</em>: Payment Data Transfer posts the data to your return page, which the <em>Pro</em> version makes use of, in preparing the return page. This can be critical when there is a delay in PayPal posting the IPN messages to the listener.</li>
    <li><em>Address Handling</em>: The <em>Pro</em> version can optionally store the customer address details in a database table, if you need it.</li>
    <li><em>IPN Relay</em>: If you have other IPN listeners, the <em>Pro</em> version of EZ PayPal can relay the IPN messages to them, so that they receive the same IPN messages, enabling integration with multiple systems.</li>
    <li><em>Template Editor</em>: The email body, thank you page and download display are all editable in the <em>Pro</em> version.</li>
    <li><em>Logo and Branding</em>: The <em>Pro</em> version gives you options to set your own logo on your shop/return pages and even on PayPal checkout page.</li>
    <li><em>Skinnable Shop and Return Pages</em>: In the <em>Pro</em> version, you can select the color schemes of your shop, return and update pages (as well as admin pages) from nine different skins.</li>
    <li><em>Flexible Shop Layout</em>: In the <em>Pro</em> version, you can select either the default table view or a lis/grid view for your e-shop display. The list/grid view is switchable on the frontend as well.</li>
    <li><em>Automatic handling of refunds and disputes</em>: When you issue a refund on the PayPal website, the corresponding sale in your database will be set to inactive. And if a buyer registers a dispute, he (and you) will get a friendly email message stating that the dispute is being reviewed and handled.</li>
    <li><em>E-Check handling</em>: The <em>Pro</em> version recognizes e-check payments and sends a mail to the buyer regarding the delay for the check clearance.</li>
    <li><em>Sales Editor</em>: You can load a single sale or a bunch of sales on to a friendly interface and change their data. For instance, it will let you change the download expiry date and resend a download notification message -- one of the frequent support requests from the buyers.</li>
    <li><em>Email Tools</em>: You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity.</li>
    <li><em>Product Version Support</em>: The <em>Pro</em> version supports versioning of your products. It will keep track of the version sold to your buyers and your current versions. So, if you want to send a product and version specific upgrade notice, you can do it with <em>Pro</em> version.</li>
    </ol> <a href='#' class='btn-help pull-right' data-content='<?php echo $features2; ?>'>Pro Features (Page 2 of 2)</a>"><i class='glyphicon glyphicon-plane'></i> Pro Features (Page 1 of 2)</h2>

<h2 class="btn btn-primary btn-help" data-content="<?php echo $features2; ?>"><i class='glyphicon glyphicon-plane'></i> Pro Features (Page 2 of 2)</h2>
