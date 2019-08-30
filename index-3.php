<?php

/*
Plugin Name: CG GoogleMap Category Listing
Plugin URI: https://cyberizegroup.com/
Description: Displays Property Category List in a Google Map on Demand
Version: 1.0
Author: Cyberize Group
Author URI: https://cyberizegroup.com/
License: GPLv2 or later
Text Domain: cyberizeframework
*/



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/**
 *
 * Getting Select Options for Property Types
 *
 */
$selected = '';

function get_category_options($select) {

	$categories = array(  
					'Choose A Property Type' => '',
					'Retail' => 'retail',
					'Office' => 'office',
					'Land' => 'land',
					'Industrial' => 'industrial',
					'Development' => 'development',
				);

	$options = '';

	echo $select;

	while (list($key, $value) = each($categories)) {

		if ($select == $value) {
	
			$options .= '<option value="' . $value . '" selected >' . $key . '</option>';

		} else {
			
			$options .= '<option value="' . $value . '">' . $key . '</option>';

		}

	}

	return $options;
}

/**
 *
 * Getting Select Options for Property Types
 *
 */

function get_status_options($select) {

	$status = array(  
				'Choose A Property Status' => '',
				'For Sale' => 'for-sale',
				'For Rent' => 'for-lease',
			);

	$status_options = '';

	echo $select;

	while (list($key, $value) = each($status)) {

		if ($select == $value) {
	
			$status_options .= '<option value="' . $value . '" selected >' . $key . '</option>';

		} else {
			
			$status_options .= '<option value="' . $value . '">' . $key . '</option>';

		}

	}

	return $status_options;
}



/**
 *
 * Adding Custom Shortcode for Property or any CPT list
 *
 */

function cg_googlemap_category_listing($atts) {

if (isset($_POST['categories'])) {

	$list_status = $_POST['categories'];
	$taxonomy_name = $_POST['categories_taxonomy'];

}	

if (isset($_POST['status'])) {

	$list_status = $_POST['status'];
	$taxonomy_name = $_POST['status_taxonomy'];

}	


	$atts = shortcode_atts( 

		array(

			'post_name' => 'properties',
			'post_number' => 10,

		), $atts
	);

	extract($atts);

	ob_start(); // OUTPUT BUFFERING

	$args = array(
	    'post_type' => $post_name,
	    'tax_query' => array(
	        array (
	            'taxonomy' => $taxonomy_name,
	            'field' => 'slug',
	            'terms' => $list_status,
	        )
	    ),
	    'posts_per_page' => $post_number
	);	

	$front_page_post_items = new WP_Query($args);

	?>

<main class="CG-GOOGLEMAP-LISTBOX-SHORTCODE">



	<?php 

		if (isset($_POST['status'])) {

			$selected = $_POST['status'];
			echo $selected  . "<br>";
			echo $taxonomy_name;
		}

		if (isset($_POST['categories'])) {

			$selected = $_POST['categories'];
			echo $selected  . "<br>";
			echo $taxonomy_name;
		}		

	?> 
	<!-- SELECT CATEGORY FORM -->
	<form action="" method="POST" target="_self">
		
		<select name="categories" onchange="this.form.submit()">
			<?php echo get_category_options($selected); ?>
		</select>

		<input type="hidden" name="categories_taxonomy" value="property-type">

	</form>    

	<!-- SELECT STATUS FORM -->
	<form action="" method="POST" target="_self">
		
		<select name="status" onchange="this.form.submit()">
			<?php echo get_status_options($selected); ?>
		</select>
		
		<input type="hidden" name="status_taxonomy" value="listing-status">

	</form>    	




	<div class="content-holder">
		<div class="row">

			<style type="text/css">

			.acf-map {
				width: 100%;
				height: 700px;
				border: #ccc solid 1px;
				margin: 20px 0;
			}

			/* fixes potential theme css conflict */
			.acf-map img {
			   max-width: inherit !important;
			}

			</style>


			<?php
			if ($front_page_post_items->have_posts()): /* Start the Loop */ 
			    while ($front_page_post_items->have_posts()):
			        $front_page_post_items->the_post();
			?>

					<div class="col-sm-12 col-md-12 col-lg-12">

						<!-- ACF MAP START -->
						<div class="acf-map">

						<?php 

								$mapLocation = get_field('location');

						?>	


						    <div class="marker" data-lat="<?php echo $mapLocation['lat'] ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
						    	<a href="<?php the_permalink(); ?>">
						    		<figure style="width: 50% !important;">
							    		<?php the_post_thumbnail( 'blog-size' ); ?>
							    	</figure>
							    	<h6><?php the_title(); ?></h6>
							    	<?php echo $mapLocation['address']; ?>
						    	</a>
						    </div>
							
						<?php 	endwhile;
								
								wp_reset_postdata();
								
							endif;

						?>

						
						</div> <!-- ACF-MAP END -->						
							
					</div> <!-- END col-sm-12 col-md-12 col-lg-6 -->

		</div> <!-- END ROW -->
	</div>
	
</main>


	<?php 

	$module_contents = ob_get_contents();

	ob_end_clean();	

	return $module_contents;
	// return "<h2>CPT: $post_name and Number: $post_number</h2>";
}

add_shortcode( 'cg-googlemap-category', 'cg_googlemap_category_listing' );















