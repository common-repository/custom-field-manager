=== Custom Field Manager ===
Contributors: f1logic
Donate link: https://xyzscripts.com/donate/
Tags: custom fields, custom field, post fields, custom field groups, custom fields for taxonomy terms, sortable custom fields, sortable custom field groups, custom attributes, more fields, extra fields, extra parameters, text field, textarea, dropdown field, numeric field, custom post type fields, page fields, post attributes, page attributes
Requires at least: 3.0
Tested up to: 4.9.4
Stable tag: 1.0
License: GPLv2 or later

Create custom fields and field groups for posts, pages and custom post types.

== Description ==

A quick look into Custom Field Manager :

	★ Custom fields for posts and pages
	★ Custom fields for custom post types
	★ Supports text field, textarea and dropdown
	★ Group fields together under different custom field groups
	★ Create groups for specific taxonomy terms
	★ Drag and drop sorting for fields and field groups
	★ Mandatory check for custom fields
	★ Display fields and values using shortcode


= Custom Field Manager Features in Detail =

The Custom Field Manager lets you create and manage multiple custom fields and custom field groups. It supports custom field elements such as text field, numeric field, textarea and dropdown list. You can create different custom field groups for posts, pages as well as custom post types and under each group you can create multiple fields.

While defining field groups you can define them for a post, page or custom post type in general or specific to any taxonomy term. You can also drag and drop to sort the field groups. You can also sort fields within a field group. You can use shortcodes to display fields and values in front end or set automatic display.

The prominent features of  the custom field manager plugin are highlighted below.

= Supported Custom Field Elements =

    Text field
    Numeric field
    Textarea
    Dropdown List

= Custom Fields  =

    Create multiple custom fields under different field groups
    Option to drag and drop for sorting custom fields within a group
    Option to activate/inactivate custom fields
    Optional mandatory check for custom fields
    Specify default values for custom fields

= Custom Field Groups  =

    Group related custom fields under single group
    Option to drag and drop for sorting custom field groups
    Option to activate/inactivate custom field groups
    Field groups for post, page and custom post types
    Define field groups for specific taxonomy terms

= Custom Field Submission =

    Option to enable mandatory check during custom field submission
    Option to save posts as draft if mandatory fields not filled
    Option to save custom fields with default values if mandatory fields not filled

= Custom Field Display =

    Shortcodes for displaying custom fields and values by passing post id
    Automatic display in single page without using shortcode


= About =

Custom Field Manager is developed and maintained by [XYZScripts](https://xyzscripts.com/ "xyzscripts.com"). For any support, you may [contact us](https://xyzscripts.com/support/ "XYZScripts Support").

★ [Custom Field Manager User Guide](http://help.xyzscripts.com/docs/custom-field-manager/ "Custom Field Manager User Guide")
★ [Custom Field Manager FAQ](http://help.xyzscripts.com/docs/custom-field-manager/faq/ "Custom Field Manager FAQ")

== Installation ==

★ [Custom Field Manager User Guide](http://help.xyzscripts.com/docs/custom-field-manager/ "Custom Field Manager User Guide")
★ [Custom Field Manager FAQ](http://help.xyzscripts.com/docs/custom-field-manager/faq/ "Custom Field Manager FAQ")

1. Extract `custom-field-manager.zip` to your `/wp-content/plugins/` directory.
2. In the admin panel under plugins activate Custom Field Manager.
3. You can configure the basic settings from `Custom Fields > Settings` menu.
4. Once settings are done, you may create custom fields as required

If you need any further help, you may contact our [support desk](https://xyzscripts.com/support/ "XYZScripts Support").

== Frequently Asked Questions ==

★ [Custom Field Manager User Guide](http://help.xyzscripts.com/docs/custom-field-manager/ "Custom Field Manager User Guide")
★ [Custom Field Manager FAQ](http://help.xyzscripts.com/docs/custom-field-manager/faq/ "Custom Field Manager FAQ")


= The Custom Field Manager is not working properly. =

Please check the wordpress version you are using. Make sure it meets the minimum version recommended by us. Make sure all files of the `custom-field-manager` plugin are uploaded to the folder `wp-content/plugins/`

= Can I create custom fields without creating field group ? =

No, you have to create a field group before creating fields.

= Can I create custom fields for a specific term of a taxonomy which is associated to post or post type ? =

Yes, you can. In the `Add Field Group` section choose post/page/post type, then select an associated taxonomy, then select desired term and then create  a field group. Under this group, you can create desired fields.

= Can I create custom fields for post/post type without associating  to a taxonomy ? =

Yes, of course. In the `Add Field Group` section choose post/page/post type, then leave the taxonomy as `select` and then create a field group. Under this group, you can create desired fields.

= Can I create custom fields or groups which are common to posts, pages and custom post types ? =

No, you have to create separate field groups and fields for posts and pages and custom post types

= How can I display the custom field groups and custom fields in my website front end ? =

You can either enable automatic display in the settings or use below shortcode in your post/page/custom post content.
`[xyz_cfl_shortcode id="{POST_ID}"]` Here {POST_ID} must be replaced by actual id of post/page/custom post.

=  Can I embed the custom field display into my template file ? =

Yes, you can embed the custom field display into your template file. You can either enable automatic display in the settings or use do_shortcode() function like this:
`echo do_shortcode( '[xyz_cfl_shortcode id="{POST_ID}"]' );` //here {POST_ID} must be replaced by actual id of post/page/custom post.


More questions ? [Drop a mail](https://xyzscripts.com/members/support/ "XYZScripts Support") and we shall get back to you with the answers.


== Screenshots ==

1. This is the settings page.
2. This is the custom field management page.

== Changelog ==

= Custom Field Manager 1.0 =
* First official launch.

== Upgrade Notice ==


== More Information ==

★ [Custom Field Manager User Guide](http://help.xyzscripts.com/docs/custom-field-manager/ "Custom Field Manager User Guide")
★ [Custom Field Manager FAQ](http://help.xyzscripts.com/docs/custom-field-manager/faq/ "Custom Field Manager FAQ")

= Troubleshooting =

Please read the FAQ first if you are having problems.

= Requirements =

    Wordpress 3.0+
    PHP 5+

= Feedback =

We would like to receive your feedback and suggestions about Custom Field Manager plugin. You may submit them at our [support desk](https://xyzscripts.com/members/support/ "XYZScripts Support").
