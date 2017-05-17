<?php
/*
Plugin Name: Search Franchise
Plugin URI: http://shramee.com/
Description: Boilerplate for fast track WordPress plugin development
Author: Shramee
Version: 1.0.0
Author URI: http://shramee.com/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';

/**
 * Search Franchise main class
 */
class Search_Franchise {

	/** @var string Token */
	public static $token;
	/** @var string Version */
	public static $version;
	/** @var string Plugin main __FILE__ */
	public static $file;
	/** @var string Plugin directory url */
	public static $url;
	/** @var string Plugin directory path */
	public static $path;
	/** @var Search_Franchise Instance */
	private static $_instance = null;

	/** @var Search_Franchise_Admin Instance */
	public $admin;

	/** @var Search_Franchise_Public Instance */
	public $public;

	/**
	 * Constructor function.
	 *
	 * @param string $file __FILE__ of the main plugin
	 */
	private function __construct( $file ) {

		self::$token   = 'search-franchise';
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.0.0';

		add_action( 'init', array( $this, 'init' ) );
	} // End instance()

	/**
	 * Main Search Franchise Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Search_Franchise instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}

		return self::$_instance;
	} // End __construct()

	/**
	 * Initiates the plugin
	 * @action init
	 * @since 1.0.0
	 */
	public function init() {

			//Initiate admin
			$this->_admin();

			//Initiate public
			$this->_public();
	} // End init()

	/**
	 * Initiates admin class and adds admin hooks
	 * @since 1.0.0
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Search_Franchise_Admin::instance();

		//Adding front end JS and CSS in /assets folder
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this->admin, 'plugin_links' ) );
	}

	/**
	 * Initiates public class and adds public hooks
	 * @since 1.0.0
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Search_Franchise_Public::instance();

		//Adding front end JS and CSS in /assets folder
		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue' ) );

	} // End enqueue()
}

/** Intantiating main plugin class */
Search_Franchise::instance( __FILE__ );
