
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
    const ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    const ACTION_ELEMENT_DELETE = 'element_delete';
    const ACTION_ELEMENT_SHOW = 'element_show';
    const ACTION_ELEMENT_NEW = 'element_new';

    function dispatchEvent(params) {
      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: params
      }));
    }

    function createContainerElement(parent, elementclass) {
      const element = document.createElement('div');
      element.classList.add('alpdeskfee-utilscontainer-editcontainer');
      if (elementclass !== '') {
        element.classList.add(elementclass);
      }
      parent.appendChild(element);
      return element;
    }

    function appendUtilsContainer(obj, parent, notUseParent, objLabels) {

      if (obj !== null && obj !== undefined) {

        let c = parent;
        if (notUseParent === true) {
          c = document.createElement('div');
          parent.appendChild(c);
        }

        if (obj.type === TARGETTYPE_PAGE) {
          c.classList.add('alpdeskfee-utilscontainer-page');
        } else {
          c.classList.add('alpdeskfee-utilscontainer');
        }

        if (obj.desc !== null && obj.desc !== undefined && obj.desc !== '') {
          const cDesc = document.createElement('div');
          cDesc.classList.add('alpdeskfee-utilscontainer-desc');
          c.appendChild(cDesc);
          cDesc.innerHTML = obj.desc;
        }

        if (obj.type === TARGETTYPE_PAGE) {
          const pageEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
          pageEdit.setAttribute('title', objLabels.page_edit_top);
          pageEdit.onclick = function () {
            dispatchEvent({
              action: ACTION_ELEMENT_EDIT,
              targetType: TARGETTYPE_PAGE,
              targetDo: obj.do,
              id: obj.id,
              targetPageId: obj.pageid
            });
          };
          const cShow = createContainerElement(c, 'alpdeskfee-utilscontainer-root');
          cShow.setAttribute('title', objLabels.page_structure);
          cShow.onclick = function () {
            dispatchEvent({
              action: ACTION_ELEMENT_SHOW,
              targetType: TARGETTYPE_PAGE,
              targetDo: TARGETTYPE_PAGE,
              targetPageId: obj.pageid
            });
          };
          // Mabye check if Articles enabled!!!
          const cEditArticles = createContainerElement(c, 'alpdeskfee-utilscontainer-rootarticle');
          cEditArticles.setAttribute('title', objLabels.article_edit_top);
          cEditArticles.onclick = function () {
            dispatchEvent({
              action: ACTION_ELEMENT_SHOW,
              targetType: TARGETTYPE_ARTICLE,
              targetDo: TARGETTYPE_ARTICLE,
              targetPageId: obj.pageid
            });
          };
        } else if (obj.type === TARGETTYPE_ARTICLE) {
          if (obj.canEdit === true) {
            const parentEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-articles');
            parentEdit.setAttribute('title', objLabels.article_all);
            parentEdit.onclick = function () {
              dispatchEvent({
                action: ACTION_ELEMENT_EDIT,
                targetType: TARGETTYPE_ARTICLE,
                targetDo: obj.do,
                id: obj.id,
                targetPageId: obj.pageid
              });
            };
            const articleEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
            articleEdit.setAttribute('title', objLabels.edit_article);
            articleEdit.onclick = function () {
              dispatchEvent({
                action: ACTION_PARENT_EDIT,
                targetType: TARGETTYPE_ARTICLE,
                targetDo: obj.do,
                id: obj.id,
                pid: obj.pid,
                targetPageId: obj.pageid
              });
            };
            if (obj.canPublish === true) {
              const elementVisibility = createContainerElement(c, (obj.invisible === true ? 'alpdeskfee-utilscontainer-invisible' : 'alpdeskfee-utilscontainer-visible'));
              elementVisibility.setAttribute('title', objLabels.article_visible);
              elementVisibility.onclick = function () {
                dispatchEvent({
                  action: ACTION_ELEMENT_VISIBILITY,
                  targetType: TARGETTYPE_ARTICLE,
                  id: obj.id,
                  state: (obj.invisible === true ? 1 : 0)
                });
              };
            }
            if (obj.canDelete === true) {
              const elementDelete = createContainerElement(c, 'alpdeskfee-utilscontainer-delete');
              elementDelete.setAttribute('title', objLabels.delete_article);
              elementDelete.onclick = function () {
                if (confirm(objLabels.delete_confirm_article)) {
                  dispatchEvent({
                    action: ACTION_ELEMENT_DELETE,
                    targetType: TARGETTYPE_ARTICLE,
                    id: obj.id
                  });
                }
              };
            }
            const elementNew = createContainerElement(c, 'alpdeskfee-utilscontainer-new');
            elementNew.setAttribute('title', objLabels.new_element_top);
            elementNew.onclick = function () {
              dispatchEvent({
                action: ACTION_ELEMENT_NEW,
                targetType: TARGETTYPE_ARTICLE,
                targetDo: obj.do,
                id: obj.id
              });
            };
          }
        } else if (obj.type === TARGETTYPE_CE) {
          if (obj.do !== null && obj.do !== '') {
            if (obj.canEdit === true) {
              const parentEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-pedit');
              parentEdit.setAttribute('title', objLabels.element_all);
              parentEdit.onclick = function () {
                dispatchEvent({
                  action: ACTION_PARENT_EDIT,
                  targetType: TARGETTYPE_CE,
                  targetDo: obj.do,
                  id: obj.id,
                  pid: obj.pid
                });
              };
              const elementEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
              elementEdit.setAttribute('title', objLabels.edit_element);
              elementEdit.onclick = function () {
                dispatchEvent({
                  action: ACTION_ELEMENT_EDIT,
                  targetType: TARGETTYPE_CE,
                  targetDo: obj.do,
                  id: obj.id
                });
              };
              if (obj.canPublish === true) {
                const elementVisibility = createContainerElement(c, (obj.invisible === true ? 'alpdeskfee-utilscontainer-invisible' : 'alpdeskfee-utilscontainer-visible'));
                elementVisibility.setAttribute('title', objLabels.element_visible);
                elementVisibility.onclick = function () {
                  dispatchEvent({
                    action: ACTION_ELEMENT_VISIBILITY,
                    targetType: TARGETTYPE_CE,
                    targetDo: obj.do,
                    id: obj.id,
                    pid: obj.pid,
                    state: (obj.invisible === true ? 1 : 0)
                  });
                };
              }
              const elementDelete = createContainerElement(c, 'alpdeskfee-utilscontainer-delete');
              elementDelete.setAttribute('title', objLabels.delete_element);
              elementDelete.onclick = function () {
                if (confirm(objLabels.delete_confirm_element)) {
                  dispatchEvent({
                    action: ACTION_ELEMENT_DELETE,
                    targetType: TARGETTYPE_CE,
                    targetDo: obj.do,
                    id: obj.id
                  });
                }
              };
              const elementNew = createContainerElement(c, 'alpdeskfee-utilscontainer-new');
              elementNew.setAttribute('title', objLabels.new_element);
              elementNew.onclick = function () {
                dispatchEvent({
                  action: ACTION_ELEMENT_NEW,
                  targetType: TARGETTYPE_CE,
                  targetDo: obj.do,
                  id: obj.id,
                  pid: obj.pid
                });
              };
            }
          }
          if (obj.act !== null && obj.act !== '') {
            const modEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-module');
            modEdit.setAttribute('title', objLabels.element_mod);
            modEdit.onclick = function () {
              dispatchEvent({
                targetType: TARGETTYPE_MOD,
                targetDo: obj.act
              });
            };
          }
        } else if (obj.type === TARGETTYPE_MOD) {
          const parentEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-module');
          parentEdit.setAttribute('title', objLabels.element_mod);
          parentEdit.onclick = function () {
            dispatchEvent({
              targetType: TARGETTYPE_MOD,
              targetDo: obj.do
            });
          };
          if (obj.act !== null && obj.act !== undefined && obj.act !== '') {
            const modEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
            modEdit.onclick = function () {
              dispatchEvent({
                targetType: TARGETTYPE_MOD,
                targetDo: obj.act
              });
            };
          }
          if (obj.subviewitems.length > 0) {
            for (let si = 0; si < obj.subviewitems.length; si++) {
              const subMod = createContainerElement(c, 'alpdeskfee-utilscontainer-' + obj.subviewitems[si].icon);
              subMod.classList.add(obj.subviewitems[si].iconclass);
              subMod.style.backgroundImage = "url('../../../system/themes/flexible/icons/" + obj.subviewitems[si].icon + ".svg')";
              subMod.onclick = function () {
                dispatchEvent({
                  targetType: TARGETTYPE_MOD,
                  targetDo: obj.subviewitems[si].path
                });
              };
            }
          }
        }

        const cClear = document.createElement('div');
        cClear.classList.add('alpdeskfee-utilscontainer-clearcontainer');
        c.appendChild(cClear);
      }


    }

    function scanElements(objLabels) {
      let data = document.querySelectorAll("*[data-alpdeskfee]");
      for (let i = 0; i < data.length; i++) {
        let jsonData = data[i].getAttribute('data-alpdeskfee');
        if (jsonData !== null && jsonData !== undefined && jsonData !== '') {
          const obj = JSON.parse(jsonData);
          if (obj !== null && obj !== undefined) {
            if (obj.type === TARGETTYPE_ARTICLE) {
              let parentNode = data[i].parentElement;
              parentNode.classList.add('alpdeskfee-article-container');
              appendUtilsContainer(obj, data[i], false, objLabels);
              parentNode.onmouseover = function () {
                data[i].classList.add("alpdeskfee-parent-active");
              };
              parentNode.onmouseout = function () {
                data[i].classList.remove("alpdeskfee-parent-active");
              };
            } else {
              appendUtilsContainer(obj, data[i], true, objLabels);
              data[i].onmouseover = function () {
                data[i].classList.add("alpdeskfee-active");
              };
              data[i].onmouseout = function () {
                data[i].classList.remove("alpdeskfee-active");
              };
            }
          }
        }
      }
    }

    function checkInIframe() {
      return (window.location !== window.parent.location);
    }

    // Maybe problem at MultiDomain-Webpage
    if (checkInIframe() === true) {

      // Get from global
      let objLabels = null;
      if (alpdeskfeeLabels !== null && alpdeskfeeLabels !== undefined && alpdeskfeeLabels !== '') {
        objLabels = JSON.parse(alpdeskfeeLabels);
      }

      scanElements(objLabels);

      // Get from global
      let showPageEdit = false;
      if (alpdeskfeeCanPageEdit !== undefined && alpdeskfeeCanPageEdit !== null && alpdeskfeeCanPageEdit === 1) {
        showPageEdit = true;
      }

      if (showPageEdit === true && alpdeskfeePageid !== null && alpdeskfeePageid !== '' && alpdeskfeePageid !== undefined && alpdeskfeePageid !== 0) {
        const bodyElement = document.body;
        if (bodyElement !== null && bodyElement !== undefined) {
          const jsonData = '{"type":"' + TARGETTYPE_PAGE + '","desc":"Page","do":"' + TARGETTYPE_PAGE + '","id":"' + alpdeskfeePageid + '","pageid":"' + alpdeskfeePageid + '"}';
          const obj = JSON.parse(jsonData);
          if (obj !== null && obj !== undefined) {
            appendUtilsContainer(obj, bodyElement, true, objLabels);
          }
        }
      }

    }

  }, false);
})(window, document);