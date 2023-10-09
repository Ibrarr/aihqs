<?php
add_action('wp_enqueue_scripts', 'enqueue_styles_scripts');
function enqueue_styles_scripts()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri() . '/style.css');

    wp_enqueue_style('site', get_stylesheet_directory_uri().'/dist/css/app.css', [], filemtime(  get_stylesheet_directory().'/dist/css/app.css' ), 'all');
    wp_enqueue_style('selectize', get_stylesheet_directory_uri().'/dist/css/selectize.css', [], filemtime(  get_stylesheet_directory().'/dist/css/selectize.css' ), 'all');

    wp_enqueue_script('site', get_stylesheet_directory_uri().'/dist/js/app.js', ['jquery'], filemtime(  get_stylesheet_directory().'/dist/js/app.js' ), true);
    wp_enqueue_script('selectize', get_stylesheet_directory_uri().'/dist/js/selectize.js', [], filemtime(  get_stylesheet_directory().'/dist/js/selectize.js' ), true);

    wp_localize_script('site', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
}

/**
 * Remove file editing from admin
 */
define( 'DISALLOW_FILE_EDIT', true );

/**
 * Remove yasr metabox on admin pages
 */
add_action('add_meta_boxes', 'plt_hide_yet_another_stars_rating_metaboxes', 20);
function plt_hide_yet_another_stars_rating_metaboxes() {
    $screen = get_current_screen();
    if ( !$screen ) {
        return;
    }

    //Hide the "Yet Another Stars Rating" meta box.
    remove_meta_box('yasr_metabox_below_editor', $screen->id, 'normal');
}

/**
 * Remove project post type
 */
add_filter( 'et_project_posttype_args', 'mytheme_et_project_posttype_args', 10, 1 );
function mytheme_et_project_posttype_args( $args ) {
    return array_merge( $args, array(
        'public'              => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => false,
        'show_in_nav_menus'   => false,
        'show_ui'             => false
    ));
}

/**
 * Remove comments
 */
add_action( 'admin_menu', 'prefix_remove_comments_tl' );
function prefix_remove_comments_tl() {
    remove_menu_page( 'edit-comments.php' );
}

/**
 * Add Tools post type
 */
add_action( 'init', function() {
    register_post_type( 'ai-tool', array(
        'labels' => array(
            'name' => 'AI Tools',
            'singular_name' => 'AI Tool',
            'menu_name' => 'Tools',
            'all_items' => 'All Tools',
            'edit_item' => 'Edit Tool',
            'view_item' => 'View Tool',
            'view_items' => 'View Tools',
            'add_new_item' => 'Add New Tool',
            'new_item' => 'New Tool',
            'parent_item_colon' => 'Parent Tool:',
            'search_items' => 'Search Tools',
            'not_found' => 'No ai-tool found',
            'not_found_in_trash' => 'No ai-tool found in the bin',
            'archives' => 'Tool Archives',
            'attributes' => 'Tool Attributes',
            'insert_into_item' => 'Insert into tool',
            'uploaded_to_this_item' => 'Uploaded to this tool',
            'filter_items_list' => 'Filter ai-tool list',
            'filter_by_date' => 'Filter ai-tool by date',
            'items_list_navigation' => 'Tools list navigation',
            'items_list' => 'Tools list',
            'item_published' => 'Tool published.',
            'item_published_privately' => 'Tool published privately.',
            'item_reverted_to_draft' => 'Tool reverted to draft.',
            'item_scheduled' => 'Tool scheduled.',
            'item_updated' => 'Tool updated.',
            'item_link' => 'Tool Link',
            'item_link_description' => 'A link to a tool.',
        ),
        'public' => true,
        'has_archive' => 'ai-ai-tool',
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-list-view',
        'rewrite' => array(
            'slug' => 'ai-tool',
            'with_front' => false
        ),
        'supports' => array(
            0 => 'title',
            1 => 'author',
            2 => 'editor',
            3 => 'thumbnail',
        ),
        'taxonomies' => array(
            0 => 'category',
        ),
        'delete_with_user' => false,
    ) );
} );

/**
 * Fallback for excerpt for AI ai-tool
 */
add_filter( 'get_the_excerpt', function ( $excerpt, $post ) {
    if ( ! empty( $excerpt ) ) {
        return $excerpt;
    }

    if ( $post->post_type === 'ai-tool' ) {
        $excerpt = get_field( 'short_description', $post->ID );
    }

    return $excerpt;
}, 10, 2 );

/**
 * Ajax calls for directory page
 */
function load_posts() {
    $search = $_GET['search'];
    $sort = $_GET['sort'];
    $category = $_GET['category'];
    $pricing = $_GET['pricing'];

    $args = array(
        'post_type' => 'ai-tool',
        'posts_per_page' => 12,
        's' => $search,
        'orderby' => $sort === 'title' ? 'title' : 'date',
        'order' => $sort === 'title' ? 'ASC' : 'DESC',
    );

    if ($category) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $category,
            ),
        );
    }

    if ($pricing) {
        $args['meta_query'] = array(
            array(
                'key' => 'pricing',
                'value' => $pricing,
                'compare' => '=',
            ),
        );
    }

    $query = new WP_Query($args);

    while ($query->have_posts()) {
        $query->the_post();
        get_template_part('template-parts/ai-tool/archive', 'post');
    }

    die();
}
add_action('wp_ajax_load_posts', 'load_posts');
add_action('wp_ajax_nopriv_load_posts', 'load_posts');
