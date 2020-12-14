
class AlpdeskBackend {

  ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';
  static REQUEST_TOKEN = null;
  static CONTAO_BACKEND = null;
  static FRAME = null;

  constructor(REQUEST_TOKEN, CONTAO_BACKEND, FRAME) {
    AlpdeskBackend.REQUEST_TOKEN = REQUEST_TOKEN;
    AlpdeskBackend.CONTAO_BACKEND = CONTAO_BACKEND;
    AlpdeskBackend.FRAME = FRAME;
    document.getElementById(AlpdeskBackend.FRAME).style.height = (window.getHeight() - 150) + 'px';
    window.document.addEventListener(this.ALPDESK_EVENTNAME, AlpdeskBackend.handleEvent, false);
  }

  static modalCloseListener() {
    const modalOverlay = document.getElementById('simple-modal-overlay');
    modalOverlay.onclick = function (e) {
      document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
    };
    const modal = document.getElementById('simple-modal');
    for (var i = 0; i < modal.childNodes.length; i++) {
      if (modal.childNodes[i].className === 'close') {
        modal.childNodes[i].onclick = function (e) {
          document.getElementById(AlpdeskBackend.FRAME).contentWindow.location.reload();
        };
        break;
      }
    }

  }

  static callModal(target, table, id, act) {
    if (AlpdeskBackend.REQUEST_TOKEN !== null && AlpdeskBackend.CONTAO_BACKEND !== null) {
      if (act !== null) {
        AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Alpdesk', 'url': '/contao?alpdeskmodal=1&do=' + target + '&table=' + table + '&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&act=' + act + '&id=' + id});
      } else {
        AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Alpdesk', 'url': '/contao?alpdeskmodal=1&do=' + target + '&table=' + table + '&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + id});
      }
      AlpdeskBackend.modalCloseListener();
    }
  }

  static handleEvent(e) {
    const data = e.detail;
    switch (data.type) {
      case 'parent':
      {
        AlpdeskBackend.callModal(data.ptable, 'tl_content', data.pid, null);
        break;
      }
      case 'element':
      {
        AlpdeskBackend.callModal(data.ptable, 'tl_content', data.id, 'edit');
        break;
      }
      default:
        break;
    }

  }
}
