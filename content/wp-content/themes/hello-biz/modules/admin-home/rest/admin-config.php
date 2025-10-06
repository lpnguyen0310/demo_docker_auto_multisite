<?php

namespace HelloBiz\Modules\AdminHome\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\DocumentTypes\Page;
use HelloBiz\Includes\Utils;
use HelloBiz\Includes\Welcome_Banner_Config;
use HelloBiz\Includes\Banner_State_Provider;
use WP_REST_Server;

class Admin_Config extends Rest_Base {

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/admin-settings',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_admin_config' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}

	public function get_admin_config() {
		$elementor_page_id = Utils::is_elementor_active() ? $this->ensure_elementor_page_exists() : null;

		$config = $this->get_welcome_box_config( [] );

		$config = $this->get_site_parts( $config, $elementor_page_id );

		$config = $this->get_resources( $config );

		$config = apply_filters( 'hello-plus-theme/rest/admin-config', $config );

		$config['config'] = [
			'showText'     => ! Utils::is_hello_plus_installed(),
			'nonceInstall' => wp_create_nonce( 'updates' ),
			'createNewHeader' => admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=ehp-header#add_new' ),
		];

		return rest_ensure_response( [ 'config' => $config ] );
	}

	public function get_resources( array $config ) {
		$config['resourcesData'] = [
			'community' => [
				[
					'title'  => __( 'Facebook', 'hello-biz' ),
					'link'   => 'https://www.facebook.com/groups/Elementors/',
					'icon'   => 'BrandFacebookIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'YouTube', 'hello-biz' ),
					'link'   => 'https://www.youtube.com/@Elementor',
					'icon'   => 'BrandYoutubeIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Discord', 'hello-biz' ),
					'link'   => 'https://discord.com/servers/elementor-official-community-1164474724626206720',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Rate Us', 'hello-biz' ),
					'link'   => 'https://wordpress.org/support/theme/hello-biz/reviews/#new-post',
					'icon'   => 'StarIcon',
					'target' => '_blank',
				],
			],
			'resources' => [
				[
					'title'  => __( 'Help Center', 'hello-biz' ),
					'link'   => 'https://go.elementor.com/hello-biz-help/',
					'icon'   => 'HelpIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Blog', 'hello-biz' ),
					'link'   => 'https://go.elementor.com/hello-biz-blog/',
					'icon'   => 'SpeakerphoneIcon',
					'target' => '_blank',
				],
				[
					'title'  => __( 'Platinum Support', 'hello-biz' ),
					'link'   => 'https://go.elementor.com/hello-biz-platinumcare',
					'icon'   => 'BrandElementorIcon',
					'target' => '_blank',
				],
			],
		];

		return $config;
	}

	private function ensure_elementor_page_exists(): int {
		$existing_page = \Elementor\Core\DocumentTypes\Page::get_elementor_page();

		if ( $existing_page ) {
			return $existing_page->ID;
		}

		$page_data = [
			'post_title'    => 'Hello Biz page',
			'post_content'  => '',
			'post_status'   => 'draft',
			'post_type'     => 'page',
			'meta_input'    => [
				'_elementor_edit_mode' => 'builder',
				'_elementor_template_type' => 'wp-page',
			],
		];

		$page_id = wp_insert_post( $page_data );

		if ( is_wp_error( $page_id ) ) {
			throw new \RuntimeException( 'Failed to create Elementor page: ' . esc_html( $page_id->get_error_message() ) );
		}

		if ( ! $page_id ) {
			throw new \RuntimeException( 'Page creation returned invalid ID' );
		}

		wp_update_post([
			'ID' => $page_id,
			'post_title' => 'Hello Biz #' . $page_id,
		]);
		return $page_id;
	}

	private function get_elementor_editor_url( ?int $page_id, string $active_tab ): string {
		$active_kit_id = Utils::elementor()->kits_manager->get_active_id();

		$url = add_query_arg(
			[
				'post' => $page_id,
				'action' => 'elementor',
				'active-tab' => $active_tab,
				'active-document' => $active_kit_id,
			],
			admin_url( 'post.php' )
		);

		return $url . '#e:run:panel/global/open';
	}

	public function get_site_parts( array $config, ?int $elementor_page_id = null ): array {
		$last_five_pages_query = new \WP_Query(
			[
				'posts_per_page'         => 5,
				'post_type'              => 'page',
				'post_status'            => 'publish',
				'orderby'                => 'post_date',
				'order'                  => 'DESC',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'lazy_load_term_meta'    => true,
				'update_post_meta_cache' => false,
			]
		);

		$site_pages = [];

		if ( $last_five_pages_query->have_posts() ) {
			$elementor_active    = Utils::is_elementor_active();
			$edit_with_elementor = $elementor_active ? '&action=elementor' : '';
			while ( $last_five_pages_query->have_posts() ) {
				$last_five_pages_query->the_post();
				$site_pages[] = [
					'title' => get_the_title(),
					'link'  => get_edit_post_link( get_the_ID(), 'admin' ) . $edit_with_elementor,
					'icon'  => 'PagesIcon',
				];
			}
		}

		$general = [
			[
				'title' => __( 'Add New Page', 'hello-biz' ),
				'link'  => self_admin_url( 'post-new.php?post_type=page' ),
				'icon'  => 'PageTypeIcon',
			],
			[
				'title' => __( 'Settings', 'hello-biz' ),
				'link'  => self_admin_url( 'admin.php?page=hello-plus-settings' ),
			],
		];

		$common_parts = [];

		$customizer_header_footer_url = self_admin_url( 'customize.php?autofocus[section]=hello-biz-options' );

		$header_part = [
			'id'           => 'header',
			'title'        => __( 'Header', 'hello-biz' ),
			'link'         => $customizer_header_footer_url,
			'icon'         => 'HeaderTemplateIcon',
			'showSublinks' => true,
			'sublinks'     => [],
		];
		$footer_part = [
			'id'           => 'footer',
			'title'        => __( 'Footer', 'hello-biz' ),
			'link'         => $customizer_header_footer_url,
			'icon'         => 'FooterTemplateIcon',
			'showSublinks' => true,
			'sublinks'     => [],
		];

		if ( Utils::is_elementor_active() ) {
			$common_parts[] = array_merge(
				[
					'id' => 'theme-builder',
					'title' => __( 'Theme Builder', 'hello-biz' ),
					'icon' => 'ThemeBuilderIcon',
				],
				Utils::get_theme_builder_options()
			);

			if ( Utils::has_pro() ) {
				$header_part = $this->update_pro_part( $header_part, 'header' );
				$footer_part = $this->update_pro_part( $footer_part, 'footer' );
			}
		}

		$site_parts = [
			'siteParts' => array_merge(
				[
					$header_part,
					$footer_part,
				],
				$common_parts
			),
			'sitePages' => $site_pages,
			'general'   => $general,
		];

		$config['siteParts'] = apply_filters( 'hello-plus-theme/template-parts', $site_parts );

		return $this->get_quicklinks( $config, $elementor_page_id );
	}

	private function update_pro_part( array $part, string $location ): array {
		$theme_builder_module = \ElementorPro\Modules\ThemeBuilder\Module::instance();
		$conditions_manager   = $theme_builder_module->get_conditions_manager();

		$documents    = $conditions_manager->get_documents_for_location( $location );
		$add_new_link = \Elementor\Plugin::instance()->app->get_base_url() . '#/site-editor/templates/' . $location;
		if ( ! empty( $documents ) ) {
			$first_document_id    = array_key_first( $documents );
			$part['showSublinks'] = true;
			$part['sublinks']     = [
				[
					'title' => __( 'Edit', 'hello-biz' ),
					'link'  => get_edit_post_link( $first_document_id, 'admin' ) . '&action=elementor',
				],
				[
					'title' => __( 'Add New', 'hello-biz' ),
					'link'  => $add_new_link,
				],
			];
		} else {
			$part['link']         = $add_new_link;
			$part['showSublinks'] = false;
		}

		return $part;
	}

	public function get_open_homepage_with_tab( ?int $page_id, $action, $section = null, $customizer_fallback_args = [] ): string {
		if ( Utils::is_elementor_active() ) {
			$url = $page_id ? $this->get_elementor_editor_url( $page_id, $action ) : Page::get_site_settings_url_config( $action )['url'];

			if ( $section ) {
				$url = add_query_arg( 'active-section', $section, $url );
			}

			return $url;
		}

		return add_query_arg( $customizer_fallback_args, self_admin_url( 'customize.php' ) );
	}

	public function get_quicklinks( $config, ?int $elementor_page_id = null ): array {
		$config['quickLinks'] = [
			'site_name' => [
				'title' => __( 'Site Name', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( $elementor_page_id, 'settings-site-identity', null, [ 'autofocus[section]' => 'title_tagline' ] ),
				'icon'  => 'TextIcon',
			],
			'site_logo' => [
				'title' => __( 'Site Logo', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( $elementor_page_id, 'settings-site-identity', null, [ 'autofocus[section]' => 'title_tagline' ] ),
				'icon'  => 'PhotoIcon',
			],
			'site_favicon' => [
				'title' => __( 'Site Favicon', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( $elementor_page_id, 'settings-site-identity', null, [ 'autofocus[section]' => 'title_tagline' ] ),
				'icon'  => 'AppsIcon',
			],
		];

		if ( Utils::is_elementor_active() ) {
			$config['quickLinks']['site_colors'] = [
				'title' => __( 'Site Colors', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( $elementor_page_id, 'global-colors' ),
				'icon'  => 'BrushIcon',
			];

			$config['quickLinks']['site_fonts'] = [
				'title' => __( 'Site Fonts', 'hello-biz' ),
				'link'  => $this->get_open_homepage_with_tab( $elementor_page_id, 'global-typography' ),
				'icon'  => 'UnderlineIcon',
			];
		}

		return $config;
	}

	public function get_welcome_box_config( array $config ): array {
		$state_provider = new Banner_State_Provider();
		$banner_config = new Welcome_Banner_Config( $state_provider );
		$config['welcome'] = $banner_config->get_complete_config();

		return $config;
	}
}
