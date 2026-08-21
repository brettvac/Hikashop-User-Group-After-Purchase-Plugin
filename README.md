# Hikashop User Group After Purchase Plugin

This plugin enables you to change the group of a user after purchase of a product. 

## Description
The Hikashop User Group After Purchase Plugin allows you to automatically add a user to a new group in Joomla after they purchase a specific product in Hikashop Starter or Hikashop Essential. 

For example, you may want to sell access to a digital course that requires enrollment based on a user's group. This plugin allows you to sell access to that course using Hikashop.

## Notes On The Original Source of this Plugin
The code for this plugin originates from this forum post: [https://www.hikashop.com/forum/orders-management/866710-user-group-after-purchase-with-multiple-purchase.html#148692](https://www.hikashop.com/forum/orders-management/866710-user-group-after-purchase-with-multiple-purchase.html#148692). 

### Changes made to the old version 2 plugin
The following changes were made to the old version of the Joomla! plugin, which worked with Joomla! version 1-3 but lacked best practices for Joomla! versions 4 and up.
- Added language strings that ship with versions of Hikashop that don't include the plugin
- Switched to using fully qualified names instead of the JPlugin: `extends \Joomla\CMS\Plugin\CMSPlugin`
- Standardized the backend view to look like other plugins such as [Product Order History](https://www.hikashop.com/marketplace/product/254-product-order-history.html)
- Replaced all JFactory calls with Factory
- Replaced database access with the container-based driver
- Removed all JVERSION conditionals for versions older than 1.6
- Replaced jimport with use statements that appear prior to the class statement
- Modernized the admin check but kept `$mainframe` instead of the more common `$app`
- Switched to Access and modern user handling
