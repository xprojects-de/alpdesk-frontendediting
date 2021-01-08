
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

    const previewLabel = 'preview.php';
    const ALPDESK_EVENTNAME_FRAME = 'alpdesk_frontendediting_framechangedEvent';
    const ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';
    const base = document.getElementById('alpdesk-fee-frame-container').getAttribute('data-base') + previewLabel + '/';

    let initHeight = (window.getHeight() - 200);
    const phone_1 = 375;
    const phone_2 = 667;
    const tablet_1 = 760;
    const tablet_2 = 1024;

    document.getElementById('devicedimensoninfo').innerHTML = document.getElementById('alpdesk-fee-frame-container').offsetWidth + ' x ' + initHeight;

    document.getElementById('alpdesk-fee-frame-container').style.height = initHeight + 'px';
    document.getElementById('alpdesk-fee-frame-container').onmouseover = function () {
      document.getElementById('devicedimensoninfo').innerHTML = document.getElementById('alpdesk-fee-frame-container').offsetWidth + ' x ' + document.getElementById('alpdesk-fee-frame-container').offsetHeight;
    };
    document.getElementById('alpdesk-fee-frame-container').onmouseout = function () {
      document.getElementById('devicedimensoninfo').innerHTML = document.getElementById('alpdesk-fee-frame-container').offsetWidth + ' x ' + document.getElementById('alpdesk-fee-frame-container').offsetHeight;
    };

    document.getElementById('setdevice').onclick = function () {
      let device = document.getElementById('getdevice').value;
      if (device === 'phone') {
        document.getElementById('alpdesk-fee-frame-container').style.width = phone_1 + 'px';
        document.getElementById('alpdesk-fee-frame-container').style.height = (initHeight < phone_2 ? initHeight : phone_2) + 'px';
      } else if (device === 'phone_landscape') {
        document.getElementById('alpdesk-fee-frame-container').style.height = (initHeight < phone_1 ? initHeight : phone_1) + 'px';
        document.getElementById('alpdesk-fee-frame-container').style.width = phone_2 + 'px';
      } else if (device === 'tablet') {
        document.getElementById('alpdesk-fee-frame-container').style.width = tablet_1 + 'px';
        document.getElementById('alpdesk-fee-frame-container').style.height = (initHeight < tablet_2 ? initHeight : tablet_2) + 'px';
      } else if (device === 'tablet_landscape') {
        document.getElementById('alpdesk-fee-frame-container').style.height = (initHeight < tablet_1 ? initHeight : tablet_1) + 'px';
        document.getElementById('alpdesk-fee-frame-container').style.width = tablet_2 + 'px';
      } else {
        document.getElementById('alpdesk-fee-frame-container').style.width = '100%';
        document.getElementById('alpdesk-fee-frame-container').style.height = initHeight + 'px';
      }

      document.getElementById('devicedimensoninfo').innerHTML = document.getElementById('alpdesk-fee-frame-container').offsetWidth + ' x ' + document.getElementById('alpdesk-fee-frame-container').offsetHeight;
    };

    function handleUrlParam() {

      document.getElementById('alpdesk-fee-alpdeskloading').style.display = 'block';

      let urlparam = document.getElementById('urlparam').value;

      if (urlparam === null || urlparam === undefined || urlparam === '') {
        urlparam = '/' + previewLabel;
      } else {
        urlparam = '/' + previewLabel + '/' + urlparam;
      }

      document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: {
          framelocation: urlparam
        }
      }));

    }

    document.getElementById('seturl').onclick = function () {
      handleUrlParam();
    };

    document.getElementById('urlparam').onkeypress = function (e) {
      // Enter pressed
      if (e.keyCode === 13) {
        handleUrlParam();
      }
    };

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

    document.addEventListener(ALPDESK_EVENTNAME_FRAME, function (e) {
      console.log(e.detail);
      if (e.detail.location !== null && e.detail.location !== undefined && e.detail.location !== '') {
        document.getElementById('urlparam').value = e.detail.location.replace(base, '');
        document.getElementById('alpdesk-fee-alpdeskloading').style.display = 'none';
      } else if (e.detail.reload !== null && e.detail.reload !== undefined && e.detail.reload === true) {
        document.getElementById('alpdesk-fee-alpdeskloading').style.display = 'block';
      }
    });

  }, false);
})(window, document);
