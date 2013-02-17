<?php
/*
Plugin Name: 8tracks Mix Feeds
Plugin URI: http://chateloin.com/8tracks-mix-feeds
Description: A plugin to display 8tracks mixes that you've liked, created, or are in your feed. Can be shown within a post/page with a shortcode or displayed as a widget in the sidebar.
Version: 1.0
Author: Miguel Chateloin
Author URI: http://chateloin.com
License: GPLv2
*/

/*  Copyright 2012  Miguel Chateloin  (email : mchateloin@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//User info
//http://8tracks.com/users/chateloinus.json?api_key=2dd3f26e795c096c6e455d7cb0d12676aa909a56&api_version=2&per_page=10

//User's created mixes
//http://8tracks.com/users/chateloinus/mixes.json?api_key=2dd3f26e795c096c6e455d7cb0d12676aa909a56&api_version=2&per_page=10

//User's liked mixes
//http://8tracks.com/users/chateloinus/mixes.json?view=liked&api_key=2dd3f26e795c096c6e455d7cb0d12676aa909a56&api_version=2&per_page=10

define( 'API_KEY', '2dd3f26e795c096c6e455d7cb0d12676aa909a56' );
define( 'API_VERSION', 2 );

add_action( 'admin_menu', 'mac8tmf_create_admin_menu_items' );
add_action( 'widgets_init', 'mac8tmf_register_widgets' );
add_shortcode( 'mixes', 'mac8tmf_shortcode_display_mix_feed' );
wp_enqueue_script( 'scripts', plugins_url( 'js/scripts.js', __FILE__ ), array( 'jquery' ) );
wp_enqueue_style( 'open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:600,300,400,700', '', '', 'screen');
wp_enqueue_style( 'front-end', plugins_url( 'css/front-end.css', __FILE__ ) );




function mac8tmf_create_admin_menu_items() 
{
	//create custom top-level menu and submenu items
	add_options_page( '8tracks Mix Feeds Information and Settings', '8tracks Mix Feeds', 'manage_options', '8tracks_mix_feeds', 'mac8tmf_display_options_page' );
}

function mac8tmf_register_widgets() 
{
    register_widget( 'mac8tmf_mix_feed_widget' );
}

function mac8tmf_display_options_page()
{
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>8tracks Mix Feeds Information</h2>
		<p>For detailed help and documentation, please visit <a href="http://chateloin.com/8tracks-mix-feeds#Usage">the plugin web page</a>.</p>
		<?php ?>
		<!--
		<form action="options.php" method="post">
			<?php //settings_fields('mac8tmf_options'); ?>
			<?php //do_settings_sections('mac8tmf'); ?>
			<?php echo do_shortcode( '[mixes heading="Mixes I Dig" item_size="150" user="chateloinus" items="25" items_per_row="5" type="mix_feed"]' ); ?>
			<input name="Submit" type="submit" value="Save Changes" />
		</form>-->
	</div>
	<?php
}

function mac8tmf_shortcode_display_mix_feed( $atts )
{
	extract( shortcode_atts( array(
		'type' => 'created',
		'items' => 5,
		'user' => '',
		'heading' => '',
		'item_size' => 50,
		'items_per_row' => 5,
		'mix_label_size' => '25px',
		'css_id' => ''
	), $atts ) );
	
	$api_url = 'http://8tracks.com/users/' 
				. urlencode( $user ) . '/mixes.json' 
				. '?api_key=' .	API_KEY 
				. '&api_version=' . API_VERSION
				. '&per_page=' . $items;
				
	switch ( $type )
	{
		case 'liked':
		case 'mix_feed':
			$api_url .= '&view=' . $type;
		case 'created':
		default:
			break;
	}
	
	$output = '<div class="mix-feed-container" ';
	if ( !empty( $css_id ) ) $output .= 'id="' . $css_id . '" ' ;
	$output .= '>';
	
	if ( !empty( $heading ) ) $output .= '<h2 class="mix-feed-heading" >' . $heading . '</h2>';
	
	$api_response = wp_remote_get( $api_url );
	$json_body = wp_remote_retrieve_body( $api_response );
	if ( empty( $json_body ) ) return false;
	$mix_list = json_decode( $json_body, true );
	
	$items_per_row = (int) $items_per_row;
	$item_count = 1;
	$style = 'width: ' . $item_size . 'px; height: ' . $item_size . 'px; background-size: ' . $item_size . 'px ' . $item_size . 'px;';
	foreach( $mix_list[ 'mixes' ] as $mix )
	{
		$output .= '<a class="mix-feed-item-link" href="' . $mix['restful_url'] . '" target="_blank" >';
			$output .= '<div class="mix-feed-item" style="' . $style . 'background-image: url(' . $mix[ 'cover_urls' ][ 'sq250' ] . ');' . '" >';
					
			$output .= '</div>';
			$output .= '<h2 style="font-size: ' . $mix_label_size . ';">' . $mix[ 'name' ] . '</h2>';
		$output .= '</a>';

		if ( ( $item_count % $items_per_row ) == 0 ) $output .= '<br />';
		$item_count++;
	}
	
	$output .= '</div>';
	
	return $output;
}


class mac8tmf_mix_feed_widget extends WP_Widget {

    //process the new widget
    function mac8tmf_mix_feed_widget() {
	
        $widget_ops = array( 
			'classname' => 'mac8tmf_mix_feed_widget_class', 
			'description' => 'Display liked, created, or feed mixes from an 8tracks profile.' 
			); 
			
        $this->WP_Widget( 'mac8tmf_mix_feed_widget', '8tracks Mix Feed', $widget_ops );
    }
 
     //build the widget settings form
    function form($instance) {
        $defaults = array( 
			'type' => 'created',
			'items' => 5,
			'user' => '',
			'heading' => '',
			'item_size' => 50,
			'items_per_row' => 5,
			'mix_label_size' => '25px',
			'css_id' => ''
		); 
        $instance = wp_parse_args( (array) $instance, $defaults );
		$type = $instance['type'];
		$items = $instance['items'];
		$user = $instance['user'];
		$heading = $instance['heading'];
		$item_size = $instance['item_size'];
		$items_per_row = $instance['items_per_row'];
		$mix_label_size = $instance['mix_label_size'];
		$css_id = $instance['css_id'];
		
        ?>
			<p>8tracks username: <input class="widefat" name="<?php echo $this->get_field_name( 'user' ); ?>"  type="text" value="<?php echo esc_attr( $user ); ?>" /></p>
			<p>Type of feed:
            	<select name="<?php echo $this->get_field_name( 'type' ); ?>">
                    <option value="created" <?php selected( $type, 'created' ); ?>>Created mixes</option>
                    <option value="liked" <?php selected( $type, 'liked' ); ?>>Liked mixes</option>
                    <option value="mix_feed" <?php selected( $type, 'mix_feed' ); ?>>Dashboard mix feed</option>
                </select>
            </p>
			<p>Feed heading: <input class="widefat" name="<?php echo $this->get_field_name( 'heading' ); ?>"  type="text" value="<?php echo esc_attr( $heading ); ?>" /></p>
			<p>Number of mixes to display (Max is 50): <input class="widefat" name="<?php echo $this->get_field_name( 'items' ); ?>"  type="text" value="<?php echo esc_attr( $items ); ?>" /></p>
			<p>Mix image size (measure in px, default is 50, max is 250): <input class="widefat" name="<?php echo $this->get_field_name( 'item_size' ); ?>"  type="text" value="<?php echo esc_attr( $item_size ); ?>" /></p>
			<p>Number of mixes in each row: <input class="widefat" name="<?php echo $this->get_field_name( 'items_per_row' ); ?>"  type="text" value="<?php echo esc_attr( $items_per_row ); ?>" /></p>
			<p>Mix label size (e.g. "16px", "1em", "100%"): <input class="widefat" name="<?php echo $this->get_field_name( 'mix_label_size' ); ?>"  type="text" value="<?php echo esc_attr( $mix_label_size ); ?>" /></p>
			<p>Container CSS ID: <input class="widefat" name="<?php echo $this->get_field_name( 'css_id' ); ?>"  type="text" value="<?php echo esc_attr( $css_id ); ?>" /></p>
			
		<?php
		
    }
 
    //save the widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['items'] = strip_tags( $new_instance['items'] );
		$instance['user'] = strip_tags( $new_instance['user'] );
		$instance['heading'] = strip_tags( $new_instance['heading'] );
		$instance['item_size'] = strip_tags( $new_instance['item_size'] );
		$instance['items_per_row'] = strip_tags( $new_instance['items_per_row'] );
		$instance['mix_label_size'] = strip_tags( $new_instance['mix_label_size'] );
		$instance['css_id'] = strip_tags( $new_instance['css_id'] );
		
        return $instance;
    }
 
    //display the widget
    function widget($args, $instance) {
        extract($args);
 
        echo $before_widget;
		
		//load the widget settings
        $heading = apply_filters( 'widget_title', $instance['heading'] );
        $type = empty( $instance['type'] ) ? 'created' : $instance['type'];
		$items = empty( $instance['items'] ) ? 5 : $instance['items'];
		$user = empty( $instance['user'] ) ? '' : $instance['user'];
		$item_size = empty( $instance['item_size'] ) ? 50 : $instance['item_size'];
		$items_per_row = empty( $instance['items_per_row'] ) ? 1 : $instance['items_per_row'];
		$mix_label_size = empty( $instance['mix_label_size'] ) ? '25px' : $instance['mix_label_size'];
		$mix_label_size = empty( $instance['css_id'] ) ? '' : $instance['css_id'];
		
        //if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		if ( !empty( $heading ) ) { echo $before_title . $heading . $after_title; };
		if ( $items > 50 ) $items = 50;
		if ( ! absint( $items ) ) $items = 5;
		if ( $item_size > 250 ) $items = 250;
		if ( ! absint( $item_size ) ) $item_size = 50;
		if ( $items_per_row > 50 ) $items_per_row = 50;
		if ( ! absint( $items_per_row ) ) $items_per_row = 1;
		if ( $user ) {
		
			echo do_shortcode( '[mixes heading="' . '' 
								. '" item_size="' . $item_size 
								. '" user="' . $user 
								. '" items="' . $items 
								. '" items_per_row="' . $items_per_row
								. '" type="' . $type 
								. '" mix_label_size="' . $mix_label_size
								. '" css_id="' . $css_id 
								.'"]' );
			
		}

        echo $after_widget;
    }
}

?>