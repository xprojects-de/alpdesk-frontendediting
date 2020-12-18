
// Currently all is static so no instance can be created
class AlpdeskBackend {

  static init(REQUEST_TOKEN, CONTAO_BACKEND, FRAME, LOADING, FRAMECHANGEDEVENT) {

    AlpdeskBackend.REQUEST_TOKEN = REQUEST_TOKEN;
    AlpdeskBackend.CONTAO_BACKEND = CONTAO_BACKEND;
    AlpdeskBackend.FRAME = FRAME;
    AlpdeskBackend.LOADING = LOADING;
    AlpdeskBackend.FRAMECHANGEDEVENT = FRAMECHANGEDEVENT;

    AlpdeskBackend.ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

    AlpdeskBackend.TARGETTYPE_PAGE = 'page';
    AlpdeskBackend.TARGETTYPE_ARTICLE = 'article';
    AlpdeskBackend.TARGETTYPE_CE = 'ce';
    AlpdeskBackend.TARGETTYPE_MOD = 'mod';

    AlpdeskBackend.ACTION_PARENT_EDIT = 'parent_edit';
    AlpdeskBackend.ACTION_ELEMENT_EDIT = 'element_edit';
    AlpdeskBackend.ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    AlpdeskBackend.ACTION_ELEMENT_DELETE = 'element_delete';
    AlpdeskBackend.ACTION_ELEMENT_SHOW = 'element_show';
    AlpdeskBackend.ACTION_ELEMENT_NEW = 'element_new';

    AlpdeskBackend.MODAL_TITLE = 'Frontend-View';

    window.document.addEventListener(AlpdeskBackend.ALPDESK_EVENTNAME, AlpdeskBackend.handleEvent, false);

    document.getElementById(AlpdeskBackend.FRAME).onload = function () {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'none';
      window.document.dispatchEvent(new CustomEvent(AlpdeskBackend.FRAMECHANGEDEVENT, {
        detail: {
          location: document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.href
        }
      }));
    };

  }

  static modalCloseListener() {
    const modalOverlay = document.getElementById('simple-modal-overlay');
    modalOverlay.onclick = function (e) {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    };
    const modal = document.getElementById('simple-modal');
    for (let i = 0; i < modal.childNodes.length; i++) {
      if (modal.childNodes[i].className === 'close') {
        modal.childNodes[i].onclick = function (e) {
          document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
          document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
        };
        break;
      }
    }
  }

  static callModal(params) {
    if (AlpdeskBackend.REQUEST_TOKEN !== null && AlpdeskBackend.CONTAO_BACKEND !== null) {
      AlpdeskBackend.CONTAO_BACKEND.openModalIframe(params);
      AlpdeskBackend.modalCloseListener();
    }
  }

  static callVisibilityArticle(id, state) {
    new Request.Contao({
      'url': '/contao?do=article&tid=' + id + '&state=' + state + '&rt=' + AlpdeskBackend.REQUEST_TOKEN,
      followRedirects: false,
      onSuccess: function (txt, json) {
        // Callback not working!!!!
      }
    }).get();
    // No fine but onSuccess-Callback not working
    setTimeout(function () {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    }, 400);
  }

  static callVisibilityElement(pid, id, state) {
    new Request.Contao({
      'url': '/contao?do=article&table=tl_content&id=' + pid + '&cid=' + id + '&state=' + state + '&rt=' + AlpdeskBackend.REQUEST_TOKEN,
      followRedirects: false,
      onSuccess: function (txt, json) {
        // Callback not working!!!!
      }
    }).get();
    // No fine but onSuccess-Callback not working
    setTimeout(function () {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    }, 400);
  }

  static callDeleteArticle(id) {
    new Request.Contao({
      'url': '/contao?do=article&act=delete&id=' + id + '&rt=' + AlpdeskBackend.REQUEST_TOKEN,
      followRedirects: false,
      onSuccess: function (txt, json) {
        // Callback not working!!!!
      }
    }).get();
    // No fine but onSuccess-Callback not working
    setTimeout(function () {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    }, 400);
  }

  static callDeleteElement(id) {
    new Request.Contao({
      'url': '/contao?do=article&table=tl_content&act=delete&id=' + id + '&rt=' + AlpdeskBackend.REQUEST_TOKEN,
      followRedirects: false,
      onSuccess: function (txt, json) {
        // Callback not working!!!!
      }
    }).get();
    // No fine but onSuccess-Callback not working
    setTimeout(function () {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    }, 400);
  }

  static handleEvent(e) {
    const data = e.detail;
    if (data.targetType === AlpdeskBackend.TARGETTYPE_PAGE) {
      if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&act=edit&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_SHOW) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&pn=' + data.targetPageId + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_ARTICLE) {
      if (data.action === AlpdeskBackend.ACTION_PARENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&act=edit&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_SHOW) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&pn=' + data.targetPageId + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_NEW) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&table=tl_content&id=' + data.id + '&act=create&mode=2&pid=' + data.id + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_VISIBILITY) {
        AlpdeskBackend.callVisibilityArticle(data.id, data.state);
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_DELETE) {
        AlpdeskBackend.callDeleteArticle(data.id);
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_CE) {
      if (data.action === AlpdeskBackend.ACTION_PARENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&alpdesk_hideheader=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.pid});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&act=edit&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_NEW) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&do=' + data.targetDo + '&table=tl_content&id=' + data.pid + '&act=create&mode=1&pid=' + data.id + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_VISIBILITY) {
        AlpdeskBackend.callVisibilityElement(data.pid, data.id, data.state);
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_DELETE) {
        AlpdeskBackend.callDeleteElement(data.id);
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_MOD) {
      AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&popup=1&' + data.targetDo + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
    }
  }
}

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
    const frameChangedEvent = 'alpdesk_frontendediting_framechangedEvent';
    const rt = document.getElementById('alpdesk-fee-frame').getAttribute('data-request-token');
    const base = document.getElementById('alpdesk-fee-frame').getAttribute('data-base') + previewLabel + '/';

    AlpdeskBackend.init(rt, Backend, 'alpdesk-fee-frame', 'alpdesk-fee-alpdeskloading', frameChangedEvent);

    let initHeight = (window.getHeight() - 200);
    const phone_1 = 375;
    const phone_2 = 667;
    const tablet_1 = 760;
    const tablet_2 = 1024;

    document.getElementById('alpdesk-fee-frame-container').style.height = initHeight + 'px';
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
    };

    function handleUrlParam() {
      document.getElementById('alpdesk-fee-alpdeskloading').style.display = 'block';
      let urlparam = document.getElementById('urlparam').value;
      if (urlparam === null || urlparam === undefined || urlparam === '') {
        urlparam = '/' + previewLabel;
      } else {
        urlparam = '/' + previewLabel + '/' + urlparam;
      }
      document.getElementById('alpdesk-fee-frame').src = urlparam;
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

    window.document.addEventListener(frameChangedEvent, function (e) {
      document.getElementById('urlparam').value = e.detail.location.replace(base, '');
      document.getElementById('alpdesk-fee-alpdeskloading').style.display = 'none';
    });

  }, false);
})(window, document);
