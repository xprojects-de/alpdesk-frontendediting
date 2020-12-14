
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

    var displayHeader = true;
    var hideHeader = getUrlParam('alpdesk_hideheader');
    if (hideHeader !== null && hideHeader !== undefined && hideHeader == 1) {
      displayHeader = false;
    }
    if (displayHeader === true) {
      let hideHeaderElement = document.querySelectorAll('.tl_header');
      for (let i = 0; i < hideHeaderElement.length; i++) {
        hideHeaderElement[i].style.display = 'block';
      }
    }

    var focusElementId = getUrlParam('alpdeskfocus_listitem');
    if (focusElementId !== null && focusElementId !== undefined) {
      var e = document.getElementById('li_' + focusElementId);
      if (e !== null && e !== undefined) {
        e.classList.add('listing_focus');
        e.style.display = 'block';
      }
    }

    var hideElements = getUrlParam('alpdesk_hideelements');
    if (hideElements !== null && hideElements !== undefined && hideElements == 1) {
      let hideOtherElements = document.querySelectorAll('.tl_listing_container li');
      for (let i = 0; i < hideOtherElements.length; i++) {
        if (hideOtherElements[i].id !== 'li_' + focusElementId) {
          hideOtherElements[i].style.display = 'none';
        }
      }
    }

  }, false);
})(window, document);