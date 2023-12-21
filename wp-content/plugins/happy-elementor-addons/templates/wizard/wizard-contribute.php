<div class="inner-content">
    <h2 class="title-big color-purple">Be our proud contributor!</h2>
    <div class="details">Are you interested in contributing to making this plugin more awesome?</div>
    
    <div class="consent-terms">
        <div disabled>
            <p>You can easily contribute by sharing non-sensitive diagnostic data and usage information to make sure optimum compatibility. You’ll be sharing - Server environment details (PHP, MySQL, server, WordPress versions), Number of users on your site, Site language, Number of active and inactive plugins, Site name and URL, Your name, and email address.</p>
            <p>We are using Appsero to collect these data. Learn more about how <a href="https://appsero.com/privacy-policy/">Appsero collects and handles your data</a>.</p>
            <p>Additionally we also collect plugin, widget, extension usage and other installed plugin info. This data is never sold to a third party or shared with Appsero. This analytics is used to enhance and improve the user experience and R&D new features. Additionally, read weDevs <a href="https://happyaddons.com/privacy-policy/">privacy policy</a> for better knowledge on it.</p>
        </div>
    </div>
    <div class="consent-check">
        <div class="ha-dashboard-widgets__item-toggle ha-toggle">
            <input 
            type="checkbox"
            class="ha-toggle__check ha-widget" 
            v-model="hasConsent"
            >
            <b class="ha-toggle__switch"></b>
            <b class="ha-toggle__track"></b>
        </div>
        <span v-if="hasConsent">I'd like to contribute</span>
        <span v-else>I don't want to contribute</span>
    </div>
    <ha-nav
    prev="bepro"
    next="congrats"
    done=""
    @set-tab="setTab"
    ></ha-nav>
</div>