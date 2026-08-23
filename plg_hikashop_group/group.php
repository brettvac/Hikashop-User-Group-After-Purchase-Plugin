<?php
/**
 * @package	HikaShop for Joomla!
 * @subpackage Hikashop User Group After Purchase Plugin
 * @author hikashop.com, Brett Vachon
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. Modified 2025-2026 by Brett Vachon.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\Access\Access;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;

?><?php
class plgHikashopGroup extends hikashopPlugin {
   
  public function __construct(&$subject, $config) {
    parent::__construct($subject, $config);
    $lang = Factory::getLanguage();
		$lang->load('plg_hikashop_group', JPATH_ADMINISTRATOR);
  }

	/**
	 *
	 * @param object $product
	 * @param array $html
	 */
  function onProductBlocksDisplay(&$product, &$html) {
    if (empty($product->product_id)) {
      return;
    }

    $subscriptiontype = hikashop_get('type.subscription');

    if (empty($subscriptiontype)) {
      return;
    }

    $ret = '
    <div class="hkc-xl-4 hkc-lg-4 hikashop_product_block">
      <div class="hikashop_product_part_title">
        '.Text::_('USER_GROUP_AFTER_PURCHASE').'
      </div>
      <div class="hikashop_product_part">'.
        $subscriptiontype->display('product_group_after_purchase',@$product->product_group_after_purchase,'product').'
      </div>
    </div>';

    $html[] = $ret;
  }
    
  public function onAfterOrderCreate(&$order, &$send_email) {
    return $this->onAfterOrderUpdate($order, $send_email);
  }

  function onAfterOrderUpdate(&$order,&$send_email){
    $config=&hikashop_config();
    $confirmed = $config->get('order_confirmed_status');
    if(!isset($order->order_status)) return true;

    $mainframe = Factory::getApplication();
    $db = Factory::getContainer()->get(DatabaseDriver::class);

    $class = hikashop_get('class.order');
    $dbOrder = $class->get($order->order_id);
    $class = hikashop_get('class.user');
    $data = $class->get($dbOrder->order_user_id);

    if(empty($data->user_cms_id)){
        if($mainframe->isClient('administrator')){
        hikashop_writeToLog(Text::sprintf('PLG_HIKASHOP_GROUP_CUSTOMER_NO_JOOMLA_ACCOUNT', $dbOrder->order_user_id), 'notice');    
        }
        return true;
    }

    $db->setQuery('SELECT b.*,a.* FROM `#__hikashop_order_product` as a LEFT JOIN `#__hikashop_product` as b ON a.product_id=b.product_id WHERE a.order_id = '.(int) $dbOrder->order_id.' AND b.product_group_after_purchase!=\'\'');
    $allProducts = $db->loadObjectList();

    if(empty($allProducts)){
        return true;
    }

    if($order->order_status!=$confirmed){
        return true;
    }

    $userGroups = Access::getGroupsByUser($data->user_cms_id, true);

    $userfactory = Factory::getContainer()->get(UserFactoryInterface::class);
    $user = clone($userfactory->loadUserById($data->user_cms_id));

    $no_change=true;
    foreach($allProducts as $oneProduct){
        if(hikashop_isAllowed($oneProduct->product_group_after_purchase,$data->user_cms_id)){
            continue;
        }
        $no_change=false;

        $userGroups[] = $oneProduct->product_group_after_purchase;

        if($mainframe->isClient('administrator')){
            hikashop_writeToLog(Text::sprintf('PLG_HIKASHOP_GROUP_USER_ADDED_TO_GROUP', $user->username, Access::getGroupTitle($oneProduct->product_group_after_purchase)));
        }
    }

    if(!$no_change){
        $user->set('groups',$userGroups);
        $user->save();
    }

    if($no_change){
        if($mainframe->isClient('administrator')){
            $groupName = '';

            foreach($allProducts as $oneProduct){
                if(hikashop_isAllowed($oneProduct->product_group_after_purchase, $data->user_cms_id)){
                    $groupName = Access::getGroupTitle($oneProduct->product_group_after_purchase);
                    break;
                }
            }

            hikashop_writeToLog(Text::sprintf('PLG_HIKASHOP_GROUP_CUSTOMER_ALREADY_IN_USER_GROUP',$data->name,$groupName));
        }
        return true;
    }
        
    else{
        // $pluginsClass = hikashop_get('class.plugins');
        // $plugin = $pluginsClass->getByName('hikashop','group');
        $force_logout = $this->params->get('force_logout');
        if( empty($force_logout) ){
            return true;
        }
        $conf = Factory::getConfig();
        $handler = $conf->get('session_handler', 'none');
        if($handler=='database'){
            $db->setQuery('DELETE FROM '.hikashop_table('session',false).' WHERE client_id=0 AND userid = '.(int)$data->user_cms_id);
            $db->execute();
        }
        if(!$mainframe->isClient('administrator')){
            $mainframe->logout( $data->user_cms_id );
        }
    }
  }

  public function _updateGroup($user_id, $new_group_id, $remove_group_id = 0) {
    $container = Factory::getContainer();
    $userFactory = $container->get(UserFactoryInterface::class);
    $user = $userFactory->loadUserById($user_id);

    if (!$user || !$user->id) {
      return false; // User not found
    }

    $userGroups = $user->groups;

    // Add new group if not already present
    if (!in_array($new_group_id, $userGroups)) {
      $userGroups[] = $new_group_id;
    }

    // Remove old group if needed
    if (!empty($remove_group_id)) {
      $key = array_search($remove_group_id, $userGroups);
      if ($key !== false) {
        unset($userGroups[$key]);
      }
    }
    $user->groups = $userGroups; // Update user groups
    return $user->save(true);
  }

}