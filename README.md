# Hikashop Group Plugin

This plugin enables you to change the group of a user after purchasing a product in Hikashop Starter. 

Works with Joomla! 4 and is backward compatible with Joomla! 5. I believe this plugin ships with the Business edition.

## Description
The Hikashop Group Plugin allows you to automatically add a user to a new group in Joomla after they purchase a specific product in Hikashop Starter. This is useful for managing user permissions and access based on their purchases.

## Features
- Changes user group after product purchase
- Configurable option to force user logout on group update
- Works with Joomla session handler set to database mode

## Configuration Options
- **Parameter:** `force_logout`  
  - **Type:** Radio  
  - **Default:** 1 (Yes)  
  - **Description:** Force user logout on group update (only with Joomla session handler option set to use the database).  
  - **Options:**  
    - 0: No  
    - 1: Yes  

## Installation
1. Go to System > Install and choose Extensions
2. Choose Install from URL and use: [https://github.com/brettvac/hikashop-user-group/archive/refs/heads/main.zip](https://github.com/brettvac/hikashop-user-group/archive/refs/heads/main.zip)
4. Enable the plugin
5. Choose the user group after purchase in the product options

## Files
- `group.php` - Main plugin file
- `group.xml` - Manifest file

###Original source of this plugin
The code for this plugin originates from this forum post: [https://www.hikashop.com/forum/orders-management/866710-user-group-after-purchase-with-multiple-purchase.html#148692](https://www.hikashop.com/forum/orders-management/866710-user-group-after-purchase-with-multiple-purchase.html#148692). 
I simply asked ChatGPT to update the parts that weren't working in Joomla! 4.
