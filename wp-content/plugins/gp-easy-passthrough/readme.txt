=== Easy Passthrough for Gravity Forms ===
Contributors: travislopes
Tags: entry, chaining, passthrough, abandonment, retention, gravity forms
Requires at least: 4.2
Tested up to: 4.7.2
License: GPL-3.0+
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easily transfer entry values from one Gravity Forms form to another

== Description ==

Easy Passthrough for Gravity Forms lets you populate a form's fields with user entry data from another form. This is an easy way to break up a large amount of fields over multiple forms, increasing user engagement and preventing form abandonment.

To enable Easy Passthrough, add a new Easy Passthrough configuration to the form you want populated, select which form you want to populate from and map the fields between the two forms. You can also use conditional logic to only passthrough certain entry data.

As users visit your site, their form entries are stored in a secure cookie throughout their browsing session. When they load a form, Easy Passthrough checks for available configurations and passes-through available entry data.

Easy Passthrough requires [Gravity Forms](https://gravityforms.com).

= Requirements =

1. [Purchase and install Gravity Forms](https://gravityforms.com)
2. WordPress 4.2+
3. Gravity Forms 1.9.14+

= Support =

If you have any problems, please contact support: https://gravitywiz.com/support/

== Installation ==

1.  Download the zipped file.
1.  Extract and upload the contents of the folder to your /wp-contents/plugins/ folder.
1.  Navigate to the WordPress admin Plugins page and activate the "Easy Passthrough for Gravity Forms" plugin.

== ChangeLog ==

= Version 1.4.3 (2019-05-10) =
- Fix Signatures not passing through.

= Version 1.4.2 (2019-04-25) =
- Fix PHP notice when mapping fields.
- Fix PHP notice when populating form.
- Update form submission process to generate Easy Passthrough Token.

= Version 1.4.1 (2019-02-24) =
- Fix Checkbox choices whose values contain commas not being populated on target form.
- Update field map to exclude "(Selected)" Checkbox option.
- Update field map to use admin field label.

= Version 1.4 (2018-10-31) =
- Add Easy Passthrough Token as registered entry meta for exporting.
- Fix Easy Passthrough running when GravityView is in edit context.
- Fix mapping issue with Name fields.
- Update passthrough engine to be more reliable when matching field values of different field types.

= Version 1.3 (2018-04-30) =
- Add support for defining license key "FG_EASYPASSTHROUGH_LICENSE_KEY" constant.
- Add support for Gravity Forms 2.3.
- Add support for Members version 2.0+.
- Add support for Product Option fields.
- Fix mapping issue with Product fields.
- Fix Signature field not passing through signature image.
- Update license API requests to support upcoming ForGravity bundle.
- Update license key feedback.

= Version 1.2 =
- Add filter to prevent session manager from initializing on page load.
- Add method for retrieving entry token.
- Add payment meta options to entry meta mapping.
- Add setting to use logged in user's last submitted entry for passthrough.
- Add support for passing through entry meta.
- Fix incorrect slug in automatic updater.
- Fix issue when form ID cannot be found.
- Fix issue with field values not passing through when multiple, different forms were embedded on the same page.
- Update source form settings field to show current form by default.

= Version 1.1 (2017-06-22) =
- Add "fg_easypassthrough_cookie_path" filter to modify cookie path.
- Add "fg_easypassthrough_expiration" filter to modify cookie expiration time.
- Add "fg_easypassthrough_field_values" filter to modify field values prepared for Easy Passthrough.
- Add "fg_easypassthrough_form" filter to modify form object after Easy Passthrough has been applied.
- Add "fg_easypassthrough_populate_same_form" filter to allow form to be populated from itself.
- Add plugin capabilities.
- Add populating individual entry via "ep_token" query parameter.
- Add support for mapping Date and Time fields.
- Fix a PHP notice when no passthrough occurs.
- Fix an issue where multiple input fields could not be unmapped for passthrough.
- Fix fatal error with PHP 5.3.
- Fix field labels not appearing correctly in field mapping for multiple input fields.
- Fix no rows appearing in multiple column list fields if an empty value was mapped.
- Fix incorrect apply_filters calls.
- Update cookie name.
- Update "fg_easypassthrough_form" to run when no field values are prepared for passthrough.

= Version 1.0 (2017-02-27) =
- It's all new!
