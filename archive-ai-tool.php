<?php
/*
Template Name: Ai Tools Archive
*/
get_header(); ?>

    <div class="container">
        <div id="ai-tool-filter">
            <div class="row">
                <div class="search col-md-8">
                    <input type="text" id="search" placeholder="Find an AI tool">
                </div>
                <div class="col-md-4">
                    <div class="custom-select">
                        <select id="sort">
                            <option value="">Date Added</option>
                            <option value="date">Date Added</option>
                            <option value="title">Alphabetical</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mt-3">
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
                <div class="col-md-6 mt-3">
                    <div class="custom-select">
                        <select id="pricing">
                            <?php
                            $field = get_field_object('field_651fe248086d7');
                            $choices = $field['choices'];

                            echo '<option value="">All Pricing</option>';

                            if ($choices) {
                                foreach ($choices as $value => $label) {
                                    echo '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5" id="posts-container">
            <!-- Posts will be loaded here -->
        </div>
    </div><!-- #container -->

<?php get_footer(); ?>