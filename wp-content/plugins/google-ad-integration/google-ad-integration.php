<?php
/*
Plugin Name: Google Ad Integration
Description: Support Google ads & GPT on wordpress.
Version: 1.0.0
Author: Zack Jones
*/

function zj_gadi_enqueue_dependencies() {
    wp_enqueue_script("gad-gpt", "https://securepubads.g.doubleclick.net/tag/js/gpt.js");
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
    echo "<script>";
    echo "
        window.googletag = window.googletag || {cmd: []};
        googletag.cmd.push(function() {
        
        googletag.defineSlot('/22360860229/Aditude/aditude_test1', [970, 250], 'gadi-ad-slot-top')
               .addService(googletag.pubads());
        googletag.defineSlot('/22360860229/Aditude/aditude_test2', [300, 250], 'gadi-ad-slot-rr-top')
               .addService(googletag.pubads());
               googletag.defineSlot('/22360860229/Aditude/aditude_test3', [[300, 600], [300, 250]], 'gadi-ad-slot-rr-middle')
               .addService(googletag.pubads());

        // Enable SRA and services.
        googletag.pubads().enableSingleRequest();
        googletag.enableServices();
    });";
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