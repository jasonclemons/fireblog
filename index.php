<?php
/**
 * FireBlog v0.1
 *
 * @author Jason Clemons <jason@clemons.io>
 * @license MIT
 * @copyright 2020
 */

init();

/**
 * Init
 */
function init() {
	// Load our language strings
	load_language();

	// Get the url parameters.
	$uri = parse_url($_SERVER['REQUEST_URI']);
	$uri_parts = explode('/', $uri['path']);
	$parts = explode('/', $_SERVER['REQUEST_URI']);
	$dir = $_SERVER['SERVER_NAME'];
	for ($i = 0; $i < count($parts) - 1; $i++) {
		$dir .= $parts[$i] . "/";
	}

	// See if our posts dir exists
	// Create it if not
	if (!file_exists('posts')) {
		mkdir('posts') or die($language['errors']['permissions']['posts']);
	}

	// Create an .htaccess file if one doesn't exist
	if (!file_exists('.htaccess')) {
		$htaccess = fopen('.htaccess', 'w') or die($language['errors']['permissions']['htaccess']);
		$htaccess_data = "<IfModule mod_rewrite.c>\n\tRewriteEngine On\n\tRewriteCond %{REQUEST_FILENAME} !-f\n\tRewriteCond %{REQUEST_FILENAME} !-d\n\tRewriteCond %{REQUEST_FILENAME} !-l\n\tRewriteRule ^(.*)$ index.php?/$1 [L,QSA]\n</IfModule>";
		fwrite($htaccess, $htaccess_data);
		fclose($htaccess);
	}

	// Check for our config file
	// Create one if it doesn't exist
	if (!file_exists('config.php')) {
		$config_options = array(
			'blog_name' => null,
			'blog_url' => null,
			'blog_author' => null,
			'blog_passwd' => null,
			'blog_style' => null
		);
		create_config($config_options);
		header('Location: ' . dirname($_SERVER['REQUEST_URI']));
	} else {
		include 'config.php';
		// If config file is empty, send them to setup
		if ($config['blog_name'] == '') {
			header('Location: config');
		}
	}
}

function _page_config() {

}

function _page_post() {

}

function _page_update() {

}

function _page_destroy() {

}

/**
 * Create config file
 *
 * @param array $config_options
 */
function create_config($config_options) {
	// Parse out the options into text
	$config_data = "<?php\n";
	// Loop thru each config option
	foreach ($config_options as $key => $val) {
		$config_data .= "\$config['" . $key . "'] = '" . $val . "';\n";
	}
	$config_data .= "?>";
	// Write to file
	$config = fopen('config.php', 'w') or die($language['errors']['permissions']['config']);
	fwrite($config, $config_data);
	fclose($config);
}

/**
 * Sanitize our user input
 *
 * @param string $input
 */
function sanitize_input($input) {
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlentities($input, ENT_QUOTES);

	return $input;
}

/**
 * Create URL slug
 *
 * @param string $slug
 */
function create_slug($slug) {
	$slug = strtolower($slug);
	$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $slug);

    return $slug;
}

/**
 * Load up all language strings
 */
function load_language() {
	$language['errors']['permissions']['posts'] = 'Unable to create /posts directory. Make sure your root directory has the correct permissions (755).';
	$language['errors']['permissions']['htaccess'] = 'Unable to create .htaccess file. Make sure your root directory has correct permissions, and index.php is chmod 755.';
	$language['errors']['permissions']['config'] = 'Unable to create config.php file. Make sure your root directory has correct permissions, and index.php is chmod 755.';
}
?>
