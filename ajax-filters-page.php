<?php get_header(); /* Template Name: Ajax Filter */
?>

<!-- Ajax Filters Content -->
<div class="container py-4 posts-list">
  <div class="ajax-title border-bottom mb-3">
    <?php the_title( '<h1>', '</h1>' ); ?>
  </div>
  <div class="row">
    <div class="col-lg-3 mb-3">
      <div id="filter-controls" >
        <label class="mb-1"><h5>Filter by Category:</h5></label>
        <?php
        // Get all categories
        $categories = get_categories();
    
        // Display a select dropdown for categories
        echo '<select id="category-filter">';
        echo '<option value="">All Categories</option>';
        foreach ($categories as $category) {
          echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
        }
        echo '</select>';
        ?>
        <label class="mb-1"><h5>Filter by Date:</h5></label>
        <select id="date-filter">
          <option value="">All Dates</option>
          <?php
          // Get all distinct dates of published posts
          global $wpdb;
          $dates = $wpdb->get_results("
            SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
            FROM $wpdb->posts
            WHERE post_type = 'post' AND post_status = 'publish'
            ORDER BY post_date DESC
          ");

          // Display a select dropdown for dates
          foreach ($dates as $date) {
            $date_string = $date->year . '-' . str_pad($date->month, 2, '0', STR_PAD_LEFT);
            echo '<option value="' . $date_string . '">' . date('Y', strtotime($date_string . '-01')) . '</option>';
          }
          ?>
  </select>
      </div>
    </div>
    <div class="col-lg">
        <div id="posts-container">
        <?php
        // Use WP_Query to retrieve the posts
        $args = array(
          'post_type' => 'post',
          'posts_per_page' => -1,
        );
      
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
        <?php endwhile; ?>
      </div>
    </div>
    </div>
</div>
  
  


<!-- Add JavaScript to handle AJAX filtering here -->
<script>
  jQuery(document).ready(function($) {
  var nonce = '<?php echo wp_create_nonce('filter_posts_nonce'); ?>';

  $('#category-filter, #date-filter').change(function() {
    var category = $('#category-filter').val();
    var date = $('#date-filter').val();

    $.ajax({
      url: '<?php echo admin_url('admin-ajax.php'); ?>',
      type: 'POST',
      data: {
        action: 'filter_posts',
        category: category,
        date: date,
        nonce: nonce,
      },
      success: function(response) {
        $('#posts-container').html(response);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });
  });
});
</script>


<?php get_footer(); ?>
