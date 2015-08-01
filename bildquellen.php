<?php
/*
Plugin Name: Bildquellenangaben 
Description: Quellenangaben für hochgeladene Dateien verwalten.
Version: 1.0
Author: Olaf Parusel
*/

function wp_bildquellen_add_meta_box_attachment( $form_fields, $post ) {
    $field_value = get_post_meta( $post->ID, 'mb_bildquellenangabe', true );
    $form_fields['mb_bildquellenangabe'] = array(
        'value' => $field_value ? $field_value : '',
        'label' => __( 'Quelle' ),
        'helps' => __( 'Geben Sie hier den Quellenverweis an' )
    );
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'wp_bildquellen_add_meta_box_attachment', 10, 2 );

function wp_bildquellen_save_meta_box_attachment( $attachment_id ) {
    if ( isset( $_REQUEST['attachments'][$attachment_id]['mb_bildquellenangabe'] ) ) {
        $mb_bildquellenangabe = $_REQUEST['attachments'][$attachment_id]['mb_bildquellenangabe'];
        update_post_meta( $attachment_id, 'mb_bildquellenangabe', $mb_bildquellenangabe );
    }
}
add_action( 'edit_attachment', 'wp_bildquellen_save_meta_box_attachment' );

function wp_bildquellen_alle(){
	global $post;

	$bq_attachment_meta = get_post_meta($post->ID, 'mb_bildquellenangabe', true);

	// WP_Query arguments
	$args = array (
		'post_type'			=> 'attachment',
		'post_status'		=> 'inherit',
		'meta_query' 		=> array(
						            array(
						                'key'     => 'mb_bildquellenangabe',
						                'value'   => null,
						                'compare' => '!=' // Gesetzt und wieder entfernte Meta-Angaben filtern
						            )
        						), // End of meta_query
		'posts_per_page'	=> -1,		
	);

	// The Query
	$bildquellen_query = new WP_Query( $args );
	$output .= '<table style="width: 100%;">';
	// The Loop
	if ( $bildquellen_query->have_posts() ) {
		while ( $bildquellen_query->have_posts() ) {
			$bildquellen_query->the_post();
			$output .= '<tr>';
				$output .= '<td style="padding: 5px; border-top: 2px solid #efefef;">';
					$output .= wp_get_attachment_link();
				$output .= '</td>';
				$output .= '<td style="padding: 5px; border-top: 2px solid #efefef;">';
					$output .= get_post_meta($post->ID, 'mb_bildquellenangabe', true);
				$output .= '</td>';
			$output .= '</tr>';

			// ob_start();
			// get_template_part('loop', 'produkte_table_x');
			// $output .= ob_get_contents(); 
			// ob_end_clean(); 
		}
	} else {
		$output .= 'Keine Bildquellen vorhanden.';
	}
	$output .= '</table>';
	// Restore original Post Data
	wp_reset_postdata();

	return $output;
}
add_shortcode( 'bildquellen', 'wp_bildquellen_alle' );

?>