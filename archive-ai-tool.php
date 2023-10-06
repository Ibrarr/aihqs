<?php
/*
Template Name: Ai Tools Archive
*/
get_header(); ?>

    <div class="container">
        <div id="tools-filter">
            <input type="text" id="search" placeholder="Search">

            <select id="sort">
                <option value="date">Date Added</option>
                <option value="title">Alphabetical</option>
            </select>

            <select id="category">
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'category',
                    'hide_empty' => true,
                ));

                echo '<option value="">All Categories</option>';

                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
        </div>

        <div id="posts-container">
            <!-- Posts will be loaded here -->
        </div>
    </div><!-- #container -->

<?php get_footer(); ?>