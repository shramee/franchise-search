<?php
/**
 * Search Franchise Admin class
 * @property string token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 */
class Search_Franchise_Admin{

	/**
	 * @var 	Search_Franchise_Admin Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Search Franchise Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Search_Franchise_Admin instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   Search_Franchise::$token;
		$this->url     =   Search_Franchise::$url;
		$this->path    =   Search_Franchise::$path;
		$this->version =   Search_Franchise::$version;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function enqueue() {
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/admin.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/admin.js', array( 'jquery' ) );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function plugin_links( $links ) {
		$ajax_url = admin_url( 'admin-ajax.php' );

		$links[] = "<a href='$ajax_url?action=search-franchise-export' download='search-franchise-export.json'>Export</a>";
		$links[] = "<a href='#search-franchise-import'>Import</a>";

		$pagenow = admin_url( 'plugins.php' );

		?>
		<style>
			#search-franchise-import, #search-franchise-reset {
				position:fixed;
				width: 340px;
				padding: 10px 16px 16px;
				right: 50%;
				top: 34%;
				margin-right: -170px;
				background: #fff;
				box-shadow: 1px 1px 3px 2px rgba(0,0,0,0.16);
				display: none;
			}
			#search-franchise-import:target, #search-franchise-reset:target {
				display: block;
			}
		</style>
		<div id='search-franchise-import'>
			<div id="search-franchise-import-msg"></div>
			<p>Are you sure you wanna import settings? All your current Storefront Pro settings will be lost.</p>
			<p><input type="file" id="search-franchise-import-file"></p>
			<a class='button button-primary' type='button' id='search-franchise-import-start'>Yeah, Load file</a>
			<a class='button right' href='#search-franchise'>No, thanks</a>
		</div>
		<div class='notice notice-warning' id='search-franchise-reset'>
			<p>Are you sure you wanna reset to default Storefront options?</p>
			<a class='button' href='<?php echo "$ajax_url?action=search-franchise-reset&redirect=$pagenow"; ?>'>Yeah</a>
			<a class='button button-primary right' href='#search-franchise'>Noooo!!!!</a>
		</div>
		<script>
			( function ( $ ) {
				var $msg = $( '#search-franchise-import-msg' ),
					msg = function ( response ) {
						if ( ! response ) return;
						if ( typeof response === 'string' ) {
							try {
								response = JSON.parse( response )
							} catch( e ) {
								console.log( e );
								alert( 'Invalid response' );
								return;
							}
						}
						if ( ! response.msg ) alert( 'Invalid response' );
						if ( ! response.type ) response.type = 'info';
						$msg.html(
							'<div class="notice notice-' + response.type + '"><p>' + response.msg + '</p></div>'
						)
					};
				$( '#search-franchise-import-start' ).click( function () {
					if ( ! window.FileReader ) {
						alert( 'The FileReader API is not supported in this browser.' );
						return;
					}

					var $i = $( '#search-franchise-import-file' ), // Put file input ID here
						input = $i[0];

					if ( input.files && input.files[0] ) {
						var file = input.files[0];
						console.log( file );
						var fr = new FileReader();
						fr.onload = function () {
							var json = fr.result;
							console.log( json );
							msg( {
								msg: 'Requesting import from file ' + file.name + '.'
							} );
							$.ajax( {
								type: "POST",
								url: '<?php echo "$ajax_url?action=search-franchise-import"; ?>',
								data: {
									json: json,
									nonce: '<?php echo wp_create_nonce( 'sfp_import_settings' ) ?>'
								},
								success: function ( res ) {
									msg( res );
								}
							} );
						};
						fr.readAsText( file );
					} else {
						// Handle errors here
						alert( "File not selected or broser incompatible." )
					}
				} );
			} )( jQuery );
		</script>
		<?php

		return $links;
	}
}