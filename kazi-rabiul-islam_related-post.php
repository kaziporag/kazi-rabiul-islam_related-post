<?php
/**
 * Plugin Name:       Kazi Rabiul Islam Related Post
 * Plugin URI:        https://wordpress.org/plugins/kazi-rabiul-islam_related-post
 * Description:       Kazi Rabiul Islam Related Post will desplay related posts with same category and rendom posts.
 * Version:           1.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Kazi Rabiul Islam
 * Author URI:        https://wordpress.org/plugins/kazi-rabiul-islam_related-post
 * Text Domain:       kazi-rabiul-islam_related-post
 */

 include_once(ABSPATH . 'wp-admin/includes/plugin.php'); // Include the plugin file

 class KaziRelatedPost {

    public function __construct() {
        add_action('init', array($this, 'initialize')); // Initialize the plugin
    }

    public function initialize() {
        add_filter('the_content',[$this,'display_related_post'], 10, 9); // Display the related post with same category
        add_action('wp_enqueue_scripts', [$this, 'frontend_assets']); // Load the assets
    }

    public function display_related_post( $content ){
        if( is_single() ) {
            
            $content .= '<h2>'. esc_attr( 'Related Post', 'kazi-rabiul-islam_related-post' ) . '</h2>';
            $content .= '<div class="related-post-wrapper">';

            $related_posts = get_the_terms( get_the_id(), 'category' ); // Get the related post

            $rel_array = array(); // array of related post id 

            foreach ($related_posts as $key => $value) {
                $rel_array[] = $value->term_id; // Rtore the related post id in the array
            }

            $args = array(
                'post_type' => 'post', // Post type
                'post__not_in' => array(get_the_id()), // Exclude the current post
                'posts_per_page' => 3, // Number of related post
                'orderby' => 'rand',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category', // Taxonomy
                        'field' => 'id', // Field
                        'terms' => $rel_array // Terms
                    )
                )
            );
            
            $query_related_post = new WP_Query($args); // Query related post with same category

            if ($query_related_post->have_posts()) {
                while ($query_related_post->have_posts()) {
                    
                    $query_related_post->the_post(); // Get the related post
                    
                    $content .= '<div class="related-post">';
                    $content .= wp_kses_post( get_the_post_thumbnail(), 'kazi-rabiul-islam_related-post' );
                    $content .= '<ul class="related-post-meta">';
                    $content .= '<li><strong><a href="' . esc_url( get_permalink(), 'kazi-rabiul-islam_related-post' ) . '">' . esc_attr( get_the_title(), 'kazi-rabiul-islam_related-post' ) . '</a></strong></li>';
                    $content .= '<li>' . esc_attr( wp_trim_words(get_the_content(), 6), 'kazi-rabiul-islam_related-post' ) . '</li>';
                    $content .= '<li><small>' . esc_attr( get_the_date(), 'kazi-rabiul-islam_related-post' )  . '</small></li>';
                    $content .= '<li><i><a href="' . get_permalink() . '">'. esc_attr( __( 'Read More', 'kazi-rabiul-islam_related-post' ) ) .'</a></i></li>';
                    $content .= '</ul>';
                    $content .= '</div>';
                }

                $content .= '</div>';

                return $content; // Return the related post
            }
        }
    }

    public function frontend_assets() { // Display the related post in frontend

        $plugin_data = get_plugin_data(__FILE__); // Get the plugin data
        $plugin_version = $plugin_data['Version']; // Get the plugin version
        $assets_path = plugin_dir_url( __FILE__)."assets/"; // Get the assets path

        wp_enqueue_style( "kazi-rabiul-related-post-frontend-style", $assets_path . "css/frontend.css", [], $plugin_version ); // Load the frontend css
    }
 }

 new KaziRelatedPost();


 