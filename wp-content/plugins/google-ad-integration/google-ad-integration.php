<?php
/*
Plugin Name: Google Ad Integration
Description: Support Google ads & GPT on wordpress.
Version: 1.0.0
Author: Zack Jones
*/

function zj_gadi_enqueue_dependencies() {
    wp_enqueue_script("gad-gpt", "https://securepubads.g.doubleclick.net/tag/js/gpt.js#asyncload");
    wp_enqueue_script("gad-prebid", plugins_url("prebid.js#asyncload", __FILE__), array("gad-gpt"), "1.0.0");
    wp_enqueue_script("google-ad-integration", plugins_url("google-ad-integration.js", __FILE__), array("gad-gpt", "jquery"), "1.0.0");
    wp_enqueue_style("google-ad-integration", plugins_url("main.css", __FILE__), null, "1.0.0");
}


function zj_gadi_generate_ad_slot_html($type) {
    $id = "";
    if ($type == "top") {   
        $id = "gadi-ad-slot-top";
    } else if ($type == "right-rail-top") {
        $id = "gadi-ad-slot-rr-top";
    } else if ($type == "right-rail-middle") {
        $id = "gadi-ad-slot-rr-middle";
    }
    $ad_html = "
    <center>
        <div class='gadi-ad-slot-wrapper'>
            <div class='gadi-ad-slot-label'>Advertisment</div>
            <div class='gadi-ad-slot' id='$id'></div>
        </div>
    </center>";
    return $ad_html;
}

function zj_gadi_top_ad_slot_shortcode() {
    echo zj_gadi_generate_ad_slot_html("top");
}

//Right rail can be top or middle
function zj_gadi_right_rail_ad_slot_shortcode($atts = []) {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );
    $pos = $atts["pos"];

    $content = "";

    if ($pos == "top") {
        $content .= zj_gadi_generate_ad_slot_html("right-rail-top");
    } else if ($pos == "middle") {
        $content .= zj_gadi_generate_ad_slot_html("right-rail-middle");
    }
    
    echo $content;
}

function zj_gadi_init_google_ads() {
    echo "<script>\n";
    echo file_get_contents(__DIR__."/prebid.wrapper.js");
    echo "</script>";
}

function zj_gadi_init() {
    //Enqueue js and css
    add_action( 'wp_enqueue_scripts', 'zj_gadi_enqueue_dependencies');
    //Add google ad code to head
    add_action( 'wp_head', 'zj_gadi_init_google_ads');

    //Allot widgets to run shortcode
    add_filter( 'widget_text', 'do_shortcode' );
    //Add the right rail widget shortcode
    add_shortcode( 'gadi-right-rail-ad-slot', 'zj_gadi_right_rail_ad_slot_shortcode');
    
    //Add ad the themes main content
    add_filter( 'colormag_before_main', 'zj_gadi_top_ad_slot_shortcode' );
}
zj_gadi_init();

//So I can easily have async js files in the header
//https://stackoverflow.com/a/20672324
function add_async_forscript($url)
{
    if (strpos($url, '#asyncload')===false)
        return $url;
    else
        return str_replace('#asyncload', '', $url)."' async='async"; 
}
add_filter('clean_url', 'add_async_forscript', 11, 1);