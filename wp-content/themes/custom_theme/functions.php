<?php
/**
 * functions.php
 *
 * Child theme functions.
 */



if ( ! function_exists( 'custom_theme_scripts' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function custom_theme_scripts() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'custom-theme-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( 'twentytwentytwo-style' ),
			$version_string
		);

		wp_register_style( 
			'custom-theme-woocommerce',
			get_stylesheet_directory_uri() . '/assets/css/plugins/woocommerce/woocommerce.css',
			array( 'woocommerce-general' ), 
			null
		);

		wp_register_style( 
			'custom-theme-jetpack-block-mailchimp',
			get_stylesheet_directory_uri() . '/assets/css/plugins/jetpack/blocks/mailchimp.css',
			array( 'jetpack-block-mailchimp' ), 
			$version_string
		);
		

		wp_register_style( 
			'custom-theme-jetpack-block-slideshow',
			get_stylesheet_directory_uri() . '/assets/css/plugins/jetpack/blocks/slideshow.css',
			array( 'jetpack-block-slideshow' ), 
			null
		);


		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && is_singular() ) {
			wp_enqueue_style( 
				'custom-theme-yoast-reading-time',
				get_stylesheet_directory_uri() . '/assets/css/plugins/yoast/blocks/reading-time.css',
				array(), 
				null
			);
		}


		// Enqueue theme stylesheet.
		wp_enqueue_style( 'custom-theme-style' );

		
		// Enqueue WooCommerce Styles
		wp_enqueue_style( 'custom-theme-woocommerce' );


		// Enqueue Jetpack Mailchimp block 
		wp_enqueue_style( 'custom-theme-jetpack-block-mailchimp' );


		// Enqueue Jetpack Mailchimp block 
		wp_enqueue_style( array ('custom-theme-jetpack-block-slideshow' ) );
		
		
		// Enqueue Google fonts
		wp_enqueue_style( 'custom-theme-fonts', custom_theme_fonts_url(), array(), null );

		wp_enqueue_script(
			'custom-theme-accordion',
			get_stylesheet_directory_uri() . '/assets/js/accordion.js',
			array( 'jquery' ),
			$version_string
		);

	
	}

endif;

add_action( 'wp_enqueue_scripts', 'custom_theme_scripts' );


/**
 * Add Google webfonts
 *
 * @return $fonts_url
 */

function custom_theme_fonts_url() {
	if ( ! class_exists( 'WP_Theme_JSON_Resolver' ) ) {
		return '';
	}

	$theme_data = WP_Theme_JSON_Resolver::get_merged_data()->get_settings();
	if ( empty( $theme_data ) || empty( $theme_data['typography'] ) || empty( $theme_data['typography']['fontFamilies'] ) ) {
		return '';
	}

	$font_families = [];
	if ( ! empty( $theme_data['typography']['fontFamilies']['custom'] ) ) {
		foreach( $theme_data['typography']['fontFamilies']['custom'] as $font ) {
			if ( ! empty( $font['google'] ) ) {
				$font_families[] = $font['google'];
			}
		}

	// NOTE: This should be removed once Gutenberg 12.1 lands stably in all environments
	} else if ( ! empty( $theme_data['typography']['fontFamilies']['user'] ) ) {
		foreach( $theme_data['typography']['fontFamilies']['user'] as $font ) {
			if ( ! empty( $font['google'] ) ) {
				$font_families[] = $font['google'];
			}
		}
	// End Gutenberg < 12.1 compatibility patch

	} else {
		if ( ! empty( $theme_data['typography']['fontFamilies']['theme'] ) ) {
			foreach( $theme_data['typography']['fontFamilies']['theme'] as $font ) {
				if ( ! empty( $font['google'] ) ) {
					$font_families[] = $font['google'];
				}
			}
		}
	}

	if ( empty( $font_families ) ) {
		return '';
	}

	// Make a single request for the theme or user fonts.
	return esc_url_raw( 'https://fonts.googleapis.com/css2?' . implode( '&', array_unique( $font_families ) ) . '&display=swap' );
}

 
