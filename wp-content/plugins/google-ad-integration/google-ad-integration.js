jQuery(document).ready(function(){
    function displayGoogleAds() {
        //Mobile breakpoint for the theme
        var isMobile = document.body.clientWidth < 752;
        //For each ad slot defined on the page
        jQuery("div.gadi-ad-slot").each(function(i, e){
            var id = jQuery(e).attr("id");
            var adUnitIndex = jQuery(e).data("adunit-index");
            var slot = null;
             //Ad index is known already, this is a 'hard coded' ad.
            if (adUnitIndex > -1 && adUnitIndex < adUnits.length) {
                defineAdSlot(id, adUnitIndex);
            } else {
                if (isMobile) slot = defineResponsiveAdSlot(id, 1);
                else slot = defineResponsiveAdSlot(id, 0);
            }

        }).promise().done(function(){
            //Refresh the ads
            googletag.cmd.push(function() {
                googletag.enableServices();
                googletag.pubads().refresh();
            });
        });

    }

    function defineResponsiveAdSlot(id, unitIndex) {
        var unit = adUnits[unitIndex];
        return googletag.cmd.push(function() {
            var slot = googletag.defineSlot(unit["code"], unit["mediaTypes"]["banner"]["sizes"], id).addService(googletag.pubads());
            var mapping = googletag.sizeMapping()
            .addSize([1024, 760], [750, 200], [728, 90])
            .addSize([640, 480], [300, 250])
            .addSize([0, 0], [300, 250]).build();
            slot.defineSizeMapping(mapping);
        });
    }

    function defineAdSlot(id, unitIndex) {
        var unit = adUnits[unitIndex];
        return googletag.cmd.push(function() {
            var slot = googletag.defineSlot(unit["code"], unit["mediaTypes"]["banner"]["sizes"], id).addService(googletag.pubads());
            var mapping = googletag.sizeMapping()
            .addSize([752, 564], unit["mediaTypes"]["banner"]["sizes"])
            .addSize([0, 0], []).build();
            slot.defineSizeMapping(mapping);
        });
    }

    displayGoogleAds();
});
