<?php

class WordPress_GDPR_Install_Pages extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Store Locator Plugin Construct
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name
     * @param   string                         $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    /**
     * Init the Public
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        if (!$this->get_option('enable')) {
            return false;
        }

        return true;
    }

    public function check_action()
    {
    	if(!isset($_GET['wordpress_gdpr']) || !is_admin()) {
    		return false;
		}

		if(!isset($_GET['wordpress_gdpr']['install-pages'])) {
			return false;
		}

        $options = get_option('wordpress_gdpr_options');

        $pages = array(
            'privacyCenter' => array (
                'post_title'    => __('Privacy Center', 'wordpress-gdpr'),
                'post_content'  => '[wordpress_gdpr_privacy_center]',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'privacyCenter',
            ),
            'contactDPO'    => array (
                'post_title'    => __('Contact DPO', 'wordpress-gdpr'),
                'post_content'  => '[wordpress_gdpr_contact_dpo]',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'contactDPO',
            ),
            'cookiePolicy'  => array (
                'post_title'    => __('Cookie Policy', 'wordpress-gdpr'),
                'post_content'  => '<h2>What is a Cookie?</h2>
Cookies are small text files, created by the website visited, that contain data. They are stored on the visitor’s computer to give the user access to various functions. Both session cookies and non-session cookies are used on this website (the “Site”). A session cookie is temporarily stored in the computer memory while the visitor is browsing the website. This cookie is erased when the user closes their web browser or after a certain time has passed (meaning that the session expires). A non-session cookie remains on the visitor’s computer until it is deleted.
<h2>Why do we use Cookies?</h2>
We use cookies to learn more about the way visitors interact with our content and help us to improve the experience when visiting our Site.
<h2>Site Functionality</h2>
The share function is used by visitors to recommend our Site and content on social networks such as Facebook and Twitter. Cookies store information on how visitors use the share function – although not at an individual level – so that the Site can be improved. If you do not accept cookies, no information is stored.

For some of the functions within our Site we use third party suppliers, for example, when you visit a page with videos embedded from or links to YouTube. These videos or links (and any other content from third party suppliers) may contain third party cookies and you may wish to consult the policies of these third party websites for information regarding their use of cookies.
<h2>Cookies we Use:</h2>
This Site uses Google Analytics which use cookies. At the aggregate level, cookies store information on how visitors use the Site, including the number of pages displayed, where the visitor comes from, and the number of visits, to improve the website and ensure a good user experience. If you do not accept cookies, no information is stored.
<h2>How to reject Cookies?</h2>
We will not use cookies to collect personally identifiable information about a visitor.

However you can choose to reject or block the cookies set by {yourwebsite} by changing your browser settings – see the “Help function” within your browser for further details. Please note that most browsers automatically accept cookies so if you do not wish cookies to be used, you may need to actively delete or block the cookies.

For information on the use of cookies in mobile phone browsers and for details on how to reject or delete such cookies, please refer to your mobile phone manual.',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'cookiePolicy',
            ),
            'DMCA'  => array (
                'post_title'    => __('Contact DMCA', 'wordpress-gdpr'),
                'post_content'  => 'Contact DMCA Form needs to be created.',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'DMCA',
            ),
            'dataRectification'    => array (
                'post_title'    => __('Data Rectification', 'wordpress-gdpr'),
                'post_content'  => '[wordpress_gdpr_data_rectification]',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'dataRectification',
            ),
            'disclaimer'    => array (
                'post_title'    => __('Disclaimer', 'wordpress-gdpr'),
                'post_content'  => 'Put your Disclaimer here.',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'disclaimer',
            ),
            'forgetMe'  => array (
                'post_title'    => __('Forget Me', 'wordpress-gdpr'),
                'post_content'  => '[wordpress_gdpr_forget_me]',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'forgetMe',
            ),
            'imprint'   => array (
                'post_title'    => __('Imprint', 'wordpress-gdpr'),
                'post_content'  => 'Put your Imprint here',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'imprint',
            ),
            'dataRectification'    => array (
                'post_title'    => __('Data Rectification', 'wordpress-gdpr'),
                'post_content'  => '[wordpress_gdpr_data_rectification]',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'dataRectification',
            ),
            'mediaCredits'    => array (
                'post_title'    => __('Media Credits', 'wordpress-gdpr'),
                'post_content'  => 'Put your Media credits here.',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'mediaCredits',
            ),
            'requestData'   => array (
                'post_title'    => __('Request Data', 'wordpress-gdpr'),
                'post_content'  => '[wordpress_gdpr_request_data]',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'requestData',
            ),
            'privacyPolicy' => array (
                'post_title'    => __('Privacy Policy', 'wordpress-gdpr'),
                'post_content'  => '<h2>Who we are</h2>
Our website address is: {url}.
<h2>What personal data we collect and why we collect it</h2>
<h3>Comments</h3>
When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor’s IP address and browser user agent string to help spam detection.

An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.
<h3>Media</h3>
If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.
<h3>Contact forms</h3>
<h3>Cookies</h3>
If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.

If you have an account and you log in to this site, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.

When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select "Remember Me", your login will persist for two weeks. If you log out of your account, the login cookies will be removed.

If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.
<h3>Embedded content from other websites</h3>
Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.

These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracing your interaction with the embedded content if you have an account and are logged in to that website.
<h3>Analytics</h3>
<h2>Who we share your data with</h2>
<h2>How long we retain your data</h2>
If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.

For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.
<h2>What rights you have over your data</h2>
If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.
<h2>Where we send your data</h2>
Visitor comments may be checked through an automated spam detection service.
<h2>Your contact information</h2>
<h2>Additional information</h2>
<h3>How we protect your data</h3>
<h3>What data breach procedures we have in place</h3>
<h3>What third parties we receive data from</h3>
<h3>What automated decision making and/or profiling we do with user data</h3>
<h3>Industry regulatory disclosure requirements</h3>',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'privacyPolicy',
            ),
            // 'privacySettings'   => array (
            //     'post_title'    => __('Privacy Settings', 'wordpress-gdpr'),
            //     'post_content'  => '[wordpress_gdpr_privacy_settings]',
            //     'post_type' => 'page',
            //     'post_status'   => 'publish',
            //     'option'    => 'privacySettings',
            // ),
            'termsConditions'   => array (
                'post_title'    => __('Terms And Conditions', 'wordpress-gdpr'),
                'post_content'  => 'The following is a WooCommerce Terms and Conditions Example.

Search for and replace the following Terms and Conditions placeholders:

{your company}
{your email}
{your address}
<h2>Overview</h2>
This website is operated by {your company}. Throughout the site, the terms “we”, “us” and “our” refer to {your company}. {your company} offers this website, including all information, tools and services available from this site to you, the user, conditioned upon your acceptance of all terms, conditions, policies and notices stated here.

By visiting our site and/ or purchasing something from us, you engage in our “Service” and agree to be bound by the following terms and conditions (“Terms and Conditions”, “Terms”), including those additional terms and conditions and policies referenced herein and/or available by hyperlink. These Terms and Conditions apply to all users of the site, including without limitation users who are browsers, vendors, customers, merchants, and/ or contributors of content.

Please read these Terms and Conditions carefully before accessing or using our website. By accessing or using any part of the site, you agree to be bound by these Terms and Conditions. If you do not agree to all the terms and conditions of this agreement, then you may not access the website or use any services. If these Terms and Conditions are considered an offer, acceptance is expressly limited to these Terms and Conditions.

Any new features or tools which are added to the current store shall also be subject to the Terms and Conditions. You can review the most current version of the Terms and Conditions at any time on this page. We reserve the right to update, change or replace any part of these Terms and Conditions by posting updates and/or changes to our website. It is your responsibility to check this page periodically for changes. Your continued use of or access to the website following the posting of any changes constitutes acceptance of those changes.

Our store is hosted on Shopify Inc. They provide us with the online e-commerce platform that allows us to sell our products and services to you.
<h2>Online Store Terms</h2>
By agreeing to these Terms and Conditions, you represent that you are at least the age of majority in your state or province of residence, or that you are the age of majority in your state or province of residence and you have given us your consent to allow any of your minor dependents to use this site.

You may not use our products for any illegal or unauthorized purpose nor may you, in the use of the Service, violate any laws in your jurisdiction (including but not limited to copyright laws).

You must not transmit any worms or viruses or any code of a destructive nature.

A breach or violation of any of the Terms will result in an immediate termination of your Services.
<h2>General Conditions</h2>
We reserve the right to refuse service to anyone for any reason at any time.

You understand that your content (not including credit card information), may be transferred unencrypted and involve (a) transmissions over various networks; and (b) changes to conform and adapt to technical requirements of connecting networks or devices. Credit card information is always encrypted during transfer over networks.

You agree not to reproduce, duplicate, copy, sell, resell or exploit any portion of the Service, use of the Service, or access to the Service or any contact on the website through which the service is provided, without express written permission by us.

The headings used in this agreement are included for convenience only and will not limit or otherwise affect these Terms.
<h2>Accuracy, Completeness And Timeliness Of Information</h2>
We are not responsible if information made available on this site is not accurate, complete or current. The material on this site is provided for general information only and should not be relied upon or used as the sole basis for making decisions without consulting primary, more accurate, more complete or more timely sources of information. Any reliance on the material on this site is at your own risk.

This site may contain certain historical information. Historical information, necessarily, is not current and is provided for your reference only. We reserve the right to modify the contents of this site at any time, but we have no obligation to update any information on our site. You agree that it is your responsibility to monitor changes to our site.
<h2>Modifications To The Service And Prices</h2>
Prices for our products are subject to change without notice.

We reserve the right at any time to modify or discontinue the Service (or any part or content thereof) without notice at any time.

We shall not be liable to you or to any third-party for any modification, price change, suspension or discontinuance of the Service.
<h2>Products Or Services</h2>
Certain products or services may be available exclusively online through the website. These products or services may have limited quantities and are subject to return or exchange only according to our Return Policy.

We have made every effort to display as accurately as possible the colors and images of our products that appear at the store. We cannot guarantee that your computer monitor’s display of any color will be accurate.

We reserve the right, but are not obligated, to limit the sales of our products or Services to any person, geographic region or jurisdiction. We may exercise this right on a case-by-case basis. We reserve the right to limit the quantities of any products or services that we offer. All descriptions of products or product pricing are subject to change at anytime without notice, at the sole discretion of us. We reserve the right to discontinue any product at any time. Any offer for any product or service made on this site is void where prohibited.

We do not warrant that the quality of any products, services, information, or other material purchased or obtained by you will meet your expectations, or that any errors in the Service will be corrected.
<h2>Accuracy Of Billing And Account Information</h2>
We reserve the right to refuse any order you place with us. We may, in our sole discretion, limit or cancel quantities purchased per person, per household or per order. These restrictions may include orders placed by or under the same customer account, the same credit card, and/or orders that use the same billing and/or shipping address. In the event that we make a change to or cancel an order, we may attempt to notify you by contacting the e-mail and/or billing address/phone number provided at the time the order was made. We reserve the right to limit or prohibit orders that, in our sole judgment, appear to be placed by dealers, resellers or distributors.

You agree to provide current, complete and accurate purchase and account information for all purchases made at our store. You agree to promptly update your account and other information, including your email address and credit card numbers and expiration dates, so that we can complete your transactions and contact you as needed.

For more detail, please review our Returns Policy.
<h2>Optional Tools</h2>
We may provide you with access to third-party tools over which we neither monitor nor have any control nor input.

You acknowledge and agree that we provide access to such tools ”as is” and “as available” without any warranties, representations or conditions of any kind and without any endorsement. We shall have no liability whatsoever arising from or relating to your use of optional third-party tools.

Any use by you of optional tools offered through the site is entirely at your own risk and discretion and you should ensure that you are familiar with and approve of the terms on which tools are provided by the relevant third-party provider(s).

We may also, in the future, offer new services and/or features through the website (including, the release of new tools and resources). Such new features and/or services shall also be subject to these Terms and Conditions.
<h2>Third-Party Links</h2>
Certain content, products and services available via our Service may include materials from third-parties.

Third-party links on this site may direct you to third-party websites that are not affiliated with us. We are not responsible for examining or evaluating the content or accuracy and we do not warrant and will not have any liability or responsibility for any third-party materials or websites, or for any other materials, products, or services of third-parties.

We are not liable for any harm or damages related to the purchase or use of goods, services, resources, content, or any other transactions made in connection with any third-party websites. Please review carefully the third-party’s policies and practices and make sure you understand them before you engage in any transaction. Complaints, claims, concerns, or questions regarding third-party products should be directed to the third-party.
<h2>User Comments, Feedback And Other Submissions</h2>
If, at our request, you send certain specific submissions (for example contest entries) or without a request from us you send creative ideas, suggestions, proposals, plans, or other materials, whether online, by email, by postal mail, or otherwise (collectively, ‘comments’), you agree that we may, at any time, without restriction, edit, copy, publish, distribute, translate and otherwise use in any medium any comments that you forward to us. We are and shall be under no obligation (1) to maintain any comments in confidence; (2) to pay compensation for any comments; or (3) to respond to any comments.

We may, but have no obligation to, monitor, edit or remove content that we determine in our sole discretion are unlawful, offensive, threatening, libelous, defamatory, pornographic, obscene or otherwise objectionable or violates any party’s intellectual property or these Terms and Conditions.

You agree that your comments will not violate any right of any third-party, including copyright, trademark, privacy, personality or other personal or proprietary right. You further agree that your comments will not contain libelous or otherwise unlawful, abusive or obscene material, or contain any computer virus or other malware that could in any way affect the operation of the Service or any related website. You may not use a false e-mail address, pretend to be someone other than yourself, or otherwise mislead us or third-parties as to the origin of any comments. You are solely responsible for any comments you make and their accuracy. We take no responsibility and assume no liability for any comments posted by you or any third-party.
<h2>Personal Information</h2>
Your submission of personal information through the store is governed by our Privacy Policy. To view our Privacy Policy.
<h2>Errors, Inaccuracies And Omissions</h2>
Occasionally there may be information on our site or in the Service that contains typographical errors, inaccuracies or omissions that may relate to product descriptions, pricing, promotions, offers, product shipping charges, transit times and availability. We reserve the right to correct any errors, inaccuracies or omissions, and to change or update information or cancel orders if any information in the Service or on any related website is inaccurate at any time without prior notice (including after you have submitted your order).

We undertake no obligation to update, amend or clarify information in the Service or on any related website, including without limitation, pricing information, except as required by law. No specified update or refresh date applied in the Service or on any related website, should be taken to indicate that all information in the Service or on any related website has been modified or updated.
<h2>Prohibited Uses</h2>
In addition to other prohibitions as set forth in the Terms and Conditions, you are prohibited from using the site or its content: (a) for any unlawful purpose; (b) to solicit others to perform or participate in any unlawful acts; (c) to violate any international, federal, provincial or state regulations, rules, laws, or local ordinances; (d) to infringe upon or violate our intellectual property rights or the intellectual property rights of others; (e) to harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate based on gender, sexual orientation, religion, ethnicity, race, age, national origin, or disability; (f) to submit false or misleading information; (g) to upload or transmit viruses or any other type of malicious code that will or may be used in any way that will affect the functionality or operation of the Service or of any related website, other websites, or the Internet; (h) to collect or track the personal information of others; (i) to spam, phish, pharm, pretext, spider, crawl, or scrape; (j) for any obscene or immoral purpose; or (k) to interfere with or circumvent the security features of the Service or any related website, other websites, or the Internet. We reserve the right to terminate your use of the Service or any related website for violating any of the prohibited uses.
<h2>Disclaimer Of Warranties; Limitation Of Liability</h2>
We do not guarantee, represent or warrant that your use of our service will be uninterrupted, timely, secure or error-free.

We do not warrant that the results that may be obtained from the use of the service will be accurate or reliable.

You agree that from time to time we may remove the service for indefinite periods of time or cancel the service at any time, without notice to you.

You expressly agree that your use of, or inability to use, the service is at your sole risk. The service and all products and services delivered to you through the service are (except as expressly stated by us) provided ‘as is’ and ‘as available’ for your use, without any representation, warranties or conditions of any kind, either express or implied, including all implied warranties or conditions of merchantability, merchantable quality, fitness for a particular purpose, durability, title, and non-infringement.

In no case shall {your company}, our directors, officers, employees, affiliates, agents, contractors, interns, suppliers, service providers or licensors be liable for any injury, loss, claim, or any direct, indirect, incidental, punitive, special, or consequential damages of any kind, including, without limitation lost profits, lost revenue, lost savings, loss of data, replacement costs, or any similar damages, whether based in contract, tort (including negligence), strict liability or otherwise, arising from your use of any of the service or any products procured using the service, or for any other claim related in any way to your use of the service or any product, including, but not limited to, any errors or omissions in any content, or any loss or damage of any kind incurred as a result of the use of the service or any content (or product) posted, transmitted, or otherwise made available via the service, even if advised of their possibility. Because some states or jurisdictions do not allow the exclusion or the limitation of liability for consequential or incidental damages, in such states or jurisdictions, our liability shall be limited to the maximum extent permitted by law.
<h2>Indemnification</h2>
You agree to indemnify, defend and hold harmless {your company} and our parent, subsidiaries, affiliates, partners, officers, directors, agents, contractors, licensors, service providers, subcontractors, suppliers, interns and employees, harmless from any claim or demand, including reasonable attorneys’ fees, made by any third-party due to or arising out of your breach of these Terms and Conditions or the documents they incorporate by reference, or your violation of any law or the rights of a third-party.
<h2>Severability</h2>
In the event that any provision of these Terms and Conditions is determined to be unlawful, void or unenforceable, such provision shall nonetheless be enforceable to the fullest extent permitted by applicable law, and the unenforceable portion shall be deemed to be severed from these Terms and Conditions, such determination shall not affect the validity and enforceability of any other remaining provisions.
<h2>Termination</h2>
The obligations and liabilities of the parties incurred prior to the termination date shall survive the termination of this agreement for all purposes.

These Terms and Conditions are effective unless and until terminated by either you or us. You may terminate these Terms and Conditions at any time by notifying us that you no longer wish to use our Services, or when you cease using our site.

If in our sole judgment you fail, or we suspect that you have failed, to comply with any term or provision of these Terms and Conditions, we also may terminate this agreement at any time without notice and you will remain liable for all amounts due up to and including the date of termination; and/or accordingly may deny you access to our Services (or any part thereof).
<h2>Entire Agreement</h2>
The failure of us to exercise or enforce any right or provision of these Terms and Conditions shall not constitute a waiver of such right or provision.

These Terms and Conditions and any policies or operating rules posted by us on this site or in respect to The Service constitutes the entire agreement and understanding between you and us and govern your use of the Service, superseding any prior or contemporaneous agreements, communications and proposals, whether oral or written, between you and us (including, but not limited to, any prior versions of the Terms and Conditions).

Any ambiguities in the interpretation of these Terms and Conditions shall not be construed against the drafting party.
<h2>Governing Law</h2>
These Terms and Conditions and any separate agreements whereby we provide you Services shall be governed by and construed in accordance with the laws of {your address}.
<h2>Changes To Terms and Conditions</h2>
You can review the most current version of the Terms and Conditions at any time at this page.

We reserve the right, at our sole discretion, to update, change or replace any part of these Terms and Conditions by posting updates and changes to our website. It is your responsibility to check our website periodically for changes. Your continued use of or access to our website or the Service following the posting of any changes to these Terms and Conditions constitutes acceptance of those changes.
<h2>Contact Information</h2>
Questions about the Terms and Conditions should be sent to us at {your-email}.',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'termsConditions',
            ),
            'unsubscribe'   => array (
                'post_title'    => __('Unscribe', 'wordpress-gdpr'),
                'post_content'  => 'Put your Unsubsribe Form here.',
                'post_type' => 'page',
                'post_status'   => 'publish',
                'option'    => 'unsubscribe',
            ),
        );

        $first = true;
        $post_parent = 0;

        foreach ($pages as $page) {

            $pageOption = $page['option'] . 'Page';
            $pageEnable = $page['option'] . 'Enable';

            if(!$first) {
                $page['post_parent'] = $post_parent;
            }

            if($first && !empty($options[$pageOption])) {
                $post_parent = $options[$pageOption];
                $first = false;
            }

            if(!empty($options[$pageOption])) {
                continue;
            }

            $page_inserted = wp_insert_post($page);
            if($first && empty($options[$pageOption])) {
                $post_parent = $page_inserted;
                $first = false;
            }

            $options[$pageOption] = $page_inserted;
            $options[$pageEnable] = '1';
        }

        $this->delete_transient();

        update_option('wordpress_gdpr_options', $options);	
		wp_redirect( get_admin_url() . 'edit.php?post_type=page' );
    }

    public function delete_transient()
    {
        delete_transient( 'wordpress_gdpr_pages');
    }
}