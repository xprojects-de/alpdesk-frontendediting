
(function (window, document) {

  function ready(callback) {
    if (document.readyState !== 'loading') {
      callback();
    } else if (document.addEventListener) {
      document.addEventListener('DOMContentLoaded', callback);
    } else {
      document.attachEvent('onreadystatechange', function () {
        if (document.readyState === 'complete') {
          callback();
        }
      });
    }
  }

  ready(function () {

    document.getElementById('pageselect').onclick = function () {
      Backend.openModalSelector({
        id: 'tl_listing',
        title: 'Frontend-View',
        url: '/contao/picker?context=page&fieldType=radio',
        callback: function (table, value) {
          if (value !== null && value !== undefined) {
            if (value.length > 0) {
              window.location.href = window.location.href + "&pageselect=" + value[0];
            }
          }
        }
      });
    };

  }, false);
})(window, document);
