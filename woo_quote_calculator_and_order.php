<?php
/**
 * Plugin Name: Woocommerce Quote Calculator
 * Plugin URI: https://www.mansurahamed.com/
 * Description: Woocommerce Quote Calculator Makes simple responsive quote form and calculate quote result for customers of woocommerce. It Produces number of products they need to buy dynamically based on the user input and quote formula. Customer can buy all the products generated from a quote with a single click. They can request a quote , get result and order the products all in one page, just using simple shortcode in post/page or widgets. 
 * Version: 1.1
 * Author: Mansur Ahamed
 * Author URI: https://www.mansurahamed.com/
 * License: GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
 
	require_once('classes/WooQcoAdmin.php');
	require_once('classes/WooQcoModel.php');
	require_once('classes/WooQcoQuote.php');
	require_once('classes/WooQcoProducts.php');
	require_once('classes/WooQcoFormula.php');
	require_once('classes/WooQcoShortcode.php');
	require_once('classes/WooQcoHelp.php');

	WooQcoModel::checkIfPluginTableExists(); //Checking for database table for storing plugin data
	add_action('admin_menu', 'pluginMenu'); //menu
	
	function pluginMenu() { // ADMIN MENU PAGE REGISTER
		add_menu_page('Woo Quote Calculator and Order', 'Woo Quotes', 'edit_themes', 'woo-qco', array('WooQcoAdmin','quotesManager'),plugins_url('images/icon.png', __FILE__),30);
		add_submenu_page('woo-qco', 'Add Products For Quote Result', 'Add New', 'edit_themes','WooQco-newquote',array('WooQcoAdmin','addEditWooQco') );
		add_submenu_page('woo-qco', 'Export Quotes', 'Export', 'edit_themes', 'WooQco-export',  array('WooQcoAdmin','export') );
		add_submenu_page('woo-qco', 'Import Quotes', 'Import', 'edit_themes', 'WooQco-import',  array('WooQcoAdmin','import') );
 	 	add_submenu_page('woo-qco', 'Woo Quote Calculator & Order : Help', 'Help', 'edit_themes', 'WooQco-help',  array('WooQcoHelp','help') );
	}  // END OF METHOD

?>