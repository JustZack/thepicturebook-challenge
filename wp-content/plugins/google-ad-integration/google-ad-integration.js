jQuery(document).ready(function(){
    function displayGoogleAds() {
        jQuery("div.gadi-ad-slot").each(function(i, e){
            var isMobile = document.body.clientWidth < 752;
            var id = jQuery(e).attr("id");
            var adUnitIndex = jQuery(e).data("adunit-index");

             //Ad index is known already, this is a 'hard coded' ad.
             if (adUnitIndex > -1 && adUnitIndex < adUnits.length) {
                //Hard coded ads are not shown on mobile (according to test document).
                if (isMobile) {
                    jQuery(e).parent().attr("style", "display:none;");
                } else {
                    var unit = adUnits[adUnitIndex];
                    googletag.cmd.push(function() {
                        googletag.defineSlot(unit["code"], unit["mediaTypes"]["banner"]["sizes"], id).addService(googletag.pubads());
                    });
                }
            } else {

            }
        });
        //
        googletag.cmd.push(function() {
            googletag.pubads().refresh();
        });
    }

    displayGoogleAds();
});
