var _dcq = _dcq || [];
var _dcs = _dcs || {}; 
_dcs.account = wcdrip.account_id;

(function() {
    var dc = document.createElement('script');
    dc.type = 'text/javascript'; dc.async = true; 
    dc.src = '//tag.getdrip.com/' + wcdrip.account_id + '.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(dc, s);
})();