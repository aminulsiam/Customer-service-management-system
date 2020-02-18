<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 * Class CSMS_Plugin_Activator
 */
if ( ! class_exists( 'CSMS_Plugin_Activator' ) ) {
	
	/**
	 * Class CSMS_Plugin_Activator
	 */
	class CSMS_Plugin_Activator {
		
		/**
		 * create table when plugin install
		 */
		public static function activate() {
			
			global $wpdb;
			$db_table_name   = $wpdb->prefix . 'csms_history_table';  // table name
			$charset_collate = $wpdb->get_charset_collate();
			
			//Check to see if the table exists already, if not, then create it
			if ( $wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) {
				$sql = "CREATE TABLE $db_table_name (
                id int(11) NOT NULL auto_increment,
                user_id varchar(15) NOT NULL,
                post_id varchar(60) NOT NULL,
                date varchar (255)NOT NULL,
                time varchar (255)NOT NULL,
<<<<<<< HEAD
                current_time_date TIMESTAMP,
=======
                status varchar(100)NOT NULL,
>>>>>>> 5a3bcb7608f6ce334109bfb2760bdb2174605eb0
                UNIQUE KEY id (id)
        ) $charset_collate;";
				
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				
			}
			
		}//end method activate
		
		
	}//end class CSMS_Plugin_Activator
}