<div class="col-md-6 col-lg-4">
    <div class="post-container">
        <a href="<?php echo get_permalink(); ?>">
            <div class="image">
                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php echo get_the_title(); ?>">
            </div>
            <div class="content">
                <h5><?php echo get_the_title(); ?></h5>
                <span class="title-border"></span>
                <p class="meta">
                    <span><?php echo get_the_date('M j, Y'); ?></span> |
                    <?php
                    $categories = get_the_category();
                    if (!empty($categories)) {
                        $count = 0;
                        foreach ($categories as $category) {
                            echo '<span>'.$category->name.'</span>';

                            $count++;
                            if ($count >= 3) {
                                break;
                            }

                            if ($count < count($categories) && $count < 3) {
                                echo ', ';
                            }
                        }
                    }
                    ?>
                </p>
                <p class="description"><?php echo get_the_excerpt(); ?></p>
            </div>
        </a>
    </div>
</div>