<?php 

function filter_posts() {
    // Verify the nonce
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'filter_posts_nonce')) {
      die('Security check failed');
    }
  
    // Get the selected category and date from the AJAX request
    $category = $_POST['category'];
    $date = $_POST['date'];
  
    // Use WP_Query to retrieve the posts with the selected category and date
    $args = array(
      'post_type' => 'post',
      'posts_per_page' => -1 
      
    );
    if (!empty($category)) {
        $args['category_name'] = $category;
        }
        
        if (!empty($date)) {
        $args['date_query'] = array(
        array(
        'year' => substr($date, 0, 4)
        ),
        );
        }
        
        $the_query = new WP_Query($args);
        
        // Loop through the posts and display them in the container
        while ($the_query->have_posts()) : $the_query->the_post(); ?>
        <div class="post">
        <h2><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?><?php the_title(); ?></a></h2>
        <div class="entry-content">
        <?php 
            if ( has_excerpt() ) {
                echo get_the_excerpt();
            } else {
                echo wp_trim_words( get_the_content(), 20 );
            }
        ?>
        </div>
        <div class="post-categories"><?php the_category(', '); ?></div>
        </div>
        
          <?php endwhile;
        
          wp_reset_postdata();
        
    die();
}
add_action('wp_ajax_filter_posts', 'filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts');
