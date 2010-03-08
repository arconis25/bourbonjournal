<?php
/**
 * Roles and Capabilities related functions.
 *
 * @version		$Rev: 63 $
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link		http://alkivia.org
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License v3

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

if ( ! function_exists('ak_get_roles') ) :
	/**
	 * Returns all valid roles.
	 * The returned list can be translated or not.
	 *
	 * @uses apply_filters() Calls the 'alkivia_roles_translate' hook on translated roles array.
	 * @param boolean $translate If the returned roles have to be translated or not.
	 * @return array All defined roles. If translated, the key is the role name and value is the translated role.
	 */
	function ak_get_roles( $translate = false ) {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$roles = $wp_roles->get_names();
		if ( $translate ) {
			foreach ($roles as $k => $r) {
				$roles[$k] = _c($r);
			}
			asort($roles);
			return apply_filters('alkivia_roles_translate', $roles);
		} else {
			$roles = array_keys($roles);
			asort($roles);
			return $roles;
		}
	}
endif;

if ( ! function_exists('ak_get_user_role') ) :
	/**
	 * Return the user role. Taken from WordPress roles and Capabilities.
	 *
	 * @param int|object $user_ID	User ID or the user object to find the role.
	 * @return string		User role in this blog (key, not translated).
	 */
	function ak_get_user_role( $user ) {
		global $wpdb, $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$caps_name = $wpdb->prefix . 'capabilities';

		if ( ! is_object($user) ) {
			$user = get_userdata($user);
		}
		$roles = array_filter( array_keys( (array) $user->$caps_name ), array( &$wp_roles, 'is_role' ) );

		return array_pop($roles);
	}
endif;

if ( ! function_exists('ak_level2caps') ) :

	/**
	 * Generates the caps names from user level.
	 *
	 * @param int $level	Level to convert to caps
	 * @return array		Generated caps
	 */
	function ak_level2caps( $level ) {
		$caps = array();
		$level = min(10, intval($level));

		for ( $i = $level; $i >= 0; $i--) {
			$caps["level_{$i}"] = "Level {$i}";
		}

		return $caps;

	}

endif;

if ( ! function_exists('ak_caps2level') ) :

	/**
	 * Finds the proper level from a capabilities list.
	 *
	 * @uses _ak_level_reduce()
	 * @param array $caps	List of capabilities.
	 * @return int 			Level found, if no level found, will return 0.
	 */
	function ak_caps2level( $caps ) {
		$level = array_reduce( array_keys( $caps ), '_ak_caps2level_CB', 0);
		return $level;
	}

	/**
	 * Callback function to find the level from caps.
	 * Taken from WordPress 2.7.1
	 *
	 * @return int level Level found.
	 */
	function _ak_caps2level_CB( $max, $item ) {
		if ( preg_match( '/^level_(10|[0-9])$/i', $item, $matches ) ) {
			$level = intval( $matches[1] );
			return max( $max, $level );
		} else {
			return $max;
		}
	}

endif;
