
class AlpdeskBackend {

  ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';
  static REQUEST_TOKEN = null;
  static CONTAO_BACKEND = null;
  static FRAME = null;
  static LOADING = null;

  static TARGETTYPE_CE = 'ce';
  static TARGETTYPE_MOD = 'mod';

  static ACTION_PARENT_EDIT = 'parent_edit';
  static ACTION_MODULE_EDIT = 'module_edit';
  static ACTION_ELEMENT_EDIT = 'element_edit';
  static ACTION_ELEMENT_NEW = 'element_new';
  static ACTION_ELEMENT_VISIBILITY = 'element_visibility';
  static ACTION_ELEMENT_CUT = 'element_cut';
  static ACTION_ELEMENT_DELETE = 'element_delete';

  static MODAL_TITLE = 'Frontend-View';

  constructor(REQUEST_TOKEN, CONTAO_BACKEND, FRAME, LOADING) {
    AlpdeskBackend.REQUEST_TOKEN = REQUEST_TOKEN;
    AlpdeskBackend.CONTAO_BACKEND = CONTAO_BACKEND;
    AlpdeskBackend.FRAME = FRAME;
    AlpdeskBackend.LOADING = LOADING;
    window.document.addEventListener(this.ALPDESK_EVENTNAME, AlpdeskBackend.handleEvent, false);
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
    if (data.targetType === AlpdeskBackend.TARGETTYPE_CE) {
      if (data.action === AlpdeskBackend.ACTION_PARENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.pid});
      } else if (data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&act=edit&id=' + data.id});
      } else if (
              data.action === AlpdeskBackend.ACTION_ELEMENT_CUT ||
              data.action === AlpdeskBackend.ACTION_ELEMENT_NEW
              ) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&alpdesk_hideheader=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.pid});
      } else if (
              data.action === AlpdeskBackend.ACTION_ELEMENT_VISIBILITY ||
              data.action === AlpdeskBackend.ACTION_ELEMENT_DELETE
              ) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&alpdesk_hideheader=1&alpdesk_hideelements=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.targetDo + '&table=tl_content&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + data.pid});
      }
    } else if (data.targetType === AlpdeskBackend.TARGETTYPE_MOD) {
      if (
              data.action === AlpdeskBackend.ACTION_MODULE_EDIT ||
              data.action === AlpdeskBackend.ACTION_ELEMENT_EDIT
              ) {
        AlpdeskBackend.callModal({'title': AlpdeskBackend.MODAL_TITLE, 'url': '/contao?alpdeskmodal=1&do=' + data.targetDo + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      }

    }

  }
}
