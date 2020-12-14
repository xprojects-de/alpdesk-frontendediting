
class AlpdeskBackend {

  ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';
  static REQUEST_TOKEN = null;
  static CONTAO_BACKEND = null;
  static FRAME = null;
  static LOADING = null;

  constructor(REQUEST_TOKEN, CONTAO_BACKEND, FRAME, LOADING) {
    AlpdeskBackend.REQUEST_TOKEN = REQUEST_TOKEN;
    AlpdeskBackend.CONTAO_BACKEND = CONTAO_BACKEND;
    AlpdeskBackend.FRAME = FRAME;
    AlpdeskBackend.LOADING = LOADING;
    document.getElementById(AlpdeskBackend.FRAME).style.height = (window.getHeight() - 150) + 'px';
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

  static callModal(targetDo, table, id, pid, act) {
    if (AlpdeskBackend.REQUEST_TOKEN !== null && AlpdeskBackend.CONTAO_BACKEND !== null) {
      if (id !== null) {
        if (act !== null) {
          AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Frontend-View', 'url': '/contao?alpdeskmodal=1&do=' + targetDo + '&table=' + table + '&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&act=' + act + '&id=' + id});
        } else {
          AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Frontend-View', 'url': '/contao?alpdeskmodal=1&alpdeskfocus_listitem=' + id + '&do=' + targetDo + '&table=' + table + '&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + pid});
        }
      } else {
        AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Frontend-View', 'url': '/contao?alpdeskmodal=1&do=' + targetDo + '&rt=' + AlpdeskBackend.REQUEST_TOKEN});
      }
      AlpdeskBackend.modalCloseListener();
    }
  }

  static handleEvent(e) {
    const data = e.detail;
    if (data.targetType === 'ce') {
      if (data.pid !== null) {
        AlpdeskBackend.callModal(data.targetDo, 'tl_content', data.id, data.pid, null);
      } else {
        AlpdeskBackend.callModal(data.targetDo, 'tl_content', data.id, data.pid, 'edit');
      }
    } else if (data.targetType === 'mod') {
      AlpdeskBackend.callModal(data.targetDo, null, null, null, null);
    }

  }
}
