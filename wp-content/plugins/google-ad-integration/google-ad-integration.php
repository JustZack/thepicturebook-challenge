<?php
/*
Plugin Name: Google Ad Integration
Description: Support Google ads & GPT on wordpress.
Version: 1.0.0
Author: Zack Jones
*/

function zj_gadi_enqueue_dependencies() {
    wp_enqueue_script("gad-gpt", "https://securepubads.g.doubleclick.net/tag/js/gpt.js#asyncload");
    wp_enqueue_script("gad-prebid", plugins_url("prebid.js#asyncload", __FILE__), array("gad-gpt"), "1.0.1");
    wp_enqueue_script("google-ad-integration", plugins_url("google-ad-integration.js", __FILE__), array("gad-gpt", "jquery"), "1.0.1");
    wp_enqueue_style("google-ad-integration", plugins_url("main.css", __FILE__), null, "1.0.0");
}

$zj_gadi_page_content_slot_count = 0;
function zj_gadi_generate_ad_slot_html($type, $adUnitIndex = -1) {
    global $zj_gadi_page_content_slot_count;
    $id = ""; $class='gadi-ad-slot-wrapper';
    if ($type == "top") $id = "gadi-ad-slot-top";
    else if ($type == "right-rail-top") $id = "gadi-ad-slot-rr-top";
    else if ($type == "right-rail-middle") $id = "gadi-ad-slot-rr-middle";
    else if ($type == "content") {
        $class .= ' gadi-content-ad-slot-wrapper';
        $id = "gadi-ad-slot-content-".$zj_gadi_page_content_slot_count++;
    }

    $ad_html = "<div class='$class'><div class='gadi-ad-slot-label'>Advertisment</div><div class='gadi-ad-slot' id='$id' data-adunit-index='$adUnitIndex'></div></div>";
    return $ad_html;
}

//Top ad slot
function zj_gadi_top_ad_slot() {
    echo zj_gadi_generate_ad_slot_html("top", 0);
}

function zj_gadi_right_rail_top_ad_slot() {
    echo zj_gadi_generate_ad_slot_html("right-rail-top", 1);
}

function zj_gadi_right_rail_middle_ad_slot() {
    echo zj_gadi_generate_ad_slot_html("right-rail-middle", 2);
}

function zj_gadi_inject_ad($content, $index) {
    //Cut the string into everything up to </h2> and verything after
    $front = substr($content, 0, $index);
    $back = substr($content, $index);

    $ad = zj_gadi_generate_ad_slot_html("content");
    //Then insert the ad between front and back
    return $front.$ad.$back;
}

//Recursively place ads after each <h2>...</h2> in the content
function zj_gadi_inject_ads_recursive($content, $offset) {
    //Find the the next </h2>
    $next_h2 = strpos($content, "</h2>", $offset);
    //Inject an ad after the found </h2>
    $content = zj_gadi_inject_ad($content, $next_h2+5);
    //Update the offset to search for the next </h2>
    $offset = strpos($content, "</h2>", $next_h2+5);
    //Find the next <p>
    $next_p = strpos($content, "<p>", $next_h2);
    //The next p must exists AND come before the next section
    if ($next_p > -1 && ($offset == -1 || $next_p < $offset)) {
        $end_p = strpos($content, "</p>", $next_p);
        $paragraph = substr($content, $next_p+3, $end_p-$next_p);
        //Paragraph must be atleast this long for An ad: 125 (Max content length) + "<p></p>" length
        if (strlen($paragraph) > 125 + 7) {
            $content = zj_gadi_inject_ad($content, $end_p+4);
        } else {
            //Otherwise place an ad after the second existing paragraph
            $next_p = strpos($content, "<p>", $end_p);
            if ($next_p > -1 && ($offset == false || $next_p < $offset)) {
                $end_p = strpos($content, "</p>", $next_p);
                $content = zj_gadi_inject_ad($content, $end_p+4);
            }
        }
    }



    
    if ($offset !== false) return zj_gadi_inject_ads_recursive($content, $offset);
    else return $content;
}

function zj_gadi_inject_ads($content) {
    //Inject ads after each h2
    $new_content = zj_gadi_inject_ads_recursive($content, 0);
    //Inject ads after the first (125+char) paragraphs of each section
    //Or Inject ads after the second paragraphs of each section
    return $new_content;
}


function zj_gadi_init_google_ads() {
    global $adslot_defines;
    echo "<script>\n";
    echo file_get_contents(__DIR__."/prebid.wrapper.js");
    echo "</script>";
}


function zj_gadi_init() {
    //Enqueue js and css dependencies
    add_action( 'wp_enqueue_scripts', 'zj_gadi_enqueue_dependencies');

    //Add important google ad code to the header
    add_action( 'wp_head', 'zj_gadi_init_google_ads');
    
    //Place an ad at the top of the main content
    add_filter( 'colormag_before_main', 'zj_gadi_top_ad_slot' );
    //Place an ad at the top of the sidebar
    add_filter( 'colormag_before_sidebar', 'zj_gadi_right_rail_top_ad_slot' );

    //Allow widgets to run shortcode & Add the right rail widget shortcode
    add_filter( 'widget_text', 'do_shortcode' );
    add_shortcode( 'gadi-right-rail-middle-ad-slot', 'zj_gadi_right_rail_middle_ad_slot');

    //Inject ads into the content
    add_filter( 'the_content', 'zj_gadi_inject_ads' );

};
zj_gadi_init();

//Add #asyncload to any script urls you want to load async
//https://stackoverflow.com/a/20672324
function zj_gadi_so_add_async_forscript($url) {
    if (strpos($url, '#asyncload')===false) return $url;
    else return str_replace('#asyncload', '', $url)."' async='async"; 
}
add_filter('clean_url', 'zj_gadi_so_add_async_forscript', 11, 1);