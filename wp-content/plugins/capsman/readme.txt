=== Capability Manager ===
Contributors: txanny
Donate link: http://alkivia.org/donate
Tags: roles, capabilities, manager, rights, role, capability
Requires at least: 2.7
Tested up to: 2.8.5
Stable tag: 1.2.4

A simple way to manage WordPress roles and capabilities.

== Description ==

The Capability Manager plugin provides a simple way to manage role capabilities. Using it, you will be able to change the capabilities of any role, add new roles, copy existing roles into new ones, and add new capabilities to existing roles.
You can also delegate capabilities management to other users. In this case, some restrictions apply to this users, as them can only set/unset the capabilities they have.
With the Backup/Restore tool, you can save your Roles and Capabilities before making changes and revert them if something goes wrong. You'll find it on the Tools menu. 
At the current version, you cannot manage capabilities at user level (only can be managed for roles). This will be included in next versions.

**Languages included:**

* English
* Spanish
* Catalan *by <a href="http://txanny.net">Jordi Canals</a>*
* Italian *by <a href="http://gidibao.net" rel="nofollow">Gianni Diurno</a>*
* German *by <a href="http://great-solution.de/" rel="nofollow">Carsten Tauber</a>*
* Byelorussian *by <a href="http://antsar.info/" rel="nofollow">Ilyuha</a>*
* Russian *by <a href="http://www.fatcow.com" rel="nofollow">Marcis Gasuns</a>*
* POT file for easy translation to other languages included. If you translated SidePosts to your language, <a href="http://alkivia.org/contact/">you can tell us</a>

**Features:**

* Manage role capabilities.
* Add new roles or delete existing roles.
* Add new capabilities to any existing role.
* Backup and restore Roles and Capabilities to revert your last changes. 

**Future Planned Features**

* Manage capabilities at user level.

**Who can manage?**

* Capability manager only supports one role per user.
* Only users with 'manage_capabilities' can manage them. This capability is created at install time and assigned to administrators.
* Administrator role cannot be deleted.

*Administrators*

* Only administrators can grant or remove 'manage_capabilities' to other users. Cannot be removed from administrators.
* Can grant or remove any capability, included the ones them not have.
* Only administrators can manage the 'administrator' role.
* Only administrators can delete roles.

*Other users granted to manage capabilities*

* Cannot grant or remove 'manage_capabilities'.
* Cannot manage 'administrator' role.
* Cannot delete roles.
* Can only manage roles that have the same or lower level than the user.
* Can only grant or remove capabilities they have.
* Cannot manage roles they have. (This is to prevent granting/removing his own capabilities).

**Rules to create new capabilities or roles**

* Can be maximum 40 characters lenght.
* Have to start with a letter.
* Can contain only letters, digits, spaces and underscores.

== Installation ==

**System Requirements**

* **Requires PHP-5**. Older versions of PHP are obsolete and expose your site to security risks.
* Verify the plugin is compatible with your WordPress Version. If not, plugin will not load.

**Installing the plugin**

1. Unzip the plugin archive.
1. Upload the plugin's folder to the WordPress plugins directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Manage the capabilities on the 'Capabilities' page on Users menu.
1. Enjoy your plugin!

== Screenshots ==

1. Setting new capabilities for a role.
2. Actions on roles.
3. Backup/Restore tool.

== Frequently Asked Questions ==

**Where can I find more information about this plugin, usage and support ?**

* Take a look to the <a href="http://alkivia.org/wordpress/capsman">Plugin Homepage</a>.
* A <a href="http://alkivia.org/wordpress/capsman">complete manual</a> is available for users and developers.
* The <a href="http://alkivia.org/cat/capsman">plugin posts archive</a> with new announcements about this plugin.
* If you need help, <a href="http://wordpress.org/tags/capsman?forum_id=10">ask in the Support forum</a>.

**I've found a bug or want to suggest a new feature. Where can I do it?**

* To fill a bug report or suggest a new feature, please fill a report in our <a href="http://alkivia.org/tracker/set_project.php?project_id=7&ref=view_all_bug_page.php">Bug Tracker</a>.

== License ==

Copyright (C) 2009  Jordi Canals

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.

== Changelog ==

* **1.2.4** - Added Italian translation.
* **1.2.3** - Added German and Belorussian translations.
* **1.2.2** - Added Russian translation.
* **1.2.1** - Coding Standards. Corrected internal links. Updated Framework.
* **1.2** - Added backup/restore tool.
* **1.1** - Role deletion added.
* **1.0.1** - Some code improvements. Updated Alkivia Framework.
* **1.0** - First public version.
