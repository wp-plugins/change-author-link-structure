<?php
defined( 'ABSPATH' ) or die();

// Check for uniqueness
if ( ! class_exists( 'Change_Author_Link_Structure' ) ) {
	// Main Plugin Class
	class Change_Author_Link_Structure {
		
		/**
		 * Rewrite rules are added at plugin's activation.
		 *
		 * @since 0.0.2
		 */	
		static function activate( $networkwide ) {
			global $wpdb;
			global $wp_rewrite;
			// In case of network activation 
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				if ( $networkwide ) {
					$main_blog = $wpdb->blogid;
					$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
					foreach ( $blogids as $blogid ) {
						switch_to_blog( $blogid );
						Change_Author_Link_Structure::add_plugin_author_rewrite_rules( $wp_rewrite );
					}
					switch_to_blog( $main_blog );
					return;
				}
			}
			// In case of single site activation
			Change_Author_Link_Structure::add_plugin_author_rewrite_rules( $wp_rewrite );
		}
		
		/**
		 * At plugin's deactivation the rewrite rules are updated to the initial state.
		 * 
		 * @since 0.0.2
		 */
		static function deactivate( $networkwide ) {
			global $wpdb;	
			global $wp_rewrite;
			// In case of network activation
			if ( function_exists ( 'is_multisite' ) && is_multisite() ) {
				if ( $networkwide ) {
					$main_blog = $wpdb->blogid;
					$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
					foreach ( $blogids as $blogid ) {
						switch_to_blog( $blogid );	
						Change_Author_Link_Structure::remove_plugin_author_rewrite_rules( $wp_rewrite );	
					}
					switch_to_blog( $main_blog );
					return;
				}
			}
			// In case of single site activation
			Change_Author_Link_Structure::remove_plugin_author_rewrite_rules( $wp_rewrite );
		}
		
		/**
		 * The author's username is replaced with the ID in the link.
		 *
		 * @since 0.0.1
		 * 
		 * @param string $link Link in posts to the author's page.
		 * @param int $author_id Author's ID.
		 * @param string $author_nicename Author's username.
		 *
		 * @return string Modified link.
		 */
		function modify_link( $link, $author_id, $author_nicename ) {
    		$link = str_replace( $author_nicename, $author_id, $link );
    		return $link;
		}
		
		/**
		 * New rewrite rules, that allow ID's instead of usernames in the link to the author page, ared added.
		 * 
		 * @since 0.0.5
		 *
		 * @param object $wp_rewrite Global object which contains rewrite rules.
		 */
		static function add_plugin_author_rewrite_rules( $wp_rewrite ) {
			$wp_rewrite->init();
			flush_rewrite_rules();
			$rules = get_option( 'rewrite_rules' );
			$new_rules = array();
			// Rewrite rule for first page
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/?$'] = 'index.php?author=$matches[2]';
			// Rewrite rule for further pages;
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/page/(\d*)/?$'] = 'index.php?author=$matches[2]&paged=$matches[3]';
			// Rewrite rules for feed pages
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author=$matches[2]&feed=$matches[3]';
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author=$matches[2]&feed=$matches[3]';
			$rules = array_merge( $new_rules, $rules );
			update_option( 'rewrite_rules', $rules );
		}		
		
		/**
		 * The default rewrite rules are initialized and the plugin's rewrite rules are removed.
		 * 
		 * @since 0.0.3
		 *
		 * @param object $wp_rewrite Global object which contains rewrite rules.
		 */
		static function remove_plugin_author_rewrite_rules( $wp_rewrite ) {
			$wp_rewrite->init();
			flush_rewrite_rules();
			$rules = get_option( 'rewrite_rules' );
			// Delete plugin rewrite rules
			unset($rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/?$']);
			unset($rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/page/(\d*)/?$']);
			unset($rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/feed/(feed|rdf|rss|rss2|atom)/?$']);
			unset($rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/(feed|rdf|rss|rss2|atom)/?$']);
			update_option( 'rewrite_rules', $rules );	
		}
		
		/**
		 * Rewrite rules ared added after regeneration.
		 * 
		 * @since 0.0.1
		 *
		 * @param object $wp_rewrite Global object which contains rewrite rules.
		 */
		function add_author_rewrite_rules( $wp_rewrite ) {
			$new_rules = array();
			// Rewrite rule for first page
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/?$'] = 'index.php?author=$matches[2]';
			// Rewrite rule for further pages;
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/page/(\d*)/?$'] = 'index.php?author=$matches[2]&paged=$matches[3]';
			// Rewrite rules for feed pages
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author=$matches[2]&feed=$matches[3]';
			$new_rules['^([A-Za-z0-9/-]*)author/([A-Za-z0-9-]+)/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author=$matches[2]&feed=$matches[3]';
			$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
		}
	}
}
?>
