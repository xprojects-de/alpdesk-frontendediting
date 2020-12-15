
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

    const TARGETTYPE_PAGE = 'page';
    const TARGETTYPE_ARTICLE = 'article';
    const TARGETTYPE_CE = 'ce';
    const TARGETTYPE_MOD = 'mod';

    const ACTION_PARENT_EDIT = 'parent_edit';
    const ACTION_ELEMENT_EDIT = 'element_edit';
    const ACTION_ELEMENT_SHOW = 'element_show';

    let globalTargetPageId = null;
    let showPageEdit = false;

    function dispatchEvent(params) {
      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: params
      }));
    }

    function createContainerElement(parent, elementclass) {
      const element = document.createElement('div');
      element.classList.add('alpdeskfee-utilscontainer-editcontainer');
      element.classList.add(elementclass);
      parent.appendChild(element);
      return element;
    }

    function appendUtilsContainer(parent, notUseParent) {

      let targetType = parent.getAttribute('data-alpdeskfee-type');
      let targetDesc = parent.getAttribute('data-alpdeskfee-desc');
      let targetSubType = parent.getAttribute("data-alpdeskfee-subtype");
      let targetDo = parent.getAttribute('data-alpdeskfee-do');
      let targetId = parent.getAttribute('data-alpdeskfee-id');
      let targetPid = parent.getAttribute('data-alpdeskfee-pid');
      let targetPageId = parent.getAttribute('data-alpdeskfee-pageid');

      let targetChmodArticleEdit = parent.getAttribute('data-alpdeskfee-articleChmodEdit');
      let canEditArticle = true;
      if (targetChmodArticleEdit !== null && targetChmodArticleEdit !== undefined) {
        canEditArticle = (targetChmodArticleEdit != 1 ? false : true);
      }

      if (globalTargetPageId === null || globalTargetPageId === '' || globalTargetPageId === undefined) {
        if (targetPageId !== null && targetPageId !== undefined) {
          globalTargetPageId = targetPageId;
        }
      }

      if (showPageEdit === false) {
        showPageEdit = (parent.getAttribute('data-alpdeskfee-chmodpageedit') == 1);
      }

      let c = parent;
      if (notUseParent === true) {
        c = document.createElement('div');
        parent.appendChild(c);
      }

      if (targetType === TARGETTYPE_PAGE) {
        c.classList.add('alpdeskfee-utilscontainer-page');
      } else {
        c.classList.add('alpdeskfee-utilscontainer');
      }

      if (targetDesc !== null && targetDesc !== undefined && targetDesc !== '') {
        const cDesc = document.createElement('div');
        cDesc.classList.add('alpdeskfee-utilscontainer-desc');
        c.appendChild(cDesc);
        cDesc.innerHTML = targetDesc;
      }

      if (targetType === TARGETTYPE_PAGE) {
        const cEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
        cEdit.onclick = function () {
          dispatchEvent({
            action: ACTION_ELEMENT_EDIT,
            targetType: targetType,
            targetDo: targetDo,
            id: targetId,
            targetPageId: targetPageId
          });
        };
        const cShow = createContainerElement(c, 'alpdeskfee-utilscontainer-root');
        cShow.onclick = function () {
          dispatchEvent({
            action: ACTION_ELEMENT_SHOW,
            targetType: TARGETTYPE_PAGE,
            targetDo: TARGETTYPE_PAGE,
            id: 0,
            targetPageId: targetPageId
          });
        };
        const cEditArticles = createContainerElement(c, 'alpdeskfee-utilscontainer-rootarticle');
        cEditArticles.onclick = function () {
          dispatchEvent({
            action: ACTION_ELEMENT_SHOW,
            targetType: TARGETTYPE_ARTICLE,
            targetDo: TARGETTYPE_ARTICLE,
            id: 0,
            targetPageId: targetPageId
          });
        };
      } else if (targetType === TARGETTYPE_ARTICLE) {
        if (canEditArticle === true) {
          const cEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-articles');
          cEdit.onclick = function () {
            dispatchEvent({
              action: ACTION_ELEMENT_EDIT,
              targetType: targetType,
              targetDo: targetDo,
              id: targetId,
              targetPageId: targetPageId
            });
          };
          const pEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
          pEdit.onclick = function () {
            dispatchEvent({
              action: ACTION_PARENT_EDIT,
              targetType: targetType,
              targetDo: targetDo,
              id: targetId,
              pid: targetPid,
              targetPageId: targetPageId
            });
          };
        }
      } else if (targetType === TARGETTYPE_CE) {
        if (canEditArticle === true) {
          const pEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-pedit');
          pEdit.onclick = function () {
            dispatchEvent({
              action: ACTION_PARENT_EDIT,
              targetType: targetType,
              targetDo: targetDo,
              id: targetId,
              pid: targetPid,
              targetPageId: targetPageId
            });
          };
          const cEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
          cEdit.onclick = function () {
            dispatchEvent({
              action: ACTION_ELEMENT_EDIT,
              targetType: targetType,
              targetDo: targetDo,
              id: targetId,
              targetPageId: targetPageId
            });
          };
          if (targetSubType !== null && targetSubType !== '') {
            const sEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-sedit');
            sEdit.onclick = function () {
              dispatchEvent({
                action: ACTION_ELEMENT_EDIT,
                targetType: TARGETTYPE_MOD,
                targetDo: targetSubType,
                targetPageId: targetPageId
              });
            };
          }
        }
      } else if (targetType === TARGETTYPE_MOD) {
        if (canEditArticle === true) {
          const cEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
          cEdit.onclick = function () {
            dispatchEvent({
              action: ACTION_ELEMENT_EDIT,
              targetType: targetType,
              targetDo: targetDo,
              id: targetId,
              targetPageId: targetPageId
            });
          };
        }
      }

      const cClear = document.createElement('div');
      cClear.classList.add('alpdeskfee-utilscontainer-clearcontainer');
      c.appendChild(cClear);

    }

    function scanElements() {
      let data = document.querySelectorAll("*[data-alpdeskfee-type]");
      for (let i = 0; i < data.length; i++) {
        if (data[i].getAttribute('data-alpdeskfee-type') === TARGETTYPE_ARTICLE) {
          let parentNode = data[i].parentElement;
          parentNode.classList.add('alpdeskfee-article-container');
          appendUtilsContainer(data[i], false);
          parentNode.onmouseover = function () {
            data[i].classList.add("alpdeskfee-parent-active");
          };
          parentNode.onmouseout = function () {
            data[i].classList.remove("alpdeskfee-parent-active");
          };
        } else {
          appendUtilsContainer(data[i], true);
          data[i].onmouseover = function () {
            data[i].classList.add("alpdeskfee-active");
          };
          data[i].onmouseout = function () {
            data[i].classList.remove("alpdeskfee-active");
          };
        }
      }
    }

    function checkInIframe() {
      return (window.location !== window.parent.location);
    }

    // Maybe problem at MultiDomain-Webpage
    if (checkInIframe() === true) {

      scanElements();

      if (showPageEdit === true && globalTargetPageId !== null && globalTargetPageId !== '' && globalTargetPageId !== undefined && globalTargetPageId !== 0) {
        const bodyElement = document.body;
        if (bodyElement !== null && bodyElement !== undefined) {
          bodyElement.setAttribute('data-alpdeskfee-type', TARGETTYPE_PAGE);
          bodyElement.setAttribute('data-alpdeskfee-desc', 'Page');
          bodyElement.setAttribute('data-alpdeskfee-do', TARGETTYPE_PAGE);
          bodyElement.setAttribute('data-alpdeskfee-id', globalTargetPageId);
          bodyElement.setAttribute('data-alpdeskfee-pageid', globalTargetPageId);
          appendUtilsContainer(bodyElement, true);
        }
      }

    }

  }, false);
})(window, document);