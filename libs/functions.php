<?
require_once('libs/markdown_extended.php');

function get_options() {
	$options = array(
		'title' => "Documentation",
		'homepage' => false,
		'theme' => 'spacelab',
		'hightlight' => 'github'
	);

	// Load User Config
	$config_file = './docs/config.json';
	if (file_exists($config_file)) {
		$config = json_decode(file_get_contents($config_file), true);
		$options = array_merge($options, $config);
	}

	// Homepage Redirect?
	$path = url_path();
	if ($path === '/') {
		// Custom Homepage?
		if ($options['homepage']) {
			header('Location: '.$options['homepage']);
		}
	}

	return $options;
}

function load_page($tree) {
	$branch = find_branch($tree);

	if (isset($branch['type']) && $branch['type'] == 'file') {
		$html = '<h1>'. $branch['title'] . '</h1>';
		$html .= MarkdownExtended(file_get_contents($branch['path']));
		return $html;
	} else {
		return "Oh No. That page dosn't exist";
	}
}

function find_branch($tree) {
	$path = url_params();
	foreach($path as $peice) {
		if (isset($tree[$peice])) {
			if ($tree[$peice]['type'] == 'folder') {
				$tree = $tree[$peice]['tree'];
			} else {
				$tree = $tree[$peice];
			}
		} else {
			return false;
		}
	}

	return $tree;
}

function url_path() {
	$url = parse_url($_SERVER['REQUEST_URI']);
	$url = $url['path'];
	return $url;
}

function url_params() {
	$url = url_path();
	$params = explode('/', trim($url, '/'));
	return $params;
}

function clean_sort($text) {
	// Remove .md file extension
	$text = str_replace('.md', '', $text);

	// Remove sort placeholder
	$parts = explode('_', $text);
	if (isset($parts[0]) && is_numeric($parts[0])) {
		unset($parts[0]);
	}
	$text = implode('_', $parts);

	return $text;
}

function clean_name($text) {
	$text = str_replace('_', ' ', $text);
	return $text;
}

function build_nav($tree, $url_params = false) {
	if (!is_array($url_params)) {
		$url_params = url_params();
	}
	$url_path = url_path();
	$html = '<ul class="nav nav-list">';
	foreach($tree as $key => $val) {
		// Active Tree Node
		if (isset($url_params[0]) && $url_params[0] == $val['clean']) {
			array_shift($url_params);

			// Final Node
			if ($url_path == $val['url']) {
				$html .= '<li class="active">';
			} else {
				$html .= '<li class="open">';
			}
		} else {
			$html .= '<li>';
		}

		if ($val['type'] == 'folder') {
			$html .= '<a href="#" class="aj-nav folder">'.$val['name'].'</a>';
			$html .= build_nav($val['tree'], $url_params);
		} else {
			$html .= '<a href="'.$val['url'].'">'.$val['name'].'</a>';
		}

		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}

function get_tree($path = '.', $clean_path = '', $title = ''){
	$tree = array();
    $ignore = array('config.json', 'cgi-bin', '.', '..');
    $dh = @opendir($path);
    $index = 0;

    // Loop through the directory
    while(false !== ($file = readdir($dh))){

     	// Check that this file is not to be ignored
        if(!in_array($file, $ignore)) {
        	$full_path = "$path/$file";
        	$clean_sort = clean_sort($file);
        	$url = $clean_path . '/' . $clean_sort;
        	$clean_name = clean_name($clean_sort);

        	// Title
        	if (empty($title)) {
        		$full_title = $clean_name;
        	} else {
        		$full_title = $title . ': ' . $clean_name;
        	}

            if(is_dir("$path/$file")) {
            	// Directory
            	$tree[$clean_sort] = array(
            		'type' => 'folder',
            		'name' => $clean_name,
            		'title' => $full_title,
            		'path' => $full_path,
            		'clean' => $clean_sort,
            		'url' => $url,
            		'tree'=> get_tree($full_path, $url, $full_title)
            	);
            } else {
            	// File
            	$tree[$clean_sort] = array(
            		'type' => 'file',
            		'name' => $clean_name,
            		'title' => $full_title,
            		'path' => $full_path,
            		'clean' => $clean_sort,
            		'url' => $url,
            	);
            }
        }
     	$index++;
    }

    // Close the directory handle
    closedir($dh);

    return $tree;
}
?>