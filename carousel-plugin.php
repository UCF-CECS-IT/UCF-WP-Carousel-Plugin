<?php
/*
Plugin Name: UCF WP Carousel Plugin
Description: This plugin adds a Bootstrap-style carousel based on Athena theme variables.
Version: 1.0.0
Author: Paul Sepulveda
License: GPL3
GitHub Plugin URI: https://github.com/UCF-CECS-IT/UCF-WP-Carousel-Plugin
*/

if (!defined('WPINC')) {
	die;
}

define( 'CAROUSEL_PLUGIN', plugin_dir_path(__FILE__) );
define( 'CAROUSEL_PLUGIN_FILE', __FILE__ );

function carousel_enqueue_style() {
	wp_enqueue_style( 'carousel',  plugins_url( 'static/css/style.min.css',CAROUSEL_PLUGIN_FILE ), array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'carousel_enqueue_style' );

function carousel_enqueue_script() {
	wp_enqueue_script( 'carousel', plugins_url( '/static/js/carousel.min.js', CAROUSEL_PLUGIN_FILE), array('jquery'), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'carousel_enqueue_script' );

function carouse_enqueue_admin_style() {
	wp_enqueue_style( 'carousel-acf', plugins_url( 'static/css/acf-styles.css', CAROUSEL_PLUGIN_FILE ) );
}
add_action( 'admin_enqueue_scripts', 'carouse_enqueue_admin_style' );



function my_acf_json_save_point( $path ) {
	// update path
	$path = CAROUSEL_PLUGIN . '/json';

	// return
	return $path;
}
add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );

add_action( 'plugins_loaded', 'add_plugin_options');

function add_plugin_options(){
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array(
			'page_title' 	=> 'Athena Carousel Settings',
			'menu_title'	=> 'Athena Carousel Settings',
			'menu_slug' 	=> 'carousel-settings',
			'capability'	=> 'edit_posts',
			'redirect'		=> false
		));
	}
}


function carousel_shortcode( $atts ) {
	$allCarousels = get_field( 'carousels', 'option' );
	$carouselIndex = $atts['id'] - 1;
	$carousel = $allCarousels[$carouselIndex];
	$controls = $carousel['carousel_control'];
	$indicators = $carousel['carousel_position_indicators'];
	$slides = $carousel['carousel_slides'];

	// echo '<pre>';
	// print_r($carousel);

	ob_start();
	?>
	<div
		id="athenaCarousel<?php echo $atts['id']; ?>"
		class="carousel slide"
		data-interval="<?php echo $carousel['carousel_interval'] ?: '5000'; ?>"
		data-keyboard="<?php echo $carousel['carousel_keyboard'] ?: 'true'; ?>"
		data-pause="<?php echo $carousel['carousel_pause'] ?: 'hover'; ?>"
		data-ride="<?php echo $carousel['carousel_ride'] ?: 'false'; ?>"
		data-wrap="<?php echo $carousel['carousel_wrap'] ?: 'true'; ?>"
		>
		<?php if ( $indicators == 'Yes' ): ?>
			<ol class="carousel-indicators">
				<?php foreach( $slides as $index => $slide ): ?>
					<li
						data-target="#athenaCarousel<?php echo $atts['id']; ?>"
						data-slide-to="<?php echo $index; ?>"
						<?php if ( $index == 0 ): ?>
							class="active"
						<?php endif; ?>
						>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<div class="carousel-inner">
			<?php foreach( $slides as $index => $slide ): ?>
				<div class="carousel-item <?php if ($index == 0) { echo 'active'; } ?>">
					<img src="<?php echo $slide['slide_image']; ?>" class="d-block w-100" alt="<?php echo $slide['slide_caption']; ?>">
					<?php if ( $slide['slide_caption'] || $slide['slide_caption_subtitle'] ): ?>
						<div class="carousel-caption d-block bg-inverse-t-3">
							<h2 class="text-primary"> <?php echo $slide['slide_caption'] ?? ''; ?>
								<?php if ( $slide['slide_caption_subtitle'] ): ?>
									<br><small class="text-inverse"><?php echo $slide['slide_caption_subtitle']; ?></small>
								<?php endif; ?>
							</h2>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( $controls == 'Yes' ): ?>
			<button class="carousel-control-prev" type="button" data-target="#athenaCarousel<?php echo $atts['id']; ?>" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-target="#athenaCarousel<?php echo $atts['id']; ?>" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</button>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'athena-carousel', 'carousel_shortcode' );
