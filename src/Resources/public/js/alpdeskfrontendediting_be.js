
class AlpdeskBackend {

  static init(REQUEST_TOKEN, CONTAO_BACKEND, FRAME, LOADING) {

    AlpdeskBackend.REQUEST_TOKEN = REQUEST_TOKEN;
    AlpdeskBackend.CONTAO_BACKEND = CONTAO_BACKEND;
    AlpdeskBackend.FRAME = FRAME;
    AlpdeskBackend.LOADING = LOADING;
    AlpdeskBackend.ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';
    AlpdeskBackend.TARGETTYPE_PAGE = 'page';
    AlpdeskBackend.TARGETTYPE_ARTICLE = 'article';
    AlpdeskBackend.TARGETTYPE_CE = 'ce';
    AlpdeskBackend.TARGETTYPE_MOD = 'mod';
    AlpdeskBackend.ACTION_PARENT_EDIT = 'parent_edit';
    AlpdeskBackend.ACTION_ELEMENT_EDIT = 'element_edit';
    AlpdeskBackend.ACTION_ELEMENT_SHOW = 'element_show';
    AlpdeskBackend.MODAL_TITLE = 'Frontend-View';

    window.document.addEventListener(AlpdeskBackend.ALPDESK_EVENTNAME, AlpdeskBackend.handleEvent, false);
    AlpdeskBackend.iframeLoaded();

  }

  static iframeLoaded() {
    document.getElementById(AlpdeskBackend.FRAME).onload = function () {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'none';
    };
  }

  static modalCloseListener() {
    const modalOverlay = document.getElementById('simple-modal-overlay');
    modalOverlay.onclick = function (e) {
      document.getElementById(AlpdeskBackend.LOADING).style.display = 'block';
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    };
    const modal = document.getElementById('simple-modal');
    for (var i = 0; i < modal.childNodes.length; i++) {
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

  static handleEvent(e) {
    const data = e.detail;
    if (data.targetType === AlpdeskBackend.TARGETTYPE_PAGE) {
      if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&act=edit&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_SHOW) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&pn=' + data.targetPageId + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_ARTICLE) {
      if (data.action === AlpdeskBackend.ACTION_PARENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&act=edit&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.id});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_SHOW) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&pn=' + data.targetPageId + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_CE) {
      if (data.action === AlpdeskBackend.ACTION_PARENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&alpdesk_hideheader=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.pid});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&act=edit&id=' + data.id});
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_MOD) {
      if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      }

    }
  }
}
