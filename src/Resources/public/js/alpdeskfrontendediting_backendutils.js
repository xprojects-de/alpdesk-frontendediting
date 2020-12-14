
(function (window, document) {

  function ready(callback) {
    if (document.readyState !== 'loading') {
      callback();
    } else if (document.addEventListener) {
      document.addEventListener('DOMContentLoaded', callback);
    } else {
      document.attachEvent('onreadystatechange', function () {
        if (document.readyState === 'complete')
          callback();
      });
    }
  }

  ready(function () {

    var getUrlParam = function getSearchParams(k) {
      var p = {};
      location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (s, k, v) {
        p[k] = v;
      });
      return k ? p[k] : p;
    };

    var focusElementId = getUrlParam('alpdeskfocus_listitem');
    if (focusElementId !== null && focusElementId !== undefined) {
      var e = document.getElementById('li_' + focusElementId);
      if (e !== null && e !== undefined) {
        e.classList.add('listing_focus');
      }
    }

  }, false);
})(window, document);