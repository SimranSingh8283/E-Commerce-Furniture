<?php
$custom_post_types_dir = get_template_directory() . '/inc/post-types/';
$post_type_files = glob($custom_post_types_dir . '*.php');

// Include top-level post type files
foreach ($post_type_files as $file) {
    require_once $file;
}

// Include subfolder files (like properties/PropertiesPostType.php)
$subfolders = glob($custom_post_types_dir . '*', GLOB_ONLYDIR);
foreach ($subfolders as $folder) {
    $loader = $folder . '/' . basename($folder) . 'PostType.php';
    if (file_exists($loader)) {
        require_once $loader;
    }
}

// Initialize classes dynamically
foreach (get_declared_classes() as $class) {
    if (strpos($class, 'ThemePostType') === 0 && method_exists($class, 'init')) {
        $class::init();
    }
}
