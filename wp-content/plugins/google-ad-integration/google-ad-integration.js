jQuery(document).ready(function(){
    function displayGoogleAds() {
        jQuery("div.gadi-ad-slot").each(function(i, e){
            var id = jQuery(e).attr("id");
            googletag.cmd.push(function() { googletag.display(id); });
        });    
    }

    displayGoogleAds();
});
