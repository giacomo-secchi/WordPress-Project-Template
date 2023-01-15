<?php
/**
 * Plugin Name: My Custom Functions
 * Plugin URI: http://yoursite.com
 * Description: This is an awesome custom plugin with functionality that I'd like to keep when switching things.
 * Author: Giacomo Secchi
 * Author URI: http://yoursite.com
 * Version: 0.1.0
 */

/* Place custom code below this line. */

require_once dirname(__DIR__) . '/../../vendor/autoload.php';

include( plugin_dir_path( __FILE__ ) . 'custom.php' );

function mcf_is_development_environment() {
    return in_array( wp_get_environment_type(), array( 'development', 'local' ), true );
}

/**
 * Temporarily disable background updates Site Health test
 * @link https://wordpress.stackexchange.com/questions/370902/wordpress-site-health-status-trust-it-or-myself-for-its-security-advice
 *
 * @param [type] $tests
 * @return void
 */
function remove_background_updates_test( $tests ) {
    unset( $tests['async']['background_updates'] );
    return $tests;
}
add_filter( 'site_status_tests', 'remove_background_updates_test' );


function get_the_user_ip() {

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
    
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return apply_filters( 'wpb_get_ip', $ip );
    
}

function mcf_maintenance() {
    global $pagenow;

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return;
    }
    
    if(
        defined( 'IN_MAINTENANCE' )
        && IN_MAINTENANCE === 'ON'
        && get_the_user_ip() !== JETPACK_IP_ADDRESS_OK
        && ! in_array( $pagenow, [ 'wp-login.php', 'gitautodeploy.php' ]  )
        && ! is_user_logged_in()
    ) {
 
        if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
            require_once WP_CONTENT_DIR . '/maintenance.php';
            die();
        }
    
        require_once ABSPATH . WPINC . '/functions.php';
        wp_load_translations_early();
    
        header( 'Retry-After: 600' );
    
        wp_die(
            __( 'Briefly unavailable for scheduled maintenance.' ),
            __( 'Maintenance' ),
            503
        );
    }
}
add_action( 'init', 'mcf_maintenance' );

function check_some_other_plugin() {
    if ( is_plugin_active( 'duracelltomi-google-tag-manager/duracelltomi-google-tag-manager-for-wordpress.php' ) ) {
        if ( isset(  $_ENV['GTM_CONTAINER_ID'] ) ) {
            define( 'GTM4WP_HARDCODED_GTM_ID', $_ENV['GTM_CONTAINER_ID'] );
        }
        if ( isset(  $_ENV['GTM_ENV_AUTH']  )) {
            define( 'GTM4WP_HARDCODED_GTM_ENV_AUTH', $_ENV['GTM_ENV_AUTH'] );
        }
        if ( isset(  $_ENV['GTM_ENV_PREVIEW']  ) ) {
            define( 'GTM4WP_HARDCODED_GTM_ENV_PREVIEW', $_ENV['GTM_ENV_PREVIEW'] );
        }
    }
}
add_action( 'admin_init', 'check_some_other_plugin' );

 
function mcf_GTM_noscript_container_code() {
    if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); }

}

add_filter('wp_body_open', 'mcf_GTM_noscript_container_code');

// Remove query string from static CSS files
function mcf_remove_query_string_from_static_files( $src ) {
     

    if ( mcf_is_development_environment() ) {
        if( strpos( $src, '?ver=' ) ) {
            $src = remove_query_arg( 'ver', $src );
        }
    }

    return $src;
}
add_filter( 'style_loader_src', 'mcf_remove_query_string_from_static_files', 10, 2 );
add_filter( 'script_loader_src', 'mcf_remove_query_string_from_static_files', 10, 2 );



// Disable Heartbeat
// https://it.siteground.com/tutorial/wordpress/limitare-heartbeat/
function mcf_stop_heartbeat() {
    wp_deregister_script('heartbeat');
}
add_action( 'init', 'mcf_stop_heartbeat', 1 );
  
 
/**
 * 
 * Admin Login Screen
 */

function mcf_custom_loginlogo() {
    if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
         
        echo '<style>
        h1 a {
            background-image:url( ' . $image[0] . ') !important;
            width: 100% !important;
            background-size: contain !important;
        }
        </style>';
    }

}
add_action( 'login_head', 'mcf_custom_loginlogo' );
    
function mcf_custom_loginlogo_url( $url ) {
    return esc_url( home_url( '/' ) );
}
add_filter( 'login_headerurl', 'mcf_custom_loginlogo_url' );
    
     
function mcf_custom_login_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'mcf_custom_login_title' );
add_filter( 'login_title', 'mcf_custom_login_title' );

function wp_remove_version() {
    return '';
}
add_filter( 'the_generator', 'wp_remove_version' );

function mcf_remove_marker( $filename, $marker ) {
	if (!file_exists( $filename ) ) {
		if (!file_exists( $filename ) ) {
			return '';
		} else {
			$markerdata = explode( "\n", implode( '', file( $filename ) ) );
		}

		$f = fopen( $filename, 'w' );
		if ( $markerdata ) {
			$state = true;
			foreach ( $markerdata as $n => $markerline ) {
				if (strpos($markerline, '# BEGIN ' . $marker) !== false)
					$state = false;
				if ( $state ) {
					if ( $n + 1 < count( $markerdata ) )
						fwrite( $f, "{$markerline}\n" );
					else
						fwrite( $f, "{$markerline}" );
				}
				if (strpos($markerline, '# END ' . $marker) !== false) {
					$state = true;
				}
			}
		}
		return true;
	} else {
		return false;
	}
}





function mcf_update_htaccess( $rules ) {

    $dir = dirname( ABSPATH );

    $base = preg_replace('#^w{3}\.(.+\.)#i', '$1', $_SERVER['SERVER_NAME']);

    $home_path     = get_home_path() . '/../';
	$htaccess_file = $home_path . '.htaccess';

     $custom_rules = <<<EOF


# redirect to not found pages
ErrorDocument 404 /wordpress/index.php?error=404


# Disable directory browsing
Options All -Indexes

# <Files wp-login.php>
# AuthUserFile {$dir}/.htpasswd
# AuthType Basic
# AuthName "Accesso ad area riservata"
# Order Deny,Allow
# Deny from all
# Require valid-user
# Satisfy any
# </Files>
    
<IfModule mod_headers.c>
Header always set Strict-Transport-Security: "max-age=31536000" env=HTTPS 
Header always set Content-Security-Policy "upgrade-insecure-requests" 
Header always set X-Content-Type-Options "nosniff" 
Header always set Expect-CT "max-age=7776000, enforce" 
Header always set Referrer-Policy: "no-referrer-when-downgrade" 
</IfModule>

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{HTTP_HOST} ^(www.)?{$base}$
RewriteCond %{REQUEST_URI} !^/wordpress/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /wordpress/$1
RewriteCond %{HTTP_HOST} ^(www.)?{$base}$
RewriteRule ^(/)?$ wordpress/index.php [L]
</IfModule>

# Deny access to all .htaccess files
<Files ~ "^.*\.([Hh][Tt][Aa])">
order allow,deny
deny from all
satisfy all
</Files>

 

<files ~ "(wp-config.php|.env|.env.example|README.md|wp-cli.yml|readme.html)$">
order allow,deny
deny from all
</files>


EOF;


$content_folder_custom_rules = <<<EOF
# Disable access to all file types except the following
Order deny,allow
Deny from all 
<Files ~ ".(xml|css|js|jpg|jpeg|png|gif|pdf|doc|docx|rtf|odf|zip|rar|svg|eot|ttf|woff|woff2|xls|mp3|flv|swf|kmz|ico)$">
Allow from all
</Files> 
EOF;
    
    mcf_remove_marker( $htaccess_file, 'WordPress Custom' ); // remove original WP rules so SuperCache rules go on top
    mcf_remove_marker( 'wp-content/.htaccess', 'WordPress Custom' ); // remove original WP rules so SuperCache rules go on top

    
    insert_with_markers( $htaccess_file, 'WordPress Custom', $custom_rules ); 
    insert_with_markers( 'wp-content/.htaccess', 'WordPress Custom', $content_folder_custom_rules ); 
    
    return $rules;
  
}

add_filter('mod_rewrite_rules', 'mcf_update_htaccess');

// Allow SVG
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

    global $wp_version;
    if ( $wp_version !== '4.7.1' ) {
       return $data;
    }
  
    $filetype = wp_check_filetype( $filename, $mimes );
  
    return [
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    ];
  
  }, 10, 4 );
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
   }
add_filter('upload_mimes', 'cc_mime_types');


/* Place custom code above this line. */
?>
