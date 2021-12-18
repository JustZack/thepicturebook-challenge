var ad_sizes_1 = [[970, 250]];
var ad_sizes_2 = [[300, 250]];
var ad_sizes_3 = [[300, 600], [300, 250]];

window.adUnits = [
    {
        // aditude_test1
        code: '/22360860229/Aditude/aditude_test1',
        mediaTypes: {
            banner: {
                sizes: ad_sizes_1,
            },
        },
        bids: [
          {
               bidder: 'rhythmone',
               params: {
                   placementId: '238205',
               },
          },
          {
                bidder: '33across',
                params: {
                    siteId: 'a3U8Oug8Or7ik1aKlKyvbs',
                    productId: 'siab',
                },
            },
            {
                bidder: 'pubmatic',
                params: {
                    publisherId: '160912',
                },
            },
        ],
    },
    {
        // aditude_test2
        code: '/22360860229/Aditude/aditude_test2',
        mediaTypes: {
            banner: {
                sizes: ad_sizes_2,
            },
        },
        bids: [
          {
               bidder: 'rhythmone',
               params: {
                   placementId: '238205',
               },
          },
          {
                bidder: '33across',
                params: {
                    siteId: 'a3U8Oug8Or7ik1aKlKyvbs',
                    productId: 'siab',
                },
            },
            {
                bidder: 'pubmatic',
                params: {
                    publisherId: '160912',
                },
            },
        ],
    },
    {
        // aditude_test3
        code: '/22360860229/Aditude/aditude_test3',
        mediaTypes: {
            banner: {
                sizes: ad_sizes_3,
            },
        },
        bids: [
          {
               bidder: 'rhythmone',
               params: {
                   placementId: '238205',
               },
          },
          {
                bidder: '33across',
                params: {
                    siteId: 'a3U8Oug8Or7ik1aKlKyvbs',
                    productId: 'siab',
                },
            },
            {
                bidder: 'pubmatic',
                params: {
                    publisherId: '160912',
                },
            },
        ],
    },

];

var PREBID_TIMEOUT = 2500;
var FAILSAFE_TIMEOUT = 3e3;

var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];

googletag.cmd.push(function() {
    googletag.pubads().enableSingleRequest(); 
    googletag.pubads().disableInitialLoad();
    googletag.enableServices();
});


var pbjs = pbjs || {};
pbjs.que = pbjs.que || [];

pbjs.que.push(function() {
    pbjs.addAdUnits(adUnits);
    pbjs.setConfig({
        priceGranularity: "high",
    });
    pbjs.requestBids({
        bidsBackHandler: initAdserver,
        timeout: PREBID_TIMEOUT
    });
});

function initAdserver() {
    if (pbjs.initAdserverSet) return;
    pbjs.initAdserverSet = true;
    googletag.cmd.push(function() {
        pbjs.que.push(function() {
            pbjs.setTargetingForGPTAsync();
            //googletag.pubads().refresh();
        });
    });
}

setTimeout(function() {
    initAdserver()
}, FAILSAFE_TIMEOUT);

googletag.cmd.push(function() {
    // Enable SRA and services.
    googletag.pubads().enableSingleRequest();

    //Sample lazy load params seem pretty good
    googletag.pubads().enableLazyLoad({
        fetchMarginPercent: 500,  // Fetch slots within 5 viewports.
        renderMarginPercent: 200,  // Render slots within 2 viewports.
        mobileScaling: 2.0  // Double the above values on mobile.
      });
    googletag.enableServices();
});
