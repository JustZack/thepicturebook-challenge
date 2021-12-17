jQuery(document).ready(function(){
    function displayGoogleAds() {
        var isMobile = document.body.clientWidth < 752;
        console.log(document.body.clientWidth);
        jQuery("div.gadi-ad-slot").each(function(i, e){
            var id = jQuery(e).attr("id");
            console.log(isMobile + " | " + id);
            if (id.indexOf("-rr-") > -1 && isMobile) {
                jQuery(e).parent().css({display: "none"});
            } else googletag.cmd.push(function() { googletag.display(id); });
        });    
    }

    //displayGoogleAds();
});
