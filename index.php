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
 *
 * @return void
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

	// Set up our valid endpoints
	$endpoints = array(
		'post',
		'destroy',
		'edit',
		'setup',
		'update'
	);

	// Figure out the page title
	if (count($uri_parts) >= 1 && $uri_parts[0]) {
		$page_title = ucwords($uri_parts[0]);
	} else if (count($uri_parts) = 2) {
		$page_title = null; //$post_data['title'];
	} else {
		$page_title = 'Index'; //langstring
	}

	// Figure out what template to show
	if (count($uri_parts) >= 1 && $uri_parts[0]) {
		if (in_array($uri_parts[0], $endpoints)) {
			$template = '_page_' . $uri_parts[0];
			_page_template_above();
			$template();
			_page_template_below();
		} else {
			header('Location: /');
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

function _page_edit() {

}

function _page_template_above() {
	echo '<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>' . $config['blog_name'] . ' | ' . $page_title . '</title>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.5/examples/blog/blog.css" rel="stylesheet">
  </head>
  <body>';
}

function _page_template_below() {
	echo '<footer class="blog-footer">
  <p>Blog template built for <a href="https://getbootstrap.com/">Bootstrap</a> by <a href="https://twitter.com/mdo">@mdo</a>.</p>
  <p>
    <a href="#">Back to top</a>
  </p>
</footer>
</body>
</html>';
}

/**
 * Create config file
 *
 * @param array $config_options
 * @return void
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
 * @return string
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
 * @return string
 */
function create_slug($slug) {
	$slug = strtolower($slug);
	$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $slug);

    return $slug;
}

/**
 * Load up all language strings
 *
 * @return void
 */
function load_language() {
	$language['errors']['permissions']['posts'] = 'Unable to create /posts directory. Make sure your root directory has the correct permissions (755).';
	$language['errors']['permissions']['htaccess'] = 'Unable to create .htaccess file. Make sure your root directory has correct permissions, and index.php is chmod 755.';
	$language['errors']['permissions']['config'] = 'Unable to create config.php file. Make sure your root directory has correct permissions, and index.php is chmod 755.';
}
?>
