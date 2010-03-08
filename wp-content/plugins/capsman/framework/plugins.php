<?php
/**
 * Plugins related functions and classes.
 *
 * @version		$Rev: 63 $
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link 		http://alkivia.org
 * @license 	http://www.gnu.org/licenses/gpl.html GNU General Public License v3

	Copyright 2009 Jordi Canals <alkivia@jcanals.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! class_exists('cmanPlugin') ) :
	/**
	 * Abtract class to be used as a plugin template.
	 * Must be implemented before using this class and it's recommended to prefix the class to prevent collissions.
	 * There are some special functions thay can declared (as protected) in implementations to perform main actions:
	 * 		- activate: (Protected) Actions to run when activating the plugin.
	 * 		- _deactivate: (Hook, must be public) Actions to run when deactivating the plugin.
	 * 		- update: (Protected) Actions to update the plugin to a new version. (Updating version on DB is done after this).
	 * 						Takes plugin running version as a parameter.
	 * 		- setDefaults: (Protected) Fills the $defaults class var with the default settings.
	 * 		- init: (Protected) Actions to run when plugins initialization is performed (In plugins loaded).
	 * 		- widgetsInit: (Protected) Actions to init plugin widgets (In widgets_init).
	 * 		- startUp: (Protected) Actions to run at system startup (before plugins are loaded).
	 * 		- _adminMenus: (Hook, must be public) Set the menus in WordPress Dashboard.
	 *
	 * @uses		plugins.php
	 * @author		Jordi Canals
	 * @package		Alkivia
	 * @subpackage	Framework
	 * @link		http://alkivia.org
	 */
	abstract class cmanPlugin
	{
		/**
		 * Plugin ID. Is the plugin short name.
		 * Filled in constructor (as a constructor param).
		 * @var string
		 */
		protected $ID;

		/**
		 * Plugin main file.
		 * Filled in constructor (as a constructor param).
		 * Cointains the full path to the main plugin's file. The one cointaining plugin's data header.
		 * @var string
		 */
		protected $p_file;

		/**
		 * Plugin data. Readed from the main plugin file header and the readme file.
		 * Filled in loadPluginData(). Called in constructor.
		 * From the filename:
		 * 		- 'ID' - Plugin internal short name. Taken from main plugin's file name.
		 * Fom the file header:
		 *		- 'Name' - Name of the plugin, must be unique.
		 *		- 'Title' - Title of the plugin and the link to the plugin's web site.
		 *		- 'Description' - Description of what the plugin does and/or notes from the author.
		 *		- 'Author' - The author's name
		 *		- 'AuthorURI' - The author's web site address.
		 *		- 'Version' - The plugin's version number.
		 *		- 'PluginURI' - Plugin's web site address.
		 *		- 'TextDomain' - Plugin's text domain for localization.
		 *		- 'DomainPath' - Plugin's relative directory path to .mo files.
		 * From readme.txt file :
		 * 		- 'Contributors' - An array with all contributors nicknames.
		 * 		- 'Tags' - An array with all plugin tags.
		 * 		- 'DonateURI' - The donations page address.
		 *      - 'Requires' - Minimum required WordPress version.
		 *      - 'Tested' - Higher WordPress version this plugin has been tested.
		 *      - 'Stable' - Last stable tag when this was released.
		 * @var array
		 */
		protected $p_data;

		/**
		 * Plugin paths: folder name, absolute path to plugin's folder and folder url.
		 * Filled in loadPaths(). Called in constructor.
		 *		- 'subdir' - The base plugin subdirectory.
		 *		- 'path' - The full path to plugin's folder.
		 *		- 'url' - The full URL to plugin's folder.
		 * @var array
		 */
		protected $p_dirs;

		/**
		 * Plugin saved data.
		 *		- 'post' - Saves the current post.
		 *		- 'more' - Saves the read more status.
		 * @var array
		 */
		protected $saved;

		/**
		 * Plugin settings (from DB)
		 * @var array
		 */
		protected $settings;

		/**
		 * Plugin default settings.
		 * This settings can difer from install defaults and are used to fill settings loaded from DB
		 * @var array
		 */
		protected $defaults = array();

		/**
		 * Flag to see if we are installing (activating for first time) or reactivating the plugin.
		 * @var boolean
		 */
		protected $installing = false;

		/**
		 * Flag to see if plugin needs to be updated.
		 * @var boolean
		 */
		protected $needs_update = false;

		/**
		 * Class constructor.
		 * Calls the implementated method 'startUp' if it exists. This is done at plugins loading time.
		 * Prepares admin menus by seting an action for the implemented method '_adminMenus' if it exists.
		 *
		 * @param string $plugin_file	Full main plugin's filename (absolute to root).
		 * @param string $ID  Plugin short name (known as plugin ID).
		 * @return cmanPlugin|false	The plugin object or false if not compatible.
		 */
		final function __construct( $plugin_file, $ID = '' ) {

			$this->p_file = trim($plugin_file);
			$this->ID = ( empty($ID) ) ? strtolower(basename($this->p_file, '.php')) : trim($ID) ;

			// Load component data and settings.
			if ( method_exists($this, 'setDefaults') ) {
				$this->setDefaults();
			}
			$this->loadPluginData();
			$this->loadPaths();

			if ( $this->isCompatible() ) {
				// Activation and deactivation hooks.
				register_activation_hook($this->p_file, array($this, '_activatePlugin'));
				if ( method_exists($this, '_deactivate') ) {
					register_deactivation_hook($this->p_file, array($this, '_deactivate'));
				}

				// Load style files.
				if ( is_admin() ) {
					add_action('admin_print_styles', array($this, '_enqueueStyles'));	// For Compatibility with WP 2.8
				} else {
					add_action('wp_print_styles', array($this, '_enqueueStyles'));
				}

				// Init plugins at plugins and widgets
				add_action('plugins_loaded', array($this, '_initPlugin'));
				add_action('widgets_init', array($this, '_initWidgets'));

				// Add administration menus.
				if ( method_exists($this, '_adminMenus') ) {
					add_action('admin_menu', array($this, '_adminMenus'));		// Add Panel menus.
				}

				// Startup the plugin.
				if ( method_exists($this, 'startUp') ) {
					$this->startUp();
				}
			}
		}

		/**
		 * Activates the plugin. Only runs on first activation.
		 * Saves the plugin version in DB, and calls the 'activate' method.
		 *
		 * @hook register_activation_hook
		 * @access private
		 * @return void
		 */
		final function _activatePlugin() {

			if ( method_exists($this, 'setDefaults') ) {
				$this->setDefaults();
			}

			// If there is an additional function to perform on activate.
			if ( method_exists($this, 'activate') ) {
				$this->activate();
			}

			$this->settings = $this->defaults;
			add_option($this->ID . '_settings', $this->settings);
			add_option($this->ID . '_version', $this->p_data['Version']);
		}

		/**
		 * Init the plugin (In action 'plugins_loaded')
		 * Here whe call the 'update' and 'init' functions. This is done after the plugins are loaded.
		 * Also the plugin version and settings are updated here.
		 *
		 * @hook action plugins_loaded
		 * @access private
		 * @return void
		 */
		final function _initPlugin() {
			$this->loadTranslations();

			// First, check if the plugin needs to be updated.
			if ( $this->needs_update ) {
				if ( method_exists($this, 'update') ) {
					$version = get_option($this->ID . '_version');
					$this->update($version);
				}
				update_option($this->ID . '_version', $this->p_data['Version']);
				update_option($this->ID . '_settings', $this->settings);
			}

			// Call the custom init for the plugin.
			if ( method_exists($this, 'init') ) {
				$this->init();
			}
		}

		/**
		 * Inits the widgets (In action 'widgets_init')
		 * Before loading the widgets, we check that standard sidebar is present.
		 *
		 * @hook action 'widgets_init'
		 * @return void
		 */
		final function _initWidgets() {
			if ( method_exists($this, 'widgetsInit') && $this->isStandardSidebar() ) {
				$this->widgetsInit();
			}
		}

		/**
		 * Loads translations file, located on the plugin's lang subdir.
		 *
		 * @return void
		 */
		final protected function loadTranslations() {
			load_plugin_textdomain($this->ID, false, $this->p_dirs['subdir'] . '/lang');
		}

		/**
		 * Prepares and enqueues plugin styles.
		 * Filters used:
		 * 		- 'pluginID_style_admin' - For the admin style URL.
		 * 		- 'pluginID_style_url' - For the public style URL.
		 *
		 * @uses apply_filters() Calls the 'ID_style_url' and 'ID_style_admin' on the style file URL.
		 * @hook action wp_print_styles and admin_print_styles
		 * @access private
		 * @return void
		 */
		final function _enqueueStyles() {

			$url = '';
			if ( is_admin() ) {
				if ( file_exists($this->p_dirs['path'] . 'admin.css') ) {
					$url = $this->p_dirs['url'] . 'admin.css';
				}
				$url = apply_filters($this->ID . '_style_admin', $url);
			} else {
				if ( file_exists($this->p_dirs['path'] . 'style.css') ) {
					$url = $this->p_dirs['url'] . 'style.css';
				}
				$url = apply_filters($this->ID . '_style_url', $url);
			}

			if ( ! empty($url) ) {
    			wp_register_style($this->ID, $url, false, $this->p_data['Version']);
    			wp_enqueue_style($this->ID);
	    	}
    	}

		/**
		 * Returns the plguin Folder basename.
		 *
		 * @return string
		 */
		final public function getFolder() {
			if ( empty($p_dirs) ) {
				$this->loadPaths();
			}
			return $this->p_dirs['subdir'];
		}

		/**
		 * Returns the URL to the plugin folder (with trailing slash).
		 *
		 * @return string
		 */
		final public function getURL() {
			if ( empty($p_dirs) ) {
				$this->loadPaths();
			}
			return $this->p_dirs['url'];
		}

		/**
		 * Returns the Absolute path to plugin folder (with trailing slash).
		 *
		 * @return string
		 */
		final public function getPath() {
			if ( empty($p_dirs) ) {
				$this->loadPaths();
			}
			return $this->p_dirs['path'];
		}

    	/**
    	 * Returns private or protected values.
    	 * @since 0.6
    	 *
    	 * @param $name	Name of the value.
    	 * @return mixed Requested value.
    	 */
    	public function __get( $name ) {

    		if ( empty($this->p_data) ) {
				$this->loadPluginData();
    		}

    		$name = strtolower($name);
    		switch ( $name ) {
    			case 'id':
    				return $this->ID;
    				break;
    			case 'file':
    				return $this->p_file;
    				break;
    			case 'version':
    				return $this->p_data['Version'];
    				break;
    			default:
   					return false;
    		}

    	}

    	/**
    	 * Returns a plugin setting.
    	 * If no specific settings is requested, returns all settings.
    	 * If requested a non existent settings, returns $default.
    	 *
    	 * @param $name	Name for the settings to return.
    	 * @param $default Default value to use if setting does not exists.
    	 * @return mixed	The settings value or an array with all settings.
    	 */
    	public function getOption( $name = '', $default = false ) {
    		if ( empty($name) ) {
    			return $this->settings;
    		} elseif ( isset($this->settings[$name]) ) {
    			return $this->settings[$name];
    		} else {
    			return $default;
    		}

    	}

		/**
		 * Returns plugin data.
		 * This data is loaded from the main plugin's file.
		 *
		 * @see $p_data
		 * @return mixed The parameter requested or an array wil all data.
		 */
		final public function getPluginData( $name = '' ) {
			if ( empty($name) ) {
				return $this->p_data;
			} elseif ( isset( $rhis->p_data[$name]) ) {
				return $this->p_data['name'];
			} else {
				return false;
			}
		}

		/**
		 * Loads plugin data and settings.
		 * Data is loaded from plugin and readme file headers. Settings from Database.
		 *
		 * @return void
		 */
		final private function loadPluginData() {
			if ( empty($this->p_data) ) {
				if ( ! function_exists('get_plugin_data') ) {
					require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				$p_data = get_plugin_data($this->p_file);
				$r_data = plugin_readme_data($this->p_file);
				$this->p_data = array_merge($r_data, $p_data);
			}

			$this->settings = get_option($this->ID . '_settings');
			if ( ! empty($this->defaults) && is_array($this->defaults) ) {
				if ( is_array($this->settings) ) {
					$this->settings = array_merge($this->defaults, $this->settings);
				} else {
					$this->settings = $this->defaults;
				}
			}

			$ver = get_option($this->ID . '_version');
			if ( false === $ver ) {
				$this->installing = true;
			} elseif ( version_compare($ver, $this->p_data['Version'], 'ne') ) {
				$this->needs_update = true;
			}
		}

		/**
		 * Saves the current post state.
		 *
		 * @return void
		 */
		final protected function savePost() {
			global $post, $more;

			$this->saved['post'] = $post;
			$this->saved['more'] = $more;
		}

		/**
		 * Restores the current post state.
		 * Saved in savePost()
		 *
		 * @return void
		 */
		final protected function restorePost() {
			global $post, $more;

			$more = $this->saved['more'];
			$post = $this->saved['post'];
			setup_postdata($post);
		}

		/**
		 * Checks if the plugin is compatible with the current WordPress version.
		 * If it's not compatible, sets an admin warning.
		 *
		 * @return boolean	Plugin is compatible with this WordPress version or not.
		 */
		final private function isCompatible() {
			global $wp_version;

			if ( version_compare($wp_version, $this->p_data['Requires'] , '>=') ) {
				return true;
			} else {
				add_action('admin_notices', array($this, '_compatibleWarning'));
				return false;
			}
		}

		/**
		 * Shows a warning message when the plugin is not compatible with current WordPress version.
		 * This is used by calling the action 'admin_notices' in isCompatible()
		 *
		 * @hook action admin_notices
		 * @access private
		 * @return void
		 */
		final function _compatibleWarning() {
			$this->loadTranslations(); // We have not loaded translations yet.

			echo '<div class="error"><p><strong>' . __('Warning:', $this->ID) . '</strong> '
				. sprintf(__('The active plugin %s is not compatible with your WordPress version.', $this->ID),
					'&laquo;' . $this->p_data['Name'] . ' ' . $this->p_data['Version'] . '&raquo;')
				. '</p><p>' . sprintf(__('WordPress %s is required to run this plugin.', $this->ID), $this->p_data['Requires'])
				. '</p></div>';
		}

		/**
		 * Checks if standard functions for Widgets are present.
		 * If them are not present, we are not using the standard sidebar: an admin warning is set.
		 *
		 * MAYBE: Move to a new Widget Class ?
		 *
		 * @return boolean	Standard widget functions were found ot not.
		 */
		final private function isStandardSidebar() {

			if (	function_exists('wp_register_sidebar_widget') &&
					function_exists('wp_register_widget_control') &&
					function_exists('wp_get_sidebars_widgets') &&
					function_exists('wp_set_sidebars_widgets') )
			{
				return true;
			} else {
				add_action('admin_notices', array($this, '_standardSidebarWarning'));
				return false;
			}
		}

		/**
		 * Shows an admin warning when not using the WordPress standard sidebar.
		 * This is done by calling the action 'admin_notices' in isStandardSidebar()
		 *
		 * MAYBE: Move to a new Widget Class ?
		 *
		 * @hook action admin_notices
		 * @access private
		 * @return void
		 */
		final function _standardSidebarWarning() {
			$this->loadTranslations(); // We have not loaded translations yet.

			echo '<div class="error"><p><strong>' . __('Warning:', $this->ID) . '</strong> '
				. __('Standard sidebar functions are not present.', $this->ID) . '</p><p>'
				. sprintf(__('It is required to use the standard sidebar to run %s', $this->ID),
					'&laquo;' . $this->p_data['Name'] . ' ' . $this->p_data['Version'] . '&raquo;')
				. '</p></div>';
		}

		/**
		 * Loads the plugin paths based on the plugin main file.
		 * Paths are set as $this->p_dirs.
		 *
		 * @see $p_dirs
		 * @return void
		 */
		final private function loadPaths() {

			$this->p_dirs['path']	= dirname($this->p_file) .'/';
			$this->p_dirs['subdir']	= basename($this->p_dirs['path']);
			$this->p_dirs['url']	= WP_PLUGIN_URL . '/' . $this->p_dirs['subdir'] .'/';
		}
	}
endif;

// ======================================================= FUNCTIONS ==========

if ( ! function_exists('plugin_readme_data') ) :
	/**
	 * Parse the plugin readme.txt file to retrieve plugin's metadata.
	 *
	 * The metadata of the plugin's readme searches for the following in the readme.txt
	 * header. All metadata must be on its own line. The below is formatted for printing.
	 *
	 * <code>
	 * Contributors: contributors nicknames, comma delimited
	 * Donate link: Link to plugin donate page
	 * Tags: Plugin tags, comma delimited
	 * Requires at least: Minimum WordPress version required
	 * Tested up to: Higher WordPress version the plugin has been tested.
	 * Stable tag: Latest stable tag in repository.
	 * </code>
	 *
	 * Readme data returned array cointains the following:
	 * 		- 'Contributors' - An array with all contributors nicknames.
	 * 		- 'Tags' - An array with all plugin tags.
	 * 		- 'DonateURI' - The donations page address.
	 *      - 'Required' - Minimum required WordPress version.
	 *      - 'Tested' - Higher WordPress version this plugin has been tested.
	 *      - 'Stable' - Last stable tag when this was released.
	 *
	 * The first 8kiB of the file will be pulled in and if the readme data is not
	 * within that first 8kiB, then the plugin author should correct their plugin
	 * and move the plugin data headers to the top.
	 *
	 * The readme file is assumed to have permissions to allow for scripts to read
	 * the file. This is not checked however and the file is only opened for
	 * reading.
	 *
	 * @param string $plugin_file Path to the plugin file (not the readme file)
	 * @return array See above for description.
	 */
	function plugin_readme_data( $plugin_file ) {

		$file = dirname($plugin_file) . '/readme.txt';

		$fp = fopen($file, 'r');	// Open just for reading.
		$data = fread( $fp, 8192 );	// Pull the first 8kiB of the file in.
		fclose($fp);				// Close the file.

		preg_match( '|Contributors:(.*)$|mi', $data, $contributors );
		preg_match( '|Donate link:(.*)$|mi', $data, $uri );
		preg_match( '|Tags:(.*)|i', $data, $tags );
		preg_match( '|Requires at least:(.*)$|mi', $data, $required );
		preg_match( '|Tested up to:(.*)$|mi', $data, $tested );
		preg_match( '|Stable tag:(.*)$|mi', $data, $stable );

		foreach ( array( 'contributors', 'uri', 'tags', 'required', 'tested', 'stable' ) as $field ) {
			if ( !empty( ${$field} ) ) {
				${$field} = trim(${$field}[1]);
			} else {
				${$field} = '';
			}
		}

		$readme_data = array(
			'Contributors' => array_map('trim', explode(',', $contributors)),
			'Tags' => array_map('trim', explode(',', $tags)),
			'DonateURI' => trim($uri),
			'Requires' => trim($required),
			'Tested' => trim($tested),
			'Stable' => trim($stable) );

		return $readme_data;
	}
endif;

if ( ! function_exists('deactivate_plugin') ) :
	/**
	 * Deactivated the plugin. Normally in case incompatibilities were detected.
	 *
	 * TODO: Run Deactivation HOOK
	 *
	 * @param string $name	Plugin name.
	 * @return void
	 */
	function deactivate_plugin( $name ) {
		$plugins = get_option('active_plugins');

		if ( in_array($name, $plugins)) {
			array_splice($plugins, array_search($name, $plugins), 1);
			update_option('active_plugins', $plugins);
		}
	}
endif;
