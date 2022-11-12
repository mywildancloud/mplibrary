<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://brandmarketers.id
 * @since      1.0.0
 *
 * @package    Mp_Design_Library
 * @subpackage Mp_Design_Library/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mp_Design_Library
 * @subpackage Mp_Design_Library/admin
 * @author     brandmarketers.id <admin@brandmarketers.id>
 */
class Mp_Design_Library_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_init', array( $this, 'register_option' ) );
		add_action( 'admin_init', array( $this, 'license_action' ), 20 );
		add_action( 'admin_menu', array( $this, 'license_menu' ), 99999999 );
		add_action( 'add_option_mpdl_license_key', array( $this, 'activate_license' ), 20, 2 );
		add_action( 'update_option_mpdl_license_key', array( $this, 'activate_license' ), 20, 2 );
		add_action( 'admin_notices', array( $this, 'admin_license_details' ), 1 );
		add_action( 'admin_init', array( $this, 'updater' ) );
		add_action( 'plugins_loaded', array( $this, 'template_init' ) );
		add_filter( 'elementor/api/get_templates/body_args', [ $this, 'filter_library_get_templates_args' ] );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mp_Design_Library_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mp_Design_Library_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mp-design-library-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mp_Design_Library_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mp_Design_Library_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mp-design-library-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function template_init() {
		if (did_action('elementor/loaded')) {
			$status = get_option( 'mpdl_license_key_status', false );
		    if ( 'valid' == $status ) {
		    require_once MP_DESIGN_LIBRARY_PATH . '/includes/class-templates-lib.php';
			}
		}
	}


	/**
	 * Adds a menu item for the theme license under the appearance menu.
	 *
	 * since 1.0.0
	 */
	public function license_menu() {

		add_submenu_page(
			'elementor',
			'Mp Design Library',
			'Mp Library',
			'manage_options',
			'mp-design-library',
			array( $this, 'menu_page' )
		);

		
	}

	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * since 1.0.0
	 */
	public function menu_page() {

		$license = trim( get_option( 'mpdl_license_key' ) );

		// Checks license status to display under license key
		if ( ! $license ) {
			$license_error = 'Silakan masukkan kode lisensi Anda.';
		} 
		else {
			$license_error = $this->check_license();
		}

		$status = get_option( 'mpdl_license_key_status', false );
		if ( empty( $status ) ) {
			$status = 'unknown';
		}
		$status_label = strtoupper( str_replace( '_', ' ', $status ) );

		$license_data = get_option( 'mpdl_license_data' );
		$license_error = get_option( 'mpdl_license_error' );
		if ( isset( $_GET['mpdl_license'] ) && $_GET['mpdl_license'] == 'false' && isset( $_GET['license_error'] ) && ! empty( $_GET['license_error'] ) ) {
			$license_error = urldecode( stripslashes( $_GET['license_error'] ) );
		}
		?>
		<style>

			/* Admin Blocks */
			.mpdl-license-active {
		        background: #e3e3e3;
			    padding: 20px;
			    color: #2e2e2e;
			    line-height: 2em;
			}
			.mpdl-active-item {
			    font-size: 14px;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box input {
				padding: 0 10px;
				vertical-align: top;

			}
			.wrap.elementor-admin-page-license form.elementor-license-box button {
				height: 35px;
				padding: 0 10px;
				vertical-align: top;
	    		margin-left: 10px;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box {
				max-width: 600px;
			    background: #fff;
			    margin: 20px 0;
			    text-align: left;
			    border-radius: 3px;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box h3 {
			    border-bottom: 1px solid #eee;
			    padding: 20px;
			    margin: -20px -20px 20px;
			    display: -webkit-box;
			    display: -ms-flexbox;
			    display: flex;
			    -webkit-box-pack: justify;
			    -ms-flex-pack: justify;
			    justify-content: space-between;
			    -webkit-box-align: center;
			    -ms-flex-align: center;
			    align-items: center;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box label {
			    display: block;
			    font-size: 1.3em;
			    font-weight: 600;
			    margin: 1em 0;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box p.description {
			    margin: 10px 0;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box h3 small {
			    float: right;
			    font-size: 13px;
			    font-weight: 400;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box p {
			    /*margin: 0;*/
			}
			.mpdl-activation-box {
			    padding: 20px;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box .e-row-divider-bottom {
			    padding-bottom: 15px;
			    border-bottom: 1px solid #eee;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box .e-row-stretch {
			    display: -webkit-box;
			    display: -ms-flexbox;
			    display: flex;
			    -webkit-box-align: center;
			    -ms-flex-align: center;
			    align-items: center;
			    -webkit-box-pack: justify;
			    -ms-flex-pack: justify;
			    justify-content: space-between;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box h3 span {
			    -webkit-box-flex: 1;
			    -ms-flex-positive: 1;
			    flex-grow: 1;
			    padding-left: 5px;
			}
			.wrap.elementor-admin-page-license form.elementor-license-box .button {
			    height: 30px;
			    margin-left: 15px;
			    margin-bottom: 0;
			}
		</style>

		<div class="wrap elementor-admin-page-license">
			<h2><?php _e( 'Mp Design Library', 'mpdl' ); ?></h2>
			<form method="post" action="options.php" class="elementor-license-box">
				<?php settings_fields( 'mpdl-license' ); ?>
				<?php wp_nonce_field( 'mpdl_nonce', 'mpdl_nonce' ); ?>
				<?php if ( empty( $license ) || ( ! empty( $license ) && in_array( $status, array( 'item_name_mismatch', 'invalid_item_id', 'missing', 'invalid' ) ) ) ) : ?>
				<div class="mpdl-activation-box">
				<h3>
					<?php _e( 'Aktivasi Lisensi', 'mpdl' ); ?>
						<small>
							<a style="text-decoration: none;" href="https://user.brandmarketers.id/account" target="_blank" class="elementor-connect-link">
								<?php _e( 'Member Area', 'mpdl' ); ?>
							</a>
						</small>
				</h3>
	                    <p><?php _e( 'Masukkan kode lisensi, untuk mengaktifkan <strong>Mp Design Library</strong>, untuk auto update, premium support dan akses Mp Design Library template library.' ); ?></p>
	                    <ol>
	                        <li><?php printf( __( 'Masuk <a href="%s" target="_blank">Member Area</a> untuk mendapatkan kode lisensi, kemudian copy lisensi nya.' ), 'https://user.brandmarketers.id/account' ); ?></li>
	                        <li><?php _e( __( 'Paste lisensi anda pada kolom <strong>"License Keys"</strong>.' ) ); ?></li>
	                        <li><?php _e( __( 'Klik tombol <strong>"Activate License"</strong>.' ) ); ?></li>
	                    </ol>
					<label for="mpdl-license-key"><?php _e( 'Kode Lisensi', 'mpdl' ); ?></label>
					<?php if ( $license ) : ?>
						<p>
							License: <strong><span style="color: #091c73;"><?php echo $this->get_hidden_license( $license ); ?></span></strong>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $license ) &&  ( $license_error ) ) : ?>
						<p>
							Status: <span style="color: #ff0000; font-style: italic;"><?php echo esc_html( $license_error ); ?></span>
						</p>
					<?php endif; ?>
					
					<input id="mpdl_license_key" name="mpdl_license_key" type="text" class="regular-text code" value="" placeholder="<?php esc_attr_e( 'License Keys', 'mpdl' ); ?>" />

					<input type="submit" class="button button-primary" name="submit" value="<?php esc_attr_e( 'Activate License', 'mpdl' ); ?>"/>	

				</div>	

			<?php else : ?>

				<div class="mpdl-license-active">
      				<div class="mpdl-active-item">

						<?php if ( in_array( $status, array( 'valid', 'inactive', 'site_inactive', 'expired' ) ) ) : ?>
								<div class="">
								<i class="dashicons dashicons-thumbs-up"></i>
									<b>Selamat Lisensi anda sudah aktif :)</b>
								</div>
							
							<?php if ( isset( $license_data->item_name ) && $license_data->item_name ) : ?>
								<div class="">
									<i class="dashicons dashicons-category"></i>
										Product Name :
										<?php echo esc_html( str_replace( '+', ' ', $license_data->item_name ) ); ?>
								</div>
							<?php endif; ?>
							<?php if ( isset( $license_data->item_name ) && $license_data->item_name ) : ?>
                                <div class="">
                                    <i class="dashicons dashicons-admin-network"></i>
                                        Product Type : Commercial License
                                </div>
                            <?php endif; ?>
							<?php $site_count = $license_data->site_count; $license_limit = $license_data->license_limit;
								if ( 0 == $license_limit ) {
									$license_limit = '∞ Unlimited Websites';
								}
								elseif ( $license_limit > 1 ) {
									$license_limit = ''.$site_count.' / '.$license_limit.' Website';
								}
								?>
								<div class="">
										<i class="dashicons dashicons-editor-unlink"></i>
										License Activation :
										<?php echo $license_limit; ?>
								</div>
						
							<?php
								$response = wp_remote_get( 'https://elementor.getdigital.id/wp-json/template/v1/info', [
									'timeout' => 5,
									'body' => [
										// Which API version is used
										'api_version' => MP_DESIGN_LIBRARY_VERSION,
										// Which language to return
										'site_lang' => get_bloginfo( 'language' ),
									],
								]
								);
								if ( is_wp_error( $response ) ) {

								}

								$http_response_code = wp_remote_retrieve_response_code( $response );

								$library_data = json_decode( wp_remote_retrieve_body( $response ), true );

								if ( empty( $library_data ) ) {
									echo '<i class="dashicons dashicons-dismiss"></i> Templates Library : NOT CONNECTED';
									echo '<p>tidak ada template yang tersedia, silakan hubungi support!</p>';
								}
								else {
									echo '<i class="dashicons dashicons-yes-alt"></i> Templates Library : CONNECTED';
								}
										
							?>
							
						<?php endif; ?>
						</div>
				</div>

				<div class="mpdl-activation-box">
					<h3><?php _e( 'Status', 'mpdl' ); ?>:
						<?php if ( in_array( $status, array( 'expired' ) ) ) : ?>
                            <span style="color: #ff0000; font-style: italic;"><?php _e( 'Expired' ); ?></span>
                        <?php elseif ( in_array( $status, array( 'inactive' ) ) ) : ?>
                            <span style="color: #ff0000; font-style: italic;"><?php _e( 'Mismatch' ); ?></span>
                        <?php elseif ( in_array( $status, array( 'invalid' ) ) ) : ?>
                            <span style="color: #ff0000; font-style: italic;"><?php _e( 'Lisensi tidak valid' ); ?></span>
                        <?php elseif ( in_array( $status, array( 'disabled' ) ) ) : ?>
                            <span style="color: #ff0000; font-style: italic;"><?php _e( 'Disabled' ); ?></span>
                        <?php elseif ( in_array( $status, array( 'valid' ) ) ) : ?>
                            <span style="color: #008000; font-style: italic;"><?php _e( 'Active' ); ?></span>
                        <?php elseif ( in_array( $status, array( 'unknown' ) ) ) : ?>
                            <span style="color: #ff0000; font-style: italic;"><?php _e( 'Pastikan cURL website Anda aktif dan tidak blockir ip server brandmarketers.id' ); ?></span>
                        <?php elseif ( in_array( $status, array( 'site_inactive' ) ) ) : ?>
                            <span style="color: #ff0000; font-style: italic;"><?php _e( 'Lisensi Anda sedang tidak aktif di website ini' ); ?></span>
                        <?php endif; ?>

						<small>
							<?php printf( __( '<a role="button" class="button button-primary" href="https://user.brandmarketers.id" target="_blank">Member Area</a>' ) ); ?>
						</small>
					</h3>

				<?php if ( in_array( $status, array( 'inactive', 'site_inactive', 'unknown' ) ) ) : ?>
					<p class="e-row-stretch e-row-divider-bottom">
						<span>
						<?php echo __( 'Aktifkan website ini dengan kode lisensi lain?', 'mpdl' ); ?>
						</span>
						<input type="submit" class="button button-secondary" name="mpdl_license_change" value="Change License"/>
					</p>
					<p class="e-row-stretch">
					<span><?php echo __( 'Aktifkan kode lisensi', 'mpdl' ); ?></span>
					<input type="submit" class="button button-primary" name="mpdl_license_activate" value="<?php esc_attr_e( 'Activate License', 'mpdl' ); ?>"/>	
					</p>
				<?php elseif ( in_array( $status, array( 'valid' ) ) ) : ?>		
					<p class="e-row-stretch">
						<span><?php echo __( 'Nonaktifkan kode lisensi?', 'mpdl' ); ?></span>
						<input type="submit" class="button button-secondary" name="mpdl_license_deactivate" value="Deactivate License"/>
					</p>
				<?php endif; ?>	
					
				</div>
			</div>

			<?php endif; ?>
			</form>
		<?php
	
	}
	

	/**
	 * Hidden License Key
	 * 
	 * since 1.0.0
	 */

	public static function get_hidden_license() {
		$input_string = get_option( 'mpdl_license_key' );

		$start = 5;
		$length = mb_strlen( $input_string ) - $start - 5;

		$mask_string = preg_replace( '/\S/', '*', $input_string );
		$mask_string = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $input_string, $mask_string, $start, $length );

		return $input_string;
	}


	/**
	 * Registers the option used to store the license key in the options table.
	 *
	 * since 1.0.0
	 */
	public function register_option() {
		register_setting(
			'mpdl-license',
			'mpdl_license_key',
			array( $this, 'sanitize_license' )
		);
	}


	/**
	 * Sanitizes the license key.
	 *
	 * since 1.0.0
	 *
	 * @param string $new License key that was submitted.
	 * @return string $new Sanitized license key.
	 */
	public function sanitize_license( $new ) {
		$old = get_option( 'mpdl_license_key' );
		if ( $old && $old != $new ) {
			// New license has been entered, so must reactivate
			delete_option( 'mpdl_license_key_status' );
			delete_option( 'mpdl_license_data' );
			delete_option( 'mpdl_license_error' );
		}
		return $new;
	}


	/**
	 * Makes a call to the API.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_params to be used for wp_remote_get.
	 * @return array $response decoded JSON response.
	 */
	public function get_api_response( $api_params ) {
		$response = wp_remote_post( MP_DESIGN_LIBRARY_MEMBER, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		return $response;
	 }


	/**
	 * Activates the license key.
	 *
	 * @since 1.0.0
	 */
	public function activate_license() {
		$license = trim( get_option( 'mpdl_license_key' ) );
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'  	 => MP_DESIGN_LIBRARY_ID,  
			'url'        => home_url()
		);
		$response = $this->get_api_response( $api_params );
		$error = '';
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		}
		elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$code = wp_remote_retrieve_response_code( $response );
			$message = wp_remote_retrieve_response_message( $response );
			if ( empty( $message ) ) {
				$message = __( 'An error occurred, please try again.', 'mpdl' );
			}
			$error = $message.' (CODE '.$code.')';
		}
		else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( 'valid' != $license_data->license ) {
				switch( $license_data->license ) {
					case 'expired' :
						$error = sprintf(
							__( 'Kode lisensi Anda telah kadaluarsa pada %s.', 'mpdl' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$error = __( 'Kode lisensi Anda telah dinonaktifkan dan tidak dapat dipergunakan lagi.', 'mpdl' );
						break;
					case 'missing' :
						$error = __( 'Lisensi tidak valid.', 'mpdl' );
						break;
					case 'invalid' :
						$error = __( 'Lisensi tidak valid.', 'mpdl' );
						break;
					case 'site_inactive' :
						$error = __( 'Lisensi Anda sedang tidak aktif di website ini.', 'mpdl' );
						break;
					case 'item_name_mismatch' :
						$error = sprintf( __( 'Kode lisensi tidak valid untuk %s, silakan ganti dengan kode lisensi yang valid.', 'mpdl' ), MP_DESIGN_LIBRARY_NAME );
						break;
					case 'invalid_item_id' :
						$error = sprintf( __( 'Kode lisensi tidak valid untuk %s, silakan ganti dengan kode lisensi yang valid.', 'mpdl' ), MP_DESIGN_LIBRARY_NAME );
						break;
					case 'no_activations_left':
						$error = __( 'Kode lisensi Anda telah mencapai batas limit aktivasi lisensi.', 'mpdl' );
						break;
					default :
						$error = __( 'An error occurred, please try again.', 'mpdl' );
						break;
				}
			}
		}
		if ( ! empty( $error ) ) {
			if ( strpos( $error, 'resolve host' ) !== false ) {
				$error = esc_html__( 'Tidak dapat terhubung ke server lisensi Getlanding!', 'mpdl' );
			}
			update_option( 'mpdl_license_error', $error );
		}
		else {
			delete_option( 'mpdl_license_error' );
		}
		if ( isset( $license_data ) && $license_data && isset( $license_data->license ) ) {
			update_option( 'mpdl_license_key_status', $license_data->license );
			update_option( 'mpdl_license_data', $license_data );
		}
		wp_redirect( admin_url( 'admin.php?page=mp-design-library' ) );
		exit();
	}


	/**
	 * Deactivates the license key.
	 *
	 * @since 1.0.0
	 */
	public function deactivate_license() {
		$license = trim( get_option( 'mpdl_license_key' ) );
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_id'  	 => MP_DESIGN_LIBRARY_ID, 
			'url'        => home_url()
		);
		$response = $this->get_api_response( $api_params );
		$error = '';
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		}
		elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$code = wp_remote_retrieve_response_code( $response );
			$message = wp_remote_retrieve_response_message( $response );
			if ( empty( $message ) ) {
				$message = __( 'An error occurred, please try again.', 'mpdl' );
			}
			$error = $message.' (CODE '.$code.')';
		}
		else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $license_data && ( $license_data->license == 'deactivated' ) ) {
				delete_option( 'mpdl_license_key' );
				delete_option( 'mpdl_license_key_status' );
				delete_option( 'mpdl_license_data' );
				delete_option( 'mpdl_license_error' );
			}
			else {
				$error = __( 'An error occurred, please try again.', 'mpdl' );
			}
		}
		if ( ! empty( $error ) ) {
			if ( strpos( $error, 'resolve host' ) !== false ) {
				$error = esc_html__( 'Tidak dapat terhubung ke server lisensi Getlanding!', 'mpdl' );
			}
			$error = __( 'License deactivation failed!', 'mpdl' ).' '.$error;
			$base_url = admin_url( 'admin.php?page=mp-design-library' );
			$redirect = add_query_arg( array( 'mpdl_license' => 'false', 'license_error' => urlencode( $error ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}
		wp_redirect( admin_url( 'admin.php?page=mp-design-library' ) );
		exit();
	}


	/**
	 * Change the license key.
	 *
	 * @since 1.0.0
	 */
	public static function change_license() {

		delete_option( 'mpdl_license_key' );
		delete_option( 'mpdl_license_key_status' );
		delete_option( 'mpdl_license_data' );
		delete_option( 'mpdl_license_error' );

		wp_redirect( admin_url( 'admin.php?page=mp-design-library' ) );
		exit();

	}


	/**
	 * Checks if a license action was submitted.
	 *
	 * @since 1.0.0
	 */
	public function license_action() {

		if ( isset( $_POST[ 'mpdl_license_activate' ] ) ) {
			if ( check_admin_referer( 'mpdl_nonce', 'mpdl_nonce' ) ) {
				$this->activate_license();
			}
		}

		if ( isset( $_POST['mpdl_license_deactivate'] ) ) {
			if ( check_admin_referer( 'mpdl_nonce', 'mpdl_nonce' ) ) {
				$this->deactivate_license();
			}
		}

		/**
		 * Credits: Agus Muhammad, LandingPress
		 * @link https://agusmu.com
		 *
		 */
		if ( isset( $_POST['mpdl_license_change'] ) ) {
			if ( check_admin_referer( 'mpdl_nonce', 'mpdl_nonce' ) ) {
				$this->change_license();
			}
		}

	}


	/**
	 * Checks if license is valid and gets expire date.
	 *
	 * @since 1.0.0
	 *
	 * @return string $message License status message.
	 */
	public function check_license() {
		$license = trim( get_option( 'mpdl_license_key' ) );
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_id'  	 => MP_DESIGN_LIBRARY_ID, 
			'url'        => home_url()
		);
		$response = $this->get_api_response( $api_params );
		$error = '';
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		}
		elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$code = wp_remote_retrieve_response_code( $response );
			$message = wp_remote_retrieve_response_message( $response );
			if ( empty( $message ) ) {
				$message = __( 'An error occurred, please try again.', 'mpdl' );
			}
			$error = $message.' (CODE '.$code.')';
		}
		else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( 'valid' != $license_data->license ) {
				switch( $license_data->license ) {
					case 'expired' :
						$error = sprintf(
							__( 'Kode lisensi Anda telah kadaluarsa pada %s.', 'mpdl' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$error = __( 'Kode lisensi Anda telah dinonaktifkan dan tidak dapat dipergunakan lagi.', 'mpdl' );
						break;
					case 'missing' :
						$error = __( 'Lisensi tidak valid.', 'mpdl' );
						break;
					case 'invalid' :
						$error = __( 'Lisensi tidak valid.', 'mpdl' );
						break;
					case 'site_inactive' :
						$error = __( 'Lisensi Anda sedang tidak aktif di website ini.', 'mpdl' );
						break;
					case 'item_name_mismatch' :
						$error = sprintf( __( 'Kode lisensi tidak valid untuk %s, silakan ganti dengan kode lisensi yang valid.', 'mpdl' ), MP_DESIGN_LIBRARY_NAME );
						break;
					case 'invalid_item_id' :
						$error = sprintf( __( 'Kode lisensi tidak valid untuk %s, silakan ganti dengan kode lisensi yang valid.', 'mpdl' ), MP_DESIGN_LIBRARY_NAME );
						break;
					case 'no_activations_left':
						$error = __( 'Kode lisensi Anda telah mencapai batas limit aktivasi lisensi.', 'mpdl' );
						break;
					default :
						$error = __( 'An error occurred, please try again.', 'mpdl' );
						break;
				}
			}
		}
		if ( ! empty( $error ) ) {
			if ( strpos( $error, 'resolve host' ) !== false ) {
				$error = esc_html__( 'Tidak dapat terhubung ke server lisensi Getlanding!', 'mpdl' );
			}
			update_option( 'mpdl_license_error', $error );
		}
		else {
			delete_option( 'mpdl_license_error' );
		}
		if ( isset( $license_data ) && $license_data && isset( $license_data->license ) ) {
			update_option( 'mpdl_license_key_status', $license_data->license );
			update_option( 'mpdl_license_data', $license_data );
		}
		return $error;
	}

	/**
	 * Credits: Elementor
	 * @link https://elementor.com
	 *
	 */

	public function filter_library_get_templates_args( $body_args ) {
		$license_key = get_option( 'mpdl_license_key' );

		if ( ! empty( $license_key ) ) {
			$body_args['license'] = $license_key;
			$body_args['url'] = home_url();
		}

		return $body_args;
	}

	private function is_block_editor_page() {
		$current_screen = get_current_screen();

		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}

		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}

		return false;
	}

	/**
	 * Credits: Agus Muhammad, LandingPress
	 * @link https://agusmu.com
	 *
	 */

	public function admin_license_details() {
		if ( ! current_user_can( 'manage_options' ) ) {
		return;
		}

		if ( $this->is_block_editor_page() ) {
			return;
		}
	    $screen = get_current_screen();
	    $allowed_screens = array(
	        'update-core',
	        'themes',
	        'plugins'
	    );
	    if ( !isset( $screen->id ) ) {
	        return;
	    }
	    if ( ! in_array( $screen->id, $allowed_screens ) ) {
	        return;
	    }
	    $status = get_option( 'mpdl_license_key_status', false );
	    if ( in_array( $status, array( 'valid' ) ) ) {
	        return;
	    }
	    echo '<div class="notice notice-error">';
	    echo '<br/>';
	    echo '<strong>'.esc_html__( 'Selamat Datang di Mp Design Library!', 'mpdl' ).'</strong> '.'<p>'.esc_html__( 'Silakan aktifkan lisensi Mp Design Library untuk mendapatkan auto update, support teknis, dan akses ke Mp Design Library template library.', 'mpdl' ).'</p> ';
	    echo '<a class="button button-primary" href="'.admin_url('admin.php?page=mp-design-library').'" >'. esc_html__( 'Aktifkan', 'mpdl' ).'</a>';
	    echo '<br/>';
	   	echo '<br/>';
	    echo '</div>';
	    
    
	}

	/**
	 * Plugin upgrader
	 *
	 * @since v1.0.0
	 */
	public function updater() {

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

    // Disable SSL verification
    add_filter('edd_sl_api_request_verify_ssl', '__return_false');

    // Setup the updater
    $license_key = get_option( 'mpdl_license_key' );

   	if ( ! $license_key ) {
		return;
	}

	$status = get_option( 'mpdl_license_key_status', false );
    if ( 'valid' == $status ) {

	    // setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater(
	            MP_DESIGN_LIBRARY_MEMBER,
	            MP_DESIGN_LIBRARY_BASENAME,
	            [
				'version' => MP_DESIGN_LIBRARY_VERSION,                   
				'license' => $license_key,          
				'item_id' => MP_DESIGN_LIBRARY_ID,  
				'author'  => 'brandmarketers.id', 
				'beta'    => false,
				]
		);
	}

	}

}
