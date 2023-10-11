<?php get_header(); ?>

    <div class="container">
        <div id="ai-tool-filter">
            <div class="row">
                <div class="search col-md-8 col-sm-6">
                    <input type="text" id="search" placeholder="Find an AI tool">
                </div>
                <div class="col-md-4 col-sm-6 d-none d-sm-block mt-3 mt-sm-0 sort-filter">
                    <div class="custom-select">
                        <select id="sort">
                            <option value="">Date Added</option>
                            <option value="date">Date Added</option>
                            <option value="title">Alphabetical</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 mt-3">
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
                <div class="col-sm-6 d-none d-sm-block mt-3 pricing-filter">
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
                <div class="mt-3"><span id="clear-filters">Clear Filters</span></div>
            </div>
        </div>

        <div class="row g-5" id="posts-container" style="padding-top: 25px;">
            <!-- Posts will be loaded here -->
        </div>
        <div id="loading-indicator" style="display: none;">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div><!-- #container -->

<?php get_footer(); ?>