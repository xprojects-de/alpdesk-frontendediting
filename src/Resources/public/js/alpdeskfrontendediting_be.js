
class AlpdeskBackend {

  ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';
  static REQUEST_TOKEN = null;
  static CONTAO_BACKEND = null;

  constructor(REQUEST_TOKEN, CONTAO_BACKEND, FRAME) {
    AlpdeskBackend.REQUEST_TOKEN = REQUEST_TOKEN;
    AlpdeskBackend.CONTAO_BACKEND = CONTAO_BACKEND;
    document.getElementById(FRAME).style.height = (window.getHeight() - 150) + 'px';
    window.document.addEventListener(this.ALPDESK_EVENTNAME, this.handleEvent, false);
  }

  static callModal(target, table, id, act) {
    if (AlpdeskBackend.REQUEST_TOKEN !== null && AlpdeskBackend.CONTAO_BACKEND !== null) {
      if (act !== null) {
        AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Alpdesk', 'url': '/contao?alpdeskmodal=1&do=' + target + '&table=' + table + '&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&act=' + act + '&id=' + id});
      } else {
        AlpdeskBackend.CONTAO_BACKEND.openModalIframe({'title': 'Alpdesk', 'url': '/contao?alpdeskmodal=1&do=' + target + '&table=' + table + '&rt=' + AlpdeskBackend.REQUEST_TOKEN + '&id=' + id});
      }
    }
  }

  handleEvent(e) {
    const data = e.detail;
    switch (data.type) {
      case 'article':
      {
        AlpdeskBackend.callModal('article', 'tl_content', data.id, null);
        break;
      }
      case 'content':
      {
        AlpdeskBackend.callModal('article', 'tl_content', data.id, 'edit');
        break;
      }
      default:
        break;
    }

  }
}
