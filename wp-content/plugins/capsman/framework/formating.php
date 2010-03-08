<?php
/**
 * General formating functions.
 * Used to format output data and send messages to user.
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

if ( ! function_exists('ak_admin_notify') ) :
	/**
	 * Displays admin notices.
	 *
	 * @param $message	Message to display.
	 * @return void
	 */
	function ak_admin_notify( $message = '' ) {
		if ( empty($message) ) {
			$message = __('Settings saved.');
		}

		echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
	}
endif;

if ( ! function_exists('ak_admin_error') ) :
	/**
	 * Displays admin ERRORS.
	 *
	 * @param $message	Message to display.
	 * @return void
	 */
	function ak_admin_error( $message ) {
		echo '<div id="error" class="error"><p><strong>' . $message . '</strong></p></div>';
	}
endif;

if ( ! function_exists('ak_pager') ) :
	/**
	 * Generic pager.
	 *
	 * @param int $total	Total elements to paginate.
	 * @param int $in_page	Number of elements per page.
	 * @param $current		Current page number.
	 * @param $url			Base url for links. Only page numbers are appended.
	 * @return string		Formated pager.
	 */

	function ak_pager( $total, $in_page, $url, $current = 0 ) {
		if ( 0 == $current ) $current = 1;

		$pages = $total / $in_page;
		$pages = ( $pages == intval($pages) ) ? intval($pages) : intval($pages) + 1;

		if ( $pages == 1 ) {
			$out = '';
		} else {
			$out = "<div class='pager'>\n";
			if ( $current != 1 ) {
				$start = $current - 1;
				$out .= '<a class="prev page-numbers" href="'. $url . $start .'">&laquo;&laquo;</a>' . "\n";
			}

			for ( $i = 1; $i <= $pages; $i++ ) {
				if ( $i == $current ) {
					$out .= '<span class="page-numbers current">'. $i ."</span>\n";
				} else {
					$out .= '<a class="page-numbers" href="'. $url . $i .'">'. $i ."</a>\n";
				}
			}

			if ( $current != $pages ) {
				$start = $current + 1;
				$out .= '<a class="next page-numbers" href="'. $url . $start .'">&raquo;&raquo;</a>' . "\n";
			}
			$out .= "</div>\n";
		}

		return $out;
	}
endif;
