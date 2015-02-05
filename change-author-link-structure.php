<?php
/*
Plugin Name: Change Author Link Structure
Plugin URI: https://wordpress.org/plugins/change-author-link-structure/ 
Description: To prevent that usernames are publically visible, the username in the author's permalink is replaced with the author's ID.  
Version: 0.1.1
Author: Yvonne Breuer
License: GPL2 or later
*/

/*
Copyright (C)  2015 Yvonne Breuer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

defined( 'ABSPATH' ) or die();

/**
 * Class Change_Author_Link_Structure with functionalitiy
 */
require_once( 'lib/class-change-author-link-structure.php' );

// Check for availability
if ( class_exists( 'Change_Author_Link_Structure' ) ) {
	
	/**
	 * Fires after plugin's activation.
	 *
	 * @since 0.0.2
	 */
	register_activation_hook( __FILE__, array( 'Change_Author_Link_Structure', 'activate' ) );
        
	/**
	 * Fires after plugin's deactivation.
	 *
	 * @since 0.0.2
	 */
	register_deactivation_hook( __FILE__, array( 'Change_Author_Link_Structure', 'deactivate' ) );
	
	// Creation of instance	
	$change_author_link_structure = new Change_Author_Link_Structure();

	// Check for availability	
	if ( isset( $change_author_link_structure ) ) {
		/**
		 * Filter the link to the author page.
		 * 
		 * @since 0.0.1
		 */
		add_filter( 'author_link', array( &$change_author_link_structure, 'modify_link' ), 10, 3 ); 

		/**
		 * Add new rewrite rules
		 *
		 * @since 0.0.1
		 */
		add_filter( 'generate_rewrite_rules', array( &$change_author_link_structure, 'add_author_rewrite_rules' ), 10, 1 );
	}
}
?>
