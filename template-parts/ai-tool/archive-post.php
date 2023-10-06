<?php

$categories = get_the_category();
$first_category = $categories[0];
$first_category_name = $first_category->name;
?>
<div class="col-md-6 col-lg-4">
    <div class="ai-tool-container">
        <a href="<?php echo get_permalink(); ?>">
            <div class="image">
                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php echo get_the_title(); ?>">
                <p class="category"><?php echo $first_category_name ?></p>
            </div>
            <div class="content">
                <h5><?php echo get_the_title(); ?></h5>
                <p class="description"><?php the_field('short_description'); ?></p>
                <p class="pricing"><?php the_field('pricing'); ?></p>
            </div>
        </a>
    </div>
</div>