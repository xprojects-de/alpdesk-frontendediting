
(function (window, document) {
  document.addEventListener('DOMContentLoaded', function () {

    const ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

    var dispatchAlpdeskEvent = function (type, id, pid, ptable) {
      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: {
          type: type,
          id: id,
          pid: pid,
          ptable: ptable
        }
      }));
    };

    var appendAlpdeskUtilsContainer = function (parent) {
      const c = document.createElement('div');
      c.classList.add('alpdeskfee-utilscontainer');
      parent.appendChild(c);

      const pEdit = document.createElement('div');
      pEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
      pEdit.classList.add('alpdeskfee-utilscontainer-pedit');
      c.appendChild(pEdit);
      pEdit.onclick = function (e) {
        let targetId = parent.getAttribute("data-alpdeskfee-id");
        let targetPid = parent.getAttribute("data-alpdeskfee-pid");
        let targetPtable = parent.getAttribute("data-alpdeskfee-ptable");
        if (targetId !== null && targetPid !== null && targetPtable !== null) {
          dispatchAlpdeskEvent('parent', targetId, targetPid, targetPtable);
        }
      };

      const cEdit = document.createElement('div');
      cEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
      cEdit.classList.add('alpdeskfee-utilscontainer-edit');
      c.appendChild(cEdit);
      cEdit.onclick = function (e) {
        let targetId = parent.getAttribute("data-alpdeskfee-id");
        let targetPid = parent.getAttribute("data-alpdeskfee-pid");
        let targetPtable = parent.getAttribute("data-alpdeskfee-ptable");
        if (targetId !== null && targetPid !== null && targetPtable !== null) {
          dispatchAlpdeskEvent('element', targetId, targetPid, targetPtable);
        }
      };

    };

    var scanAlpdeskElements = function () {
      let data = document.querySelectorAll("div[data-alpdeskfee-id]");
      for (let i = 0; i < data.length; i++) {
        appendAlpdeskUtilsContainer(data[i]);
        data[i].onmouseover = function (e) {
          data[i].classList.add("alpdeskfee-active");
        };
        data[i].onmouseout = function (e) {
          data[i].classList.remove("alpdeskfee-active");
        };
      }
    };

    scanAlpdeskElements();

  }, false);
})(window, document);