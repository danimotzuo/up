<?php
/*
Plugin Name: Resumao
Plugin URI: http://modelos.edu.pl/enfimorg
Description: The plugin shows excerpts instead of contents in your blog, single posts and pages excluded. It tries to display your custom excerpt text and if it doesn't find it it will show an automatically generated excerpt. You can also define an excerpt length (default is 500) and a custom read more link.
Version: 1.5
Author: Modelos
Author URI: http://modelos.edu.pl
*/

function auto_enfimorg_activation() {
	if (!get_option("enfimorg_length")){
		update_option("enfimorg_length","500");
	}
	if (!get_option("enfimorg_align")){
		update_option("enfimorg_align","alignleft");
	}
	if (!get_option("enfimorg_moretext")){
		update_option("enfimorg_moretext","Read more [...]");
	}
	if (!get_option("enfimorg_moreimg")){
		update_option("enfimorg_moreimg","");
	}
	if (!get_option("enfimorg_rss")){
		update_option("enfimorg_rss","yes");
	}
	if (!get_option("enfimorg_homepage")){
		update_option("enfimorg_homepage","no");
	}
	if (!get_option("enfimorg_sticky")){
		update_option("enfimorg_sticky","no");
	}
	if (!get_option("enfimorg_thumb")){
		update_option("enfimorg_thumb","none");
	}
}
function auto_enfimorg_construct() {
	$rss_disable=get_option("enfimorg_rss");
	$sticky_disable=get_option("enfimorg_sticky");
	$homepage_disable=get_option("enfimorg_homepage");
	if ($rss_disable=="yes" && $sticky_disable=="yes" && $homepage_disable=="yes"){
		if(!is_single() && !is_page() && !is_feed() && !is_sticky() && !is_home()) {
			add_filter('the_content','enfimorg');
		} 
	}
	else if ($rss_disable=="yes" && $sticky_disable=="yes"){
		if(!is_single() && !is_page() && !is_feed() && !is_sticky()) {
			add_filter('the_content','enfimorg');
		}
	}
	else if ($sticky_disable=="yes" && $homepage_disable=="yes"){
		if(!is_single() && !is_page() && !is_sticky() && !is_home()) {
			add_filter('the_content','enfimorg');
		} 
	}
	else if ($rss_disable=="yes" && $homepage_disable=="yes"){
		if(!is_single() && !is_page() && !is_feed() && !is_home()) {
			add_filter('the_content','enfimorg');
		} 
	} else if ($rss_disable=="yes"){
		if(!is_single() && !is_page() && !is_feed()) {
			add_filter('the_content','enfimorg');
		} 
	} else if ($sticky_disable=="yes") {
		if(!is_single() && !is_page() && !is_sticky()) {
			add_filter('the_content','enfimorg');
		} 
	} else if ($homepage_disable=="yes") {
		if(!is_single() && !is_page() && !is_home()) {
			add_filter('the_content','enfimorg');
		} 
	} else if ($sticky_disable!="yes" && $rss_disable!="yes" && $homepage_disable!="yes") {
		if(!is_single() && !is_page()) {
			//add_filter('the_excerpt_rss','enfimorg');
			add_filter('the_content','enfimorg');
		}	
	}
	if (!get_option("enfimorg_length")){
		update_option("enfimorg_length","500");
	}
	if (!get_option("enfimorg_align")){
		update_option("enfimorg_align","alignleft");
	}
	if (!get_option("enfimorg_rss")){
		update_option("enfimorg_rss","yes");
	}
	if (!get_option("enfimorg_homepage")){
		update_option("enfimorg_homepage","no");
	}
	if (!get_option("enfimorg_sticky")){
		update_option("enfimorg_sticky","no");
	}
	if (!get_option("enfimorg_thumb")){
		update_option("enfimorg_thumb","none");
	}
}

function auto_enfimorg_options() {
	add_options_page('enfimorg', 'enfimorg', 'manage_options','enfimorg/options.php');
}

function myTruncate($string, $limit, $break=".", $pad="...") {
	
	if(strlen($string) <= $limit) return $string; 
	if(false !== ($breakpoint = strpos($string, $break, $limit))) {
		if($breakpoint < strlen($string) - 1) {
			$string = substr($string, 0, $breakpoint) . $pad;
		}
	} return $string;
}

function enfimorg($content) {
	global $post;
	$testomore = get_option("enfimorg_moretext");
	$imgmore = get_option("enfimorg_moreimg");
	$whatthumb = get_option("enfimorg_thumb");
	$customclass = get_option("enfimorg_class");
	$alignment=get_option("enfimorg_align"); if ($alignment=="none"){$alignment="";}
	if ($whatthumb=="none"){ $thumb = ""; }
	else {
		if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
			$default_attr = array(
			'class'	=> "attachment-".$whatthumb." ".$alignment." autoexcerpt_thumb ".$customclass,
			'alt'	=> trim(strip_tags(strip_shortcodes( $attachment->post_excerpt ))),
			'title'	=> trim(strip_tags(strip_shortcodes( $attachment->post_title ))),
			);
			  $thumb=get_the_post_thumbnail($post->ID, $whatthumb,$default_attr);
			} 
	}
	if ($post->post_excerpt!=""){
		$excerpt=$thumb.$post->post_excerpt;
		if ($imgmore!=""){$linkmore = ' <a href="'.get_permalink().'" class="more-link"><img src="'.$imgmore.'" border="0" alt="Read more" /></a>';} 
		else if ($testomore!=""){$linkmore = ' <a href="'.get_permalink().'" class="more-link">'.$testomore.'</a>';}
	} else {
		if (strlen($post->post_content)>get_option("enfimorg_length")){
			$excerpt= $thumb.myTruncate(strip_tags(strip_shortcodes($post->post_content)), get_option("enfimorg_length"), " ", "");
			if ($imgmore!=""){$linkmore = ' <a href="'.get_permalink().'" class="more-link"><img src="'.$imgmore.'" border="0" alt="Read more" /></a>';} 
			else if ($testomore!=""){$linkmore = ' <a href="'.get_permalink().'" class="more-link">'.$testomore.'</a>';}
		} else {
			$excerpt=$thumb.$content;
			$linkmore="";
		}
	}
	
	return $excerpt.$linkmore;
	
}
function custom_enfimorg_length() {
	return get_option("enfimorg_length");
}


function add_settings_link($links, $file) {
static $this_plugin;
if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
 
if ($file == $this_plugin){
$settings_link = '<a href="options-general.php?page=enfimorg/options.php">'.__("Settings", "enfimorg").'</a>';
 array_unshift($links, $settings_link);
}
return $links;
 }

register_activation_hook(__FILE__,'auto_enfimorg_activation');
add_action('the_post', 'auto_enfimorg_construct');
add_action('admin_menu','auto_enfimorg_options');
add_filter('excerpt_length', 'custom_enfimorg_length');
add_filter('plugin_action_links', 'add_settings_link', 10, 2 );
if ( function_exists( 'add_theme_support' ) ) { 
  add_theme_support( 'post-thumbnails' ); 
}

?>