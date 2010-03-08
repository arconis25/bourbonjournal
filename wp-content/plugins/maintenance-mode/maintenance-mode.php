<?php
/*
Plugin Name: Maintenance Mode
Plugin URI: http://sw-guide.de/wordpress/plugins/maintenance-mode/
Description: Adds a splash page to your blog that lets visitors know your blog is down for maintenance. Logged in administrators get full access to the blog including the front-end. Navigate to <a href="options-general.php?page=maintenance-mode.php">Settings &rarr; Maintenance Mode</a> to get started.
Version: 4.3
Author: Michael Wöhrer
Author URI: http://sw-guide.de/
*/

/*
    ----------------------------------------------------------------------------
   	      ____________________________________________________
         |                                                    |
         |                 Maintenance Mode                   |
         |____________________________________________________|

	                  Copyright © Michael Wöhrer 
	                    <http://sw-guide.de>
                (michael dot woehrer at gmail dot com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License <http://www.gnu.org/licenses/> for 
	more details.

	----------------------------------------------------------------------------
*/

require_once ( dirname(__FILE__) . '/inc.swg-plugin-framework.php');

class MaintenanceMode extends MaintenanceMode_SWGPluginFramework {

	/**
	 * Apply Maintenance Mode
	 */
	function ApplyMaintenanceMode() {

		// 1. Back end: Display information in administration when Maintenance Mode is activated.
		if ( is_admin() && ($this->g_opt['mamo_activate'] == 'on') && ($_GET['page'] != basename($this->g_info['PluginFile'])) )  {
			add_action('admin_notices', array(&$this, 'display_admin_msg'));
		}

		// 2. Front end: display maintenance mode
		if ($this->g_opt['mamo_activate'] == 'on') {

			# Path to splash page 
			$path503file = get_template_directory() . '/503.php';
			if ( ($this->g_opt['mamo_use_503_php'] == '1') && file_exists($path503file)  ) {
				$path503file = $path503file;
			} else {
				$path503file = dirname(__FILE__) . '/maintenance-mode_site.php';
			}
	
			if( (!$this->current_user_can_access_on_maintenance()) && ($this->g_opt['mamo_noaccesstobackend'] == '1') && (!strstr($_SERVER['PHP_SELF'], 'wp-login.php'))  ) {
				$status = 'noaccesstobackend';	// Some status to be used later in '/maintenance-mode_site.php'
				# Display splash page on any access to the blog (frontend & backend!)
				include($path503file);
			    exit();
			}
			if(	!strstr($_SERVER['PHP_SELF'], 'feed/') 
					&& !strstr($_SERVER['PHP_SELF'], 'trackback/')
					&& !is_admin()  
					&& ! ( strstr($_SERVER['PHP_SELF'], 'upgrade.php') && $this->current_user_can_access_on_maintenance() )	// we do not want to block the upgrade.php to upgrade the database. One user reported an issue about that therefore we add this line of code...	
					&& !strstr($_SERVER['PHP_SELF'], 'wp-login.php')
					&& !strstr($_SERVER['PHP_SELF'], 'async-upload.php')	// needed for Media Uploader under WP 2.7, otherwise Maintenance Mode being displayed in the iframe when modifying image title etc.
					&& !$this->is_excluded_url()
					&& !$this->current_user_can_access_on_maintenance()
				) {
					# Apply HTTP header
			    	if ($this->g_opt['mamo_503'] == '1') $this->http_header_unavailable();
					# Display splash page
					include($path503file);
				    exit();    
			} elseif( (strstr($_SERVER['PHP_SELF'], 'feed/') || strstr($_SERVER['PHP_SELF'], 'trackback/') ) ) {
				# HTTP header for feed and trackback
				$this->http_header_unavailable(); 
			    exit();    
			}
		}	// if ($this->g_opt['mamo_activate'] == 'on')
	}

	function display_admin_msg() { 
		echo '<div class="error"><p>'.__('The Maintenance Mode is active. Please don\'t forget to',$this->g_info['ShortName']).' <a href="admin.php?page=' . basename($this->g_info['PluginFile']) . '">'.__('deactivate',$this->g_info['ShortName']).'</a> '.__('it as soon as you are done.',$this->g_info['ShortName']).'</p></div>'; 
	}

	function is_excluded_url() {
		$urlarray = $this->g_opt['mamo_excludedpaths'];
		$urlarray = preg_replace("/\r|\n/s", ' ', $urlarray);	// needed, otherwise explode doesn't work here
		$urlarray = explode(' ', $urlarray);		
		$oururl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		foreach ($urlarray as $expath) {
			if ((!empty($expath)) && (strpos($oururl, $expath) !== false)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if the current user can access to front-end on maintenance
	 */
	function current_user_can_access_on_maintenance() {
	
		// For "wp_get_current_user();". We need to include now since it is by default included AFTER plugins are being loaded.
		if (!function_exists('wp_get_current_user')) require (ABSPATH . WPINC . '/pluggable.php');
	
		$admin_role = get_role('administrator');
		$admin_caps = $admin_role->capabilities;
		if ( array_key_exists('access_on_maintenance', $admin_caps) ) {
			# Capability for Administrator role does exist, so we don't add or modify it
		} else {
			# Maintenance Capability for Administrator role DOES NOT EXIST, so we add the capability and grant it.
			$admin_role->add_cap('access_on_maintenance', true);
		}
	
		return current_user_can('access_on_maintenance');
	
	}
	
	/**
	 * Plugin Options
	 */
	function PluginOptionsPage() {

		//Add options
		$this->AddContentMain(__('Activate/Deactivate Maintenance Mode',$this->g_info['ShortName']), "
			<table border='0'><tr>
				<td width='130'>
					<p style='font-weight: bold; line-height: 2em;'>
						<input id='radioa1' type='radio' name='mamo_activate' value='on' " . ($this->COPTHTML('mamo_activate')=='on'?'checked="checked"':'') . " />
						<label for='radioa1'>".__('Activated',$this->g_info['ShortName'])."</label>
						<br />	
						<input id='radioa2' type='radio' name='mamo_activate' value='off' " . ($this->COPTHTML('mamo_activate')!='on'?'checked="checked"':'') . " />
						<label for='radioa2'>".__('Deactivated',$this->g_info['ShortName'])."</label>
					</p>				
				</td>
				<td>
					<div class='submit' style='text-align: left;'>
						<input type='submit' name='update-options-".$this->g_info['ShortName']. "' class='button-primary' value='" . __('Save Changes',$this->g_info['ShortName']) . "' />
					</div>			
				</td>
			</tr></table>
			");
	
		$this->AddContentMain(__('Message',$this->g_info['ShortName']), "
			<table width='100%' cellspacing='2' cellpadding='5' class='editform'> 
			<tr valign='center'> 
				<th align=left width='150px' scope='row'><label for='mamo_pagetitle'>".__('Title',$this->g_info['ShortName']).":</label></th> 
				<td width='100%'><input style='font-weight:bold;' name='mamo_pagetitle' type='text' id='mamo_pagetitle' value='" . htmlspecialchars(stripslashes($this->g_opt['mamo_pagetitle'])) . "' size='60' /></td>
			</tr>
			<tr valign='top'> 
				<th align=left width='150px' scope='row'><label for='mamo_pagemsg'>".__('Message',$this->g_info['ShortName']).":</label></th> 
				<td width='100%'><textarea style='font-size: 90%; width:95%;' name='mamo_pagemsg' id='mamo_pagemsg' rows='15' >" . $this->COPTHTML('mamo_pagemsg') . "</textarea>
				<p class='info'>".__('Use HTML only, no PHP allowed. You can use <strong>[blogurl]</strong>, <strong>[blogtitle]</strong> and <strong>[backtime]</strong> as placeholders.',$this->g_info['ShortName'])."</p>
				</td>
			</tr>
			</table>
			<h4>".__('Use 503.php',$this->g_info['ShortName'])."</h4>
			<p>".__('With the following option activated the plugin will use the file \'503.php\' from the current theme directory for the splash page instead using \'maintenance-mode_site.php\' from the plugin directory; If there is no \'503.php\', it will use the default file \'maintenance-mode_site.php\'.',$this->g_info['ShortName']).' '.
			__('So this option will help using a customized splash page without the fear of losing this page when updating the plugin.',$this->g_info['ShortName']).' '.
			__('Please note that you can\'t use WordPress theme functions (e.g. <em>get_sidebar()</em>, etc.) in the \'503.php\'.',$this->g_info['ShortName'])
			."</p>		
			<p>
				<input name='mamo_use_503_php' type='checkbox' id='mamo_use_503_php' value='1' " . ($this->COPTHTML('mamo_use_503_php')=='1'?'checked="checked"':'') . " /> 
				<label for='mamo_use_503_php'>".__('Use 503.php from within the theme directory for the splash page',$this->g_info['ShortName'])."</label>
			</p>			
			");
	
		$this->AddContentMain(__('Backtime and HTTP header',$this->g_info['ShortName']), "
			<table width='100%' cellspacing='2' cellpadding='5' class='editform'> 
			<tr valign='center'> 
				<th align=left width='170px' scope='row'><label for='mamo_backtime'>".__('Backtime in minutes',$this->g_info['ShortName']).":</label></th> 
				<td width='30px'><input name='mamo_backtime' type='text' id='mamo_backtime' value='" . $this->COPTHTML('mamo_backtime') . "' size='3' /></td> 
				<td class='info' style='padding-left: 20px;'>".__('A special HTML header for feed and trackback will be applied (&laquo;503 Service Unavailable&raquo;) including \'retry after x minutes\'. Enter here the approximate time in minutes to retry. Also, by using the placeholder <strong>[backtime]</strong> above, the time in minutes be displayed to the visitors as well.',$this->g_info['ShortName'])."</td>
			</tr> 
			</table>
			<p style='margin-top: 20px;'>
				<input name='mamo_503' type='checkbox' id='mamo_503' value='1' " . ($this->COPTHTML('mamo_503')=='1'?'checked="checked"':'') . " /> 
				<label for='mamo_503'>".__('Apply HTTP header \'503 Service Unavailable\' and \'Retry-After &lt;backtime&gt;\' to Maintenance Mode splash page',$this->g_info['ShortName'])."</label>
			</p>
			");
	
		$this->AddContentMain(__('Paths to be still accessable',$this->g_info['ShortName']), "
			<p class='info'>
				".__('Enter paths that shall be excluded and still be accessable. Separate multiple paths with line breaks.<br />Example: If you want to exclude <em>http://site.com/about/</em>, then enter <em>/about/</em>.',$this->g_info['ShortName'])."
			</p>
			<textarea style='width:95%;' name='mamo_excludedpaths' id='mamo_excludedpaths' rows='2' >" . $this->COPTHTML('mamo_excludedpaths') . "</textarea>
			");
	
		$this->AddContentMain(__('Permissions',$this->g_info['ShortName']), '
			<h4>'.__('How to permit blog editors to see the blog\'s front-end',$this->g_info['ShortName']).'</h4>
			<p>'.__('As soon as you activate the maintenance mode, a splash page is being added to your blog that informs visitors about your blog maintenance. Logged in administrators get full access to the blog including the front-end and will not see any splash page.<br />This plugin adds the capability \'access_on_maintenance\' to the role \'Administrator\'. If users with a specific role, for instance \'Editor\', should get full access to the blog as well, use an appropriate plugin like',$this->g_info['ShortName'])
			 	.' <a href="http://www.im-web-gefunden.de/wordpress-plugins/role-manager/">Role Manager</a> '
				.__('and grant the capability \'Access On Maintenance\' for the role \'Editor\' or for any other role of your choice. <br />Check out',$this->g_info['ShortName'])
				.' <a href="http://codex.wordpress.org/Roles_and_Capabilities">WordPress Codex > Roles and Capabilities</a> '
				.__('for further information.',$this->g_info['ShortName']).'
			</p>
			<h4>'.__('How to deny non-administrators to access the blog\'s back-end (in addition to not see the front-end)',$this->g_info['ShortName']).'</h4>
			<p>'.__('By activating the following option, you can prevent users that do not have the capability \'access_on_maintenance\' from being able to access the back-end (the WordPress\' administration) too. This plugin adds the capability \'access_on_maintenance\' to the role \'Administrator\', so any other roles (like editors, authors, etc.) will not have access to the back-end after activating the following option.',$this->g_info['ShortName']).'
			<p>
					<input name="mamo_noaccesstobackend" type="checkbox" id="mamo_noaccesstobackend" value="1" ' . ($this->COPTHTML('mamo_noaccesstobackend')=='1'?'checked="checked"':'') . ' /> 
					<label for="mamo_noaccesstobackend">'.__('Deny non-administrators to access the blog\'s back-end',$this->g_info['ShortName']).'</label>
			</p>



			');
		
		// Sidebar, we can also add individual items...
		$this->PrepareStandardSidebar();
		
		$this->GetGeneratedOptionsPage();
	
	
	}
	
	/**
	 * Apply HTTP header
	 */
	function http_header_unavailable() {
	
	   	header('HTTP/1.0 503 Service Unavailable');
	
		$backtime = intval($this->g_opt['mamo_backtime']);
		if ( $backtime > 1 ) {
	    	# Apply return-after only if value > 0. Also, intval returns 0 on failure; empty arrays and objects return 0, non-empty arrays and objects return 1
			header('Retry-After: ' . $backtime * 60 );
		}
	
	}

	/**
	 * Convert option prior to save ("COPTSave"). 
	 * !!!! This function is used by the framework class !!!!
	 */
	function COPTSave($optname) {
		switch ($optname) {
			case 'mamo_excludedpaths':
			    return $this->LinebreakToWhitespace($_POST[$optname]);
			default:
				return $_POST[$optname];
		} // switch
	}


	/**
	 * Convert option before HTML output ("COPTHTML"). 
	 * *NOT* used by the framework class
	 */
	function COPTHTML($optname) {
		$optval = $this->g_opt[$optname];
		switch ($optname) {
			case 'mamo_excludedpaths':
				return $this->WhitespaceToLinebreak($optval);
			case 'mamo_pagetitle':
				return htmlspecialchars(stripslashes($optval));
			case 'mamo_pagemsg':
				return htmlspecialchars(stripslashes($optval));
			default:
				return $optval;
		} // switch
	}




} // class


if( !isset($myMaMo)  ) {
	// Create a new instance of your plugin that utilizes the WordpressPluginFramework and initialize the instance.
	$myMaMo = new MaintenanceMode();

	$myMaMo->Initialize( 
		// 1. We define the plugin information now and do not use get_plugin_data() due to performance.
		array(	 
			# Plugin name
				'Name' => 			'Maintenance Mode',
			# Author of the plugin
				'Author' => 		'Michael W&ouml;hrer',
			# Authot URI
				'AuthorURI' => 		'http://sw-guide.de/',
			# Plugin URI
				'PluginURI' => 		'http://sw-guide.de/wordpress/plugins/maintenance-mode/',
			# Support URI: E.g. WP or plugin forum, wordpress.org tags, etc.
				'SupportURI' => 	'http://wordpress.org/tags/maintenance-mode',
			# Name of the options for the options database table
				'OptionName' => 	'plugin_maintenance-mode',
			# Old option names to delete from the options table; newest last please
				'DeleteOldOpt' =>	array('plugin_maintenancemode', 'plugin_maintenancemode2'),
			# Plugin version
				'Version' => 		'4.3',
			# First plugin version of which we do not reset the plugin options to default;
			# Normally we reset the plugin's options after an update; but if we for example
			# update the plugin from version 2.3 to 2.4 und did only do minor changes and
			# not any option modifications, we should enter '2.3' here. In this example
			# options are being reset to default only if the old plugin version was < 2.3.
				'UseOldOpt' => 		'2.3',
			# Copyright year(s)
				'CopyrightYear' => 	'2006-2009',
			# Minimum WordPress version
				'MinWP' => 			'2.3',				
			# Do not change; full path and filename of the plugin
				'PluginFile' => 	__FILE__,
			# Used for language file, nonce field security, etc.				
				'ShortName' =>		'maintenance-mode',
			),

		// 2. We define the plugin option names and the initial options
		array(
			'mamo_activate' => 			'off',
			'mamo_excludedpaths' => 	'',
			'mamo_backtime' => 			'60',
			'mamo_pagetitle' => 		'Maintenance Mode',
			'mamo_pagemsg' => 			'<h1>Maintenance Mode</h1>' . "\n" . '<p><a title="[blogtitle]" href="[blogurl]">[blogtitle]</a> is currently undergoing scheduled maintenance.<br />' . "\n" . 'Please try back <strong>in [backtime] minutes</strong>.</p>' . "\n" . '<p>Sorry for the inconvenience.</p>' . "\n\n" . '<!-- GERMAN' . "\n" . '<h1>Wartungsmodus</h1>' . "\n" . '<p>Derzeit werden auf <a title="[blogtitle]" href="[blogurl]">[blogtitle]</a> Wartungsarbeiten durchgef&uuml;hrt.<br />Bitte versuchen Sie es <strong>in [backtime] Minuten</strong> nochmal.</p>' . "\n" . '<p>Vielen Dank f&uuml;r Ihr Verst&auml;ndnis.</p>' . "\n" . '-->' . "\n\n" . '<!-- FRENCH' . "\n" . '<h1>Maintenance en cours</h1>' . "\n" . '<p><a title="[blogtitle]" href="[blogurl]">[blogtitle]</a> est actuellement en cours de maintenance.<br />' . "\n" . 'Merci de vous reconnecter <strong>dans [backtime] minutes</strong>.</p>' . "\n" . '<p>Merci de bien vouloir accepter nos excuses.</p>' . "\n" . '-->',
			'mamo_503' => 				'',
			'mamo_noaccesstobackend' => '',
			'mamo_use_503_php' => '',
		));


	$myMaMo->ApplyMaintenanceMode();



	################################################################################
	# Template Tags for using in themes
	################################################################################
	/**
	 * You can display a warning message in the front-end if you are logged in and the Maintenance Mode is activated
	 * to remember you to deactivate the Maintenance Mode.
	 */	 
	function is_maintenance() {
		global $myMaMo; 
		if ( $myMaMo->g_opt['mamo_activate'] == 'on' ) {
			return true;
		} else {
			return false;
		}
	}


} // if( !$myMaMo


?>