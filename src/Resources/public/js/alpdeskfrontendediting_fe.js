
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

    const TARGETTYPE_CE = 'ce';
    const TARGETTYPE_MOD = 'mod';

    const ACTION_PARENT_EDIT = 'parent_edit';
    const ACTION_MODULE_EDIT = 'module_edit';
    const ACTION_ELEMENT_EDIT = 'element_edit';
    const ACTION_ELEMENT_NEW = 'element_new';
    const ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    const ACTION_ELEMENT_CUT = 'element_cut';
    const ACTION_ELEMENT_DELETE = 'element_delete';

    var dispatchAlpdeskEvent = function (params) {
      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: params
      }));
    };

    var appendAlpdeskUtilsContainer = function (parent) {
      const c = document.createElement('div');
      c.classList.add('alpdeskfee-utilscontainer');
      parent.appendChild(c);

      let targetType = parent.getAttribute("data-alpdeskfee-type");
      let targetSubType = parent.getAttribute("data-alpdeskfee-subtype");
      let targetDo = parent.getAttribute("data-alpdeskfee-do");
      let targetId = parent.getAttribute("data-alpdeskfee-id");
      let targetPid = parent.getAttribute("data-alpdeskfee-pid");
      let targetPageId = parent.getAttribute("data-alpdeskfee-pageid");

      if (targetType === TARGETTYPE_CE) {

        const pEdit = document.createElement('div');
        pEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
        pEdit.classList.add('alpdeskfee-utilscontainer-pedit');
        c.appendChild(pEdit);
        pEdit.onclick = function (e) {
          dispatchAlpdeskEvent({
            action: ACTION_PARENT_EDIT,
            targetType: targetType,
            targetDo: targetDo,
            id: targetId,
            pid: targetPid,
            targetPageId: targetPageId
          });
        };
      }

      if (targetSubType !== null && targetSubType !== '') {
        const sEdit = document.createElement('div');
        sEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
        sEdit.classList.add('alpdeskfee-utilscontainer-sedit');
        c.appendChild(sEdit);
        sEdit.onclick = function (e) {
          dispatchAlpdeskEvent({
            action: ACTION_MODULE_EDIT,
            targetType: TARGETTYPE_MOD,
            targetDo: targetSubType,
            targetPageId: targetPageId
          });
        };
      }

      const cEdit = document.createElement('div');
      cEdit.classList.add('alpdeskfee-utilscontainer-editcontainer');
      cEdit.classList.add('alpdeskfee-utilscontainer-edit');
      c.appendChild(cEdit);
      cEdit.onclick = function (e) {
        dispatchAlpdeskEvent({
          action: ACTION_ELEMENT_EDIT,
          targetType: targetType,
          targetDo: targetDo,
          id: targetId,
          targetPageId: targetPageId
        });
      };

      if (targetType === TARGETTYPE_CE) {

        const cNew = document.createElement('div');
        cNew.classList.add('alpdeskfee-utilscontainer-editcontainer');
        cNew.classList.add('alpdeskfee-utilscontainer-new');
        c.appendChild(cNew);
        cNew.onclick = function (e) {
          dispatchAlpdeskEvent({
            action: ACTION_ELEMENT_NEW,
            targetType: targetType,
            targetDo: targetDo,
            id: targetId,
            pid: targetPid,
            targetPageId: targetPageId
          });
        };

        const cVisibility = document.createElement('div');
        cVisibility.classList.add('alpdeskfee-utilscontainer-editcontainer');
        cVisibility.classList.add('alpdeskfee-utilscontainer-visible');
        c.appendChild(cVisibility);
        cVisibility.onclick = function (e) {
          dispatchAlpdeskEvent({
            action: ACTION_ELEMENT_VISIBILITY,
            targetType: targetType,
            targetDo: targetDo,
            id: targetId,
            pid: targetPid,
            targetPageId: targetPageId
          });
        };

        const cCut = document.createElement('div');
        cCut.classList.add('alpdeskfee-utilscontainer-editcontainer');
        cCut.classList.add('alpdeskfee-utilscontainer-cut');
        c.appendChild(cCut);
        cCut.onclick = function (e) {
          dispatchAlpdeskEvent({
            action: ACTION_ELEMENT_CUT,
            targetType: targetType,
            targetDo: targetDo,
            id: targetId,
            pid: targetPid,
            targetPageId: targetPageId
          });
        };

        const cDelete = document.createElement('div');
        cDelete.classList.add('alpdeskfee-utilscontainer-editcontainer');
        cDelete.classList.add('alpdeskfee-utilscontainer-delete');
        c.appendChild(cDelete);
        cDelete.onclick = function (e) {
          dispatchAlpdeskEvent({
            action: ACTION_ELEMENT_DELETE,
            targetType: targetType,
            targetDo: targetDo,
            id: targetId,
            pid: targetPid,
            targetPageId: targetPageId
          });
        };

      }

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