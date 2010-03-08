<?php
/**
 * Capability Manager Backup Tool.
 * Provides backup and restore functionality to Capability Manager.
 *
 * @version		$Rev: 154498 $
 * @author		Jordi Canals
 * @package		CapsMan
 * @link		http://alkivia.org/wordpress/community
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

// File cannot be called directly
if (isset($_SERVER['SCRIPT_FILENAME']) && 'backup.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die (''); // Silence is gold
}

?>
<div class="wrap">
	<div id="icon-capsman-admin" class="icon32"></div>
	<h2><?php _e('Backup Tool for Capability Manager', $this->ID) ?></h2>

	<form method="post" action="tools.php?page=<?php echo $this->p_dirs['subdir']; ?>-tool">
	<?php wp_nonce_field('capsman-backup-tool'); ?>
	<fieldset>
	<table id="akmin">
	<tr>
		<td class="content">
		<dl>
			<dt><?php _e('Backup and Restore', $this->ID); ?></dt>
			<dd>
				<table width='100%' class="form-table">
				<tr>
					<th scope="row"><?php _e('Select action:', $this->ID); ?></th>
					<td>
						<select name="action">
							<option value="backup"> <?php _e('Backup roles and capabilities', $this->ID); ?> </option>
							<option value="restore"> <?php _e('Restore last saved backup', $this->ID); ?> </option>
						</select> &nbsp;
						<input type="submit" name="Perform" value="<?php _e('Do Action', $this->ID) ?>" class="button-primary" />
					</td>
				</tr>
				</table>
			</dd>
		</dl>

		<dl>
			<dt><?php _e('Reset WordPress Defaults', $this->ID)?></dt>
			<dd>
				<p style="text-align:center;"><strong><span style="color:red;"><?php _e('WARNING:', $this->ID); ?></span> <?php _e('Reseting default Roles and Capabilities will set them to the WordPress install defaults.', $this->ID); ?></strong><br />
					<?php _e('If you have installed any plugin that adds new roles or capabilities, these will be lost.', $this->ID)?><br />
					<strong><?php _e('It is recommended to use this only as a last resource!')?></strong></p>
				<p style="text-align:center;"><a class="ak-delete" title="<?php echo attribute_escape(__('Reset Roles and Capabilities to WordPress defaults', $this->ID)) ?>" href="<?php echo wp_nonce_url("tools.php?page={$this->p_dirs['subdir']}-tool&amp;action=reset-defaults", 'capsman-reset-defaults'); ?>" onclick="if ( confirm('<?php echo js_escape(sprintf(__("You are about to reset Roles and Capabilities to WordPress defaults.\n 'Cancel' to stop, 'OK' to reset.", $this->ID), $roles[$default])); ?>') ) { return true;}return false;"><?php _e('Reset to WordPress defaults', $this->ID)?></a>

			</dd>
		</dl>

		<p class="footer"><a href="<?php echo $this->p_data['PluginURI']; ?>"><?php echo $this->p_data['Name'] . ' ' . $this->p_data['Version']?></a> &nbsp;
			&copy; Copyright 2009 <?php echo $this->p_data['Author']?></p>
		</td>
		<td class="sidebar">
			<dl>
				<dt>Capability Manager</dt>
				<dd>
					<ul>
						<li><a href="http://alkivia.org/wordpress/capsman" class="capsman-home"><?php _e('Plugin Homepage', $this->ID); ?></a></li>
						<li><a href="http://wordpress.org/tags/capsman?forum_id=10" class="support-forum"><?php _e('Support Forum', $this->ID); ?></a></li>
						<li><a href="http://alkivia.org" class="ak-home"><?php _e('Author Homepage', $this->ID)?></a></li>
						<li><a href="http://alkivia.org/donate" class="donate"><?php _e('Help donating', $this->ID)?></a></li>
					</ul>
				</dd>
			</dl>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>
</div>
