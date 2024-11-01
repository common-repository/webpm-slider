<?php
/*
Plugin Name: WebPM Slider
Plugin URI: http://webplantmedia.com/wordpress/2012/03/webpm-slider/
Description: Image Slider
Version: 1.3
Author: Chris Baldelomar
Author URI: http://www.webplantmedia.com
License: Free
*/

class WebPMSlider {
	function __construct() {
		register_activation_hook( __FILE__, array(  &$this, 'install' ) );

		add_action( 'admin_init', array( &$this, 'admin_setup' ) );
		add_action( 'init', array( &$this, 'slide_init' ) );

		// Add scripts to front end of wordpress
		add_action('wp_enqueue_scripts', array(&$this, 'front_end_script_method') );
		$this->add_slide_image_size();
	}
	function install() {
		if ( !get_option( 'webpm_slider_size_w' ) );
			add_option( 'webpm_slider_size_w', 800 );

		if ( !get_option( 'webpm_slider_size_h' ) );
			add_option( 'webpm_slider_size_h', 800 );

		if ( !get_option( 'webpm_slider_pagination' ) );
			add_option( 'webpm_slider_pagination', 1 );

		if ( !get_option( 'webpm_slider_showtext' ) );
			add_option( 'webpm_slider_showtext', 1 );

		if ( !get_option( 'webpm_slider_generatePagination' ) );
			add_option( 'webpm_slider_generatePagination', 1 );

		if ( !get_option( 'webpm_slider_fadeSpeed' ) );
			add_option( 'webpm_slider_fadeSpeed', 350 );

		if ( !get_option( 'webpm_slider_slideSpeed' ) );
			add_option( 'webpm_slider_slideSpeed', 350 );

		if ( !get_option( 'webpm_slider_effect' ) );
			add_option( 'webpm_slider_effect', 'fade' );

		if ( !get_option( 'webpm_slider_play' ) );
			add_option( 'webpm_slider_play', 3000 );

		if ( !get_option( 'webpm_slider_pause' ) );
			add_option( 'webpm_slider_pause', 3000 );

	}

	function front_end_script_method() {
		wp_register_script('webpm-slides', plugins_url( 'slider/js/slides.min.jquery.js' , __FILE__ ), array('jquery'));
		wp_enqueue_script('webpm-slides');
		wp_register_style('webpm-slides-style', plugins_url( 'slider/css/global.css' , __FILE__ ));
		wp_enqueue_style('webpm-slides-style');
	}
	function set_slider_size($args) {
		?>
		<fieldset>
			<legend class="screen-reader-text"><span>Slider size</span></legend>
			<label for="webpm_slider_size_w">Max Width</label>
			<input name="webpm_slider_size_w" type="text" id="webpm_slider_size_w" value="<?php form_option('webpm_slider_size_w'); ?>" class="small-text">
			<label for="webpm_slider_size_h">Max Height</label>
			<input name="webpm_slider_size_h" type="text" id="webpm_slider_size_h" value="<?php form_option('webpm_slider_size_h'); ?>" class="small-text">
			<span class="description">This option was added by WebPM Slider Plugin.</span>
		</fieldset>
		<?php
	}
	function set_slider_section($args) {
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Timing</th>
					<td>
						<label for="webpm_slider_pause">Pause</label>
						<input name="webpm_slider_pause" type="text" id="webpm_slider_pause" value="<?php form_option('webpm_slider_pause'); ?>" class="small-text">

						<label for="webpm_slider_play">Play</label>
						<input name="webpm_slider_play" type="text" id="webpm_slider_play" value="<?php form_option('webpm_slider_play'); ?>" class="small-text">

						<label for="webpm_slider_fadeSpeed">Fade</label>
						<input name="webpm_slider_fadeSpeed" type="text" id="webpm_slider_fadeSpeed" value="<?php form_option('webpm_slider_fadeSpeed'); ?>" class="small-text">

						<label for="webpm_slider_slideSpeed">Slide</label>
						<input name="webpm_slider_slideSpeed" type="text" id="webpm_slider_slideSpeed" value="<?php form_option('webpm_slider_slideSpeed'); ?>" class="small-text">
						<span class="description">All times are in milliseconds</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Effect</th>
					<td>
						<?php $checked = (get_option('webpm_slider_effect') == 'fade' ? ' checked="checked"' : ''); ?>
						<input name="webpm_slider_effect" type="radio" id="webpm_slider_effect" value="fade" <?php echo $checked; ?>>
						<label>Fade</label>

						<?php $checked = (get_option('webpm_slider_effect') == 'slide' ? ' checked="checked"' : ''); ?>
						<input name="webpm_slider_effect" type="radio" id="webpm_slider_effect" value="slide" <?php echo $checked; ?>>
						<label>Slide</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Show Text</th>
					<td>
						<?php $checked = (get_option('webpm_slider_showtext') == 1 ? ' checked="checked"' : ''); ?>
						<input name="webpm_slider_showtext" type="checkbox" id="webpm_slider_showtext" value="1" <?php echo $checked; ?>>
						<span class="description">Display Title and Paragraph on Slide Image</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Pagination</th>
					<td>
						<?php $checked = (get_option('webpm_slider_pagination') == 1 ? ' checked="checked"' : ''); ?>
						<input name="webpm_slider_pagination" type="checkbox" id="webpm_slider_pagination" value="1" <?php echo $checked; ?>>
						<span class="description">If you're not using pagination you can set to false, but don't have to</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Generate Pagination</th>
					<td>
						<?php $checked = (get_option('webpm_slider_generatePagination') == 1 ? ' checked="checked"' : ''); ?>
						<input name="webpm_slider_generatePagination" type="checkbox" id="webpm_slider_generatePagination" value="1" <?php echo $checked; ?>>
						<span class="description">Auto generate pagination</span>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
	}
	function admin_setup() {
		register_setting( 'media', 'webpm_slider_size_w', 'intval' );
		register_setting( 'media', 'webpm_slider_size_h', 'intval' );
		register_setting( 'media', 'webpm_slider_pagination', 'intval' );
		register_setting( 'media', 'webpm_slider_generatePagination', 'intval' );
		register_setting( 'media', 'webpm_slider_showtext', 'intval' );
		register_setting( 'media', 'webpm_slider_fadeSpeed', 'intval' );
		register_setting( 'media', 'webpm_slider_slideSpeed', 'intval' );
		register_setting( 'media', 'webpm_slider_effect', 'strval' );
		register_setting( 'media', 'webpm_slider_play', 'intval' );
		register_setting( 'media', 'webpm_slider_pause', 'intval' );

		add_settings_field( 'webpm_slider_size', 'Slider size', array( &$this, 'set_slider_size' ), 'media', 'default' );
		add_settings_section( 'webpm_slider', 'WebPM Slider', array( &$this, 'set_slider_section' ), 'media' );
	}
	function add_slide_image_size() {
		$width = get_option( 'webpm_slider_size_w' );
		$height = get_option( 'webpm_slider_size_h' );
		add_image_size( 'webpm_slider', $width, $height );
	}
	function slide_init() {
		register_post_type( 'slide',
			array(
				'labels' => array(
					'name' => __( 'Slides' ),
					'singular_name' => __( 'Slide' )
				),
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => false,
			'capability_type' => 'post',
			'has_archive' => false,
			'show_ui' => true,
			'hierarchical' => false,
			'rewrite' => true,
			'menu_position' => 24,
			'supports' => array('title','editor','thumbnail', 'excerpt', 'page-attributes', 'revisions', 'custom-fields')
			)
		);
	}
}
$webpm['slider'] = new WebPMSlider();

function webpm_slider() {
?>
	<script>
		jQuery(document).ready(function($) {
			$('#slides').slides({
				pagination: <?php echo ( get_option('webpm_slider_pagination') ? 'true' : 'false' ); ?>,
				generatePagination: <?php echo ( get_option('webpm_slider_generatePagination') ? 'true' : 'false' ); ?>,
				fadeSpeed: <?php echo get_option('webpm_slider_fadeSpeed'); ?>,
				slideSpeed: <?php echo get_option( 'webpm_slider_slideSpeed' ); ?>,
				effect: '<?php echo get_option( 'webpm_slider_effect' ); ?>',
				play: <?php echo get_option( 'webpm_slider_play' ); ?>,
				pause: <?php echo get_option( 'webpm_slider_pause' ); ?>,
			});
		});
	</script>
	<div id="container">
	<div id="example">
	<div id="slides">
		<div class="slides_container">
			<?php 
			$args = array(
				'post_type'			=>	'slide',
				'orderby'			=>	'menu_order',
				'order'				=>	'ASC',
				'posts_per_page'	=>	-1
			); 
			?>
			<?php $my_query = new WP_Query( $args ); ?>
			<?php if($my_query->have_posts()) : ?>
				<!-- Beginning of Loop for slider -->
				<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
					<?php global $more; $more = 0; ?>
					<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'webpm_slider' ); ?> 
					<div id="window" style="background:url('<?php echo $image[0]; ?>')" class="slide quote<?php echo $post->ID; ?>">
						<div class="comment-box slidetext-<?php echo $post->ID; ?>">
							<?php if ( get_option( 'webpm_slider_showtext' ) ): ?>
							<!-- This prints out the title for each Slide post type -->
							<h2><?php the_title(); ?></h2>
							<!-- Hi Angie, This prints out the content that you type on the the editor -->
							<p><?php the_content('See More'); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
				<!-- End of Loop for slider -->
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
	</div>
	</div>
<?php
}
