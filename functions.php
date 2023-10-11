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
        'has_archive' => 'ai-tools',
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
    $offset = $_GET['offset'];

    $args = array(
        'post_type' => 'ai-tool',
        'posts_per_page' => 12,
        'offset' => (int)$offset,
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

    if (!$query->have_posts()) {
        echo json_encode(array('count' => 0, 'html' => '<h3 class="no-results">No results found</h3>'));
        die();
    }

    $posts_html = '';
    while ($query->have_posts()) {
        $query->the_post();
        ob_start();
        get_template_part('template-parts/ai-tool/archive', 'post');
        $posts_html .= ob_get_clean();
    }

    echo json_encode(array('count' => $query->post_count, 'html' => $posts_html));
    die();
}
add_action('wp_ajax_load_posts', 'load_posts');
add_action('wp_ajax_nopriv_load_posts', 'load_posts');


/**
 * Direct all searches to tool directory
 */
add_action( 'template_redirect', 'wpb_change_search_url' );
function wpb_change_search_url() {
    if ( is_search() && ! empty( $_GET['s'] ) ) {
        wp_redirect( home_url( "/ai-tools/" ) . '?tool=' . urlencode( get_query_var( 's' ) ) );
        exit();
    }
}

/**
 * Recent tools
 */
add_shortcode( 'recent_ai_tool_posts', 'recent_ai_tool_posts_shortcode' );
function recent_ai_tool_posts_shortcode() {
    $args = array(
        'post_type'      => 'ai-tool',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        ?><div class="row g-5" id="homepage-tools"><?php
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part('template-parts/ai-tool/archive', 'post');
        }
        ?></div><?php
    } else {
        echo '<p>No Tools found</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Related tools
 */
add_shortcode( 'related_ai_tool_posts', 'related_ai_tool_posts_shortcode' );
function related_ai_tool_posts_shortcode() {
    global $post;
    $current_post_id = $post->ID;
    $categories = get_the_category($current_post_id);
    $first_category = $categories[0];

    $args = array(
        'post_type'      => 'ai-tool',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category__in' => array($first_category->term_id),
        'post__not_in' => array($current_post_id),
    );

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        ?><div class="row g-5" id="homepage-tools"><?php
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part('template-parts/ai-tool/archive', 'post');
        }
        ?></div><?php
    } else {
        echo '<p>No Tools found</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Related posts
 */
add_shortcode( 'related_posts', 'related_posts_shortcode' );
function related_posts_shortcode() {
    global $post;
    $current_post_id = $post->ID;
    $categories = get_the_category($current_post_id);
    $first_category = $categories[0];

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category__in' => array($first_category->term_id),
        'post__not_in' => array($current_post_id),
    );

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        ?><div class="row g-5" id="posts-grid-container"><?php
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part('template-parts/post/archive', 'post');
        }
        ?></div><?php
    } else {
        echo '<p>No posts found</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Recent posts
 */
add_shortcode( 'recent_posts', 'recent_posts_shortcode' );
function recent_posts_shortcode() {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        ?><div class="row g-5" id="posts-grid-container"><?php
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part('template-parts/post/archive', 'post');
        }
        ?></div><?php
    } else {
        echo '<p>No posts found</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Posts archive
 */
add_shortcode( 'posts_archive', 'posts_archive_shortcode' );
function posts_archive_shortcode() {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 12,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1
    );

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        ?><div class="row g-5" id="posts-grid-container"><?php
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part('template-parts/post/archive', 'post');
        }
        ?>
        </div>
        <div class="post-archive-pagination">
        <?php
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'mid_size' => 1,
            'prev_text' => '<span class="prev-icon"></span>',
            'next_text' => '<span class="next-icon"></span>',
        ));
        ?></div><?php
    } else {
        echo '<p>No posts found</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Category archive
 */
add_shortcode( 'category_archive', 'category_archive_shortcode' );
function category_archive_shortcode() {
    $category = get_query_var('category_name') ? get_query_var('category_name') : '';

    $args = array(
        'post_type' => array('ai-tool'),
        'posts_per_page' => 12,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    );

    if ($category) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        ?><div class="row g-5" id="posts-grid-container"><?php
        while ( $query->have_posts() ) {
            $query->the_post();
            if (get_post_type() == 'ai-tool') {
                get_template_part('template-parts/category/tool', 'post');
            } else {
                get_template_part('template-parts/category/regular', 'post');
            }
        }
        ?>
        </div>
        <div class="post-archive-pagination">
        <?php
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'mid_size' => 1,
            'prev_text' => '<span class="prev-icon"></span>',
            'next_text' => '<span class="next-icon"></span>',
        ));
        ?></div><?php
    } else {
        echo '<p>No posts found</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Monarch shortcode
 */
add_shortcode('et_social_share_custom', 'monarchShortcode');
function monarchShortcode(){
    $monarch = $GLOBALS['et_monarch'];
    $monarch_options = $monarch->monarch_options;
    return $monarch->generate_inline_icons('et_social_inline_custom');
}

/**
 * Save first category in post meta on save
 */
add_action( 'save_post', 'save_first_category_link_as_custom_field' );
function save_first_category_link_as_custom_field( $post_id ) {
    if ( wp_is_post_revision( $post_id ) )
        return;

    $categories = get_the_category( $post_id );

    if ( $categories && ! is_wp_error( $categories ) ) {
        $first_category = $categories[0];
        $first_category_link = get_category_link( $first_category->term_id );
        $link_html = '<a href="' . esc_url( $first_category_link ) . '">' . esc_html( $first_category->name ) . '</a>';
        update_post_meta( $post_id, 'first_category_name_and_link', $link_html );
    }
}