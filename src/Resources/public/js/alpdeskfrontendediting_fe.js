
(function (window, document) {

  function ready(callback) {
    if (document.readyState !== 'loading') {
      callback();
    } else if (document.addEventListener) {
      document.addEventListener('DOMContentLoaded', callback);
    } else {
      document.attachEvent('onreadystatechange', function () {
        if (document.readyState === 'complete')
          callback();
      });
    }
  }

  ready(function () {

    const ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

    var dispatchAlpdeskEvent = function (targetType, targetPageId, targetDo, id, pid) {
      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: {
          targetType: targetType,
          targetDo: targetDo,
          id: id,
          pid: pid,
          targetPageId: targetPageId
        }
      }));
    };

    var appendAlpdeskUtilsContainer = function (parent) {
      const c = document.createElement('div');
      c.classList.add('alpdeskfee-utilscontainer');
      parent.appendChild(c);

      let targetType = parent.getAttribute("data-alpdeskfee-type");
      let targetSubType = parent.getAttribute("data-alpdeskfee-subtype");
      let targetDesc = parent.getAttribute("data-alpdeskfee-desc");
      let targetDo = parent.getAttribute("data-alpdeskfee-do");
      let targetId = parent.getAttribute("data-alpdeskfee-id");
      let targetPid = parent.getAttribute("data-alpdeskfee-pid");
      let targetPageId = parent.getAttribute("data-alpdeskfee-pageid");

      /*const desc = document.createElement('div');
       desc.classList.add('alpdeskfee-utilscontainer-desccontainer');
       desc.innerHTML = targetDesc;
       c.appendChild(desc);*/

      if (targetType === 'ce') {
        const pEdit = document.createElement('div');
        pEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
        pEdit.classList.add('alpdeskfee-utilscontainer-pedit');
        c.appendChild(pEdit);
        pEdit.onclick = function (e) {
          if (targetType !== null && targetDo !== null) {
            dispatchAlpdeskEvent(targetType, targetPageId, targetDo, targetId, targetPid);
          }
        };
      }

      if (targetSubType !== null && targetSubType !== '') {
        const sEdit = document.createElement('div');
        sEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
        sEdit.classList.add('alpdeskfee-utilscontainer-sedit');
        c.appendChild(sEdit);
        sEdit.onclick = function (e) {
          if (targetType !== null && targetDo !== null) {
            dispatchAlpdeskEvent('mod', targetPageId, targetSubType, null, null);
          }
        };
      }

      const cEdit = document.createElement('div');
      cEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
      cEdit.classList.add('alpdeskfee-utilscontainer-edit');
      c.appendChild(cEdit);
      cEdit.onclick = function (e) {
        if (targetType !== null && targetDo !== null) {
          dispatchAlpdeskEvent(targetType, targetPageId, targetDo, targetId, null);
        }
      };

      const cClear = document.createElement('div');
      cClear.classList.add('alpdeskfee-utilscontainer-clearcontainer');
      c.appendChild(cClear);

    };

    var scanAlpdeskElements = function () {
      let data = document.querySelectorAll("*[data-alpdeskfee-type]");
      for (let i = 0; i < data.length; i++) {
        appendAlpdeskUtilsContainer(data[i]);
        data[i].onmouseover = function () {
          data[i].classList.add("alpdeskfee-active");
        };
        data[i].onmouseout = function () {
          data[i].classList.remove("alpdeskfee-active");
        };
      }
    };

    scanAlpdeskElements();

  }, false);
})(window, document);