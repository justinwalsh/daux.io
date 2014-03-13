<?php

    //  Generate Static Documentation
    function generate_static($out_dir) {
        global $tree, $base, $docs_path, $output_path, $options, $mode, $multilanguage, $output_language;
        $mode = 'Static';
        if ($out_dir === '') $output_path = $base . '/static';
        else {
            if (substr($out_dir, 0, 1) !== '/') $output_path = $base . '/' . $out_dir;
            else $output_path = $out_dir;
        }
        clean_copy_assets($output_path);
        build_tree();
        if (!$multilanguage) generate_static_branch($tree, '');
        else
            foreach ($options['languages'] as $languageKey => $language) {
                $output_language = $languageKey;
                generate_static_branch($tree[$languageKey], $languageKey);
            }
        $index = $docs_path . '/index.md';
        if (is_file($index)) {
            $index = generate_page($index);
            file_put_contents($output_path . '/index.html', $index);
        }
    }

    //  Generate Static Content For Given Directory
    function generate_static_branch($tree, $current_dir) {
        global $docs_path, $output_path;
        $p = $output_path;
        if ($current_dir !== '') {
            $p .= '/' . clean_url($current_dir);
            $current_dir .= '/';
        }
        if (!is_dir($p)) @mkdir($p);
        foreach ($tree as $key => $node)
            if (is_array($node)) generate_static_branch($node, $current_dir . $key);
            else {
                $html = generate_page($docs_path . '/' . $current_dir . $node);
                file_put_contents($p . "/" . clean_url($node), $html);
            }
    }

    //  Rmdir
    function clean_directory($dir) {
        $it = new RecursiveDirectoryIterator($dir);
        $files = new RecursiveIteratorIterator($it,
                     RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') continue;
            if ($file->isDir()) rmdir($file->getRealPath());
            else unlink($file->getRealPath());
        }
    }

    //  Copy Local Assets
    function clean_copy_assets($path){
        @mkdir($path);
        $options = $GLOBALS["options"];
        //Clean
        clean_directory($path);
        //Copy assets
        $unnecessaryImgs = array('./img/favicon.png', './img/favicon-blue.png', './img/favicon-green.png', './img/favicon-navy.png', './img/favicon-red.png');
        $unnecessaryJs = array();
        if ($options['colors']) {
            $unnecessaryLess = array('./less/daux-blue.less', './less/daux-green.less', './less/daux-navy.less', './less/daux-red.less');
            copy_recursive('./less', $path.'/', $unnecessaryLess);
            $unnecessaryImgs = array_diff($unnecessaryImgs, array('./img/favicon.png'));
        } else {
            $unnecessaryJs = array('./js/less.min.js');
            @mkdir($path.'/css');
            @copy('./css/daux-'.$options['theme'].'.min.css', $path.'/css/daux-'.$options['theme'].'.min.css');
            $unnecessaryImgs = array_diff($unnecessaryImgs, array('./img/favicon-'.$options['theme'].'.png'));
        }
        copy_recursive('./img', $path.'/', $unnecessaryImgs);
        copy_recursive('./js', $path.'/', $unnecessaryJs);
    }


    //  Copy Recursive
    function copy_recursive($source, $dest, $ignoreList = array()) {
        $src_folder=str_replace(array('.','/'), '', $source);
        @mkdir($dest . '/' . $src_folder);
        foreach (
            $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) @mkdir($dest . '/' . $src_folder . '/' .$iterator->getSubPathName());
            else if (!$ignoreList || !in_array($item, $ignoreList)) @copy($item, $dest . '/' .$src_folder. '/' .$iterator->getSubPathName());
        }
    }

?>