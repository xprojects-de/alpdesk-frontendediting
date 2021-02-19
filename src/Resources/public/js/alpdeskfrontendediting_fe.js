
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

    const ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

    if (alpdeskfeeLabels !== null && alpdeskfeeLabels !== undefined && alpdeskfeeLabels !== '') {

      let objLabels = JSON.parse(alpdeskfeeLabels);

      let canPageEdit = false;
      if (alpdeskfeeCanPageEdit !== undefined && alpdeskfeeCanPageEdit !== null && alpdeskfeeCanPageEdit === 1) {
        canPageEdit = true;
      }

      let pageId = 0;
      if (alpdeskfeePageid !== undefined && alpdeskfeePageid !== null && alpdeskfeePageid !== 0) {
        pageId = alpdeskfeePageid;
      }

      let accessFilemanagement = false;
      if (alpdeskfeeAccessFilemanagement !== undefined && alpdeskfeeAccessFilemanagement !== null && alpdeskfeeAccessFilemanagement !== 0) {
        accessFilemanagement = true;
      }

      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: {
          action: 'init',
          labels: objLabels,
          pageEdit: canPageEdit,
          pageId: pageId,
          accessFilemanagement: accessFilemanagement
        }
      }));
    }

  }, false);
})(window, document);