<?php
/*
Plugin Name: Author Tags
Description: Displays author specific tags with post count on single post page.
Version: 1.0
Author: Shwetuk
*/

// Creating the widget 
class wpb_author_tags extends WP_Widget {

	function __construct() {
	parent::__construct(
	// Base ID of your widget
	'wpb_author_tags', 

	// Widget name will appear in UI
	__('Author Tags', 'wpb_author_tags_domain'), 

	// Widget description
	array( 'description' => __( 'Displays Author specific tags with post count', 'wpb_author_tags_domain' ), ) 
	);
	}
	
	// Widget Backend 
	public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
	}
	else {
	$title = __( 'Author Tags', 'wpb_author_tags_domain' );
	}
	// Widget admin form

	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
	}
	
	// Creating widget front-end
	public function widget( $args, $instance ) {
	$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Author Tags' );
	$title = apply_filters( 'widget_title', $title );
	if(is_single()){
		global $post;
		$current_user_id=$post->post_author;
		$postargs = array(
				 'posts_per_page' => 20,
				 'ignore_sticky_posts' => 1,
				 'author' => $current_user_id
		);

		//$author_posts = new WP_Query($postargs);
		$author_posts = get_posts( $postargs );
		$cnt = count($author_posts);
		foreach( $author_posts as $author_posts_all){
		$get_tags[$cnt] = wp_get_post_tags( $author_posts_all->ID ) ;$cnt--;
		}
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		$authorname = get_the_author_meta( 'display_name', $current_user_id );
		$title = str_replace('[authorname]', $authorname.'\'s', $title);
		echo $args['before_title'] . $title . $args['after_title'];
		foreach( $get_tags as $alltags){
			foreach( $alltags as $alltags){ $count_tags[] = $alltags->name;}
		}
		$count_tags = array_count_values($count_tags);
		foreach( $count_tags as $key => $value){
		?>
			<div class="author-tags">
			<a href="<?php echo get_site_url().'/?tag='.$key;?>&author=<?php echo $current_user_id;?>"><?php echo $key." (".$value.")";?></a>
			</div>

		<?php
			}
		
		echo $args['after_widget'];
		wp_reset_postdata();
		}
	}	
	} // Class wpb_author_tags ends here
	
	// Register and load the widget
	function wpb_load_authortag_widget() {
		register_widget( 'wpb_author_tags' );
	}
	add_action( 'widgets_init', 'wpb_load_authortag_widget' );
	
	function author_tags_stylesheet() {
					wp_register_style('author_tags_stylesheet', plugins_url('css/author-tags.css', __FILE__) );
					wp_enqueue_style('author_tags_stylesheet');
	}
	add_action('wp_enqueue_scripts', 'author_tags_stylesheet');
?>


