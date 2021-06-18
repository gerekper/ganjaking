<div class="upadmin-highlight">

<h4>Can I seperate from login redirects and register redirects? Can I have a user redirected after registration based on custom field (say City for example)?</h4>

Yes. The plugin has two seperate redirects modes, login redirects and registration redirects.

<h4>How can I redirect a specific user?</h4>

<ol>
<li>Open <strong>UserPro > Advanced Redirection</strong></li>
<li>Make a new rule by <strong>select the user you want to redirect from users list</strong></li>
<li>In the redirection URL, enter the URL that you would like that user to view after his access.</li>
</ol>

<h4>How can I redirect all my users to their specific page after login or registration?</h4>

<ol>
<li>Open <strong>UserPro > Advanced Redirection</strong></li>
<li>Make a new rule by <strong>Map All Users</strong></li>
<li>In the redirection URL, use the wildcard {username} to auto-map logged in user to their specific page: e.g. <code>http://mydomain.com/{username}/</code></li>
<li>The redirection URL can be any page or URL (It does not have to contain any wildcards {*}.</li>
</ol>

That will result in: when user1 logs in he will be taken to <code>http://mydomain.com/user1/</code> and so on.

<h4>How can I redirect all users who choose their country as United States to a custom page (example)</h4>

<ol>
<li>Open <strong>UserPro > Advanced Redirection</strong></li>
<li>Make a new rule by <strong>choosing your custom field from the list</strong> (this can be any custom field, not just country)</li>
<li>Since you want to redirect users who live in United States only, put <strong>United States</strong> in front of <code>If you want redirection based on specific custom field value enter the value here</code></li>
<li>Finally in the redirection URL, enter the URL that you want users to get redirected to if they choose United States as their country</li>
</ol>

That will redirect all users who select <strong>United States</strong> to your custom page.

<h4>How to redirect all users who have a specific role?</h4>

<ol>
<li>Open <strong>UserPro > Advanced Redirection</strong></li>
<li>Make a new rule by <strong>choosing a Role</strong></li>
<li>Enter the redirection URL (where all users who have this role be redirected)</li>
</ol>

</div>