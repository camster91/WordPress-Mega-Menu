<?php
/**
 * GitHub-based automatic plugin updater.
 *
 * Checks the GitHub repo for new releases and presents them
 * through the native WordPress plugin-update UI.
 *
 * @package Easy_Mega_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EMM_Updater {

	/**
	 * @var EMM_Updater|null
	 */
	private static $instance = null;

	/**
	 * GitHub repo owner/name.
	 *
	 * @var string
	 */
	private $repo = 'camster91/WordPress-Mega-Menu';

	/**
	 * How often to check GitHub (seconds).
	 *
	 * @var int
	 */
	private $check_interval = 43200; // 12 hours

	/**
	 * WordPress version the plugin is tested up to.
	 *
	 * @var string
	 */
	private $tested = '6.6';

	/**
	 * @return EMM_Updater
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
	}

	/**
	 * Inject update data into the WP transient.
	 *
	 * @param object $transient update_plugins transient.
	 * @return object
	 */
	public function check_update( $transient ) {
		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}

		if ( ! empty( $transient->checked ) && ! empty( $transient->response[ plugin_basename( EMM_PLUGIN_FILE ) ] ) ) {
			// Already processed this cycle.
		}

		$current = EMM_VERSION;
		$latest  = $this->get_latest_version();

		if ( $latest && version_compare( $latest['version'], $current, '>' ) ) {
			$plugin_file = plugin_basename( EMM_PLUGIN_FILE );

			$transient->response[ $plugin_file ] = (object) array(
				'id'            => 'github.com/' . $this->repo,
				'slug'          => dirname( $plugin_file ),
				'plugin'        => $plugin_file,
				'new_version'   => $latest['version'],
				'url'           => 'https://github.com/' . $this->repo,
				'package'       => $latest['package_url'],
				'icons'         => array(),
				'banners'       => array(),
				'banners_rtl'   => array(),
				'tested'        => $this->tested,
				'requires_php'  => '7.4',
				'requires'      => '5.8',
				'compatibility' => new stdClass(),
			);
		}

		return $transient;
	}

	/**
	 * Provide plugin details for the "View details" thickbox.
	 *
	 * @param false|object|array $result The result object or array.
	 * @param string             $action The type of information requested.
	 * @param object             $args   Plugin API arguments.
	 * @return false|object
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		$plugin_file = plugin_basename( EMM_PLUGIN_FILE );
		if ( dirname( $plugin_file ) !== ( $args->slug ?? '' ) ) {
			return $result;
		}

		$latest = $this->get_latest_version();
		if ( ! $latest ) {
			return $result;
		}

		$info = (object) array(
			'name'          => 'Easy Mega Menu',
			'slug'          => dirname( $plugin_file ),
			'author'        => '<a href="https://github.com/camster91">Cameron</a>',
			'author_profile'=> 'https://github.com/camster91',
			'homepage'      => 'https://github.com/' . $this->repo,
			'download_link' => $latest['package_url'],
			'version'       => $latest['version'],
			'requires'      => '5.8',
			'requires_php'  => '7.4',
			'tested'        => $this->tested,
			'last_updated'  => $latest['published_at'] ?? gmdate( 'Y-m-d H:i:s' ),
			'sections'      => array(
				'description' => 'Create beautiful mega menus like corporate platforms menus — managed visually from the admin panel. No coding required.',
				'changelog'   => $latest['changelog'] ?? 'See https://github.com/' . $this->repo . '/releases',
			),
		);

		return $info;
	}

	/**
	 * Fetch latest release from GitHub API with caching.
	 *
	 * @return array|null
	 */
	private function get_latest_version() {
		$cached = get_transient( 'emm_github_release' );
		if ( false !== $cached ) {
			return $cached;
		}

		$response = wp_remote_get(
			'https://api.github.com/repos/' . $this->repo . '/releases/latest',
			array(
				'timeout'    => 10,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['tag_name'] ) ) {
			return null;
		}

		$version = ltrim( $body['tag_name'], 'vV' );

		$package_url = '';
		if ( ! empty( $body['assets'] ) && is_array( $body['assets'] ) ) {
			foreach ( $body['assets'] as $asset ) {
				if ( ! empty( $asset['browser_download_url'] ) && substr( $asset['name'], -4 ) === '.zip' ) {
					$package_url = $asset['browser_download_url'];
					break;
				}
			}
		}
		// Fallback to source zip if no release asset.
		if ( ! $package_url && ! empty( $body['zipball_url'] ) ) {
			$package_url = $body['zipball_url'];
		}

		$data = array(
			'version'      => $version,
			'package_url'  => $package_url,
			'published_at' => $body['published_at'] ?? '',
			'changelog'    => $body['body'] ?? '',
		);

		set_transient( 'emm_github_release', $data, $this->check_interval );

		return $data;
	}
}
