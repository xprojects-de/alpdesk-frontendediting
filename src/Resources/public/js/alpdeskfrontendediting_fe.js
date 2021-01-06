
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
    const ACTION_ELEMENT_COPY = 'element_copy';

    function draggableElement(el) {

      let initialX;
      let initialY;

      let contentElementContainer = el.parentElement.parentElement;

      let barContainer = null;
      let currentElement = null;

      // This element is unique and there is no check needed
      el.onmousedown = function (e) {
        e = e || window.event;
        initialX = e.clientX;
        initialY = e.clientY;
        if (e.target === this) {
          currentElement = this;
          barContainer = this.parentElement;
        }
      };

      // Check if contentElementContainer still has a listener 
      if (contentElementContainer.getAttribute('data-movelistener') !== 'true') {

        contentElementContainer.onmouseup = function () {
          currentElement = null;
          barContainer = null;
        };

        contentElementContainer.onmousemove = function (e) {
          if (currentElement !== null && barContainer !== null) {
            e = e || window.event;
            e.preventDefault();
            let currentX = initialX - e.clientX;
            let currentY = initialY - e.clientY;
            initialX = e.clientX;
            initialY = e.clientY;

            let limit_bottom = contentElementContainer.offsetHeight - currentY - barContainer.offsetHeight;
            let top = (barContainer.offsetTop - currentY);
            if (barContainer.offsetTop >= limit_bottom) {
              top = limit_bottom;
            } else if (top <= 0) {
              top = 0;
            }

            let limit_left = contentElementContainer.offsetWidth - currentX - barContainer.offsetWidth;
            let left = (barContainer.offsetLeft - currentX);
            if (barContainer.offsetLeft >= limit_left) {
              left = limit_left;
            }
            // Bar is left not in the middle
            if (barContainer.classList.contains('alpdeskfee-utilscontainer-custommodule')) {
              if (left <= 0) {
                left = 0;
              }
            } else {
              if (left <= (barContainer.offsetWidth / 2)) {
                left = (barContainer.offsetWidth / 2);
              }
            }

            barContainer.style.top = top + "px";
            barContainer.style.left = left + "px";
          }
        };
        contentElementContainer.setAttribute('data-movelistener', 'true');
      }
    }

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

    function appendUtilsContainer(obj, parent, notUseParent, objLabels, moveenabled) {

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

        if (moveenabled === true) {
          const cMove = document.createElement('div');
          cMove.classList.add('alpdeskfee-utilscontainer-move');
          c.appendChild(cMove);
          draggableElement(cMove);
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
            const parentEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-edit');
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
            const articleEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-pedit');
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
              const parentEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-articles');
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
              const elementCopy = createContainerElement(c, 'alpdeskfee-utilscontainer-copy');
              elementCopy.setAttribute('title', objLabels.copy_element);
              elementCopy.onclick = function () {
                dispatchEvent({
                  action: ACTION_ELEMENT_COPY,
                  targetType: TARGETTYPE_CE,
                  targetDo: obj.do,
                  id: obj.id,
                  pid: obj.pid
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
            // if Element has a special Module-Item, display left because otherwise first ce_element will be under this item
            c.classList.add('alpdeskfee-utilscontainer-custommodule');
            const modEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-module');
            if (obj.iconclass !== null && obj.iconclass !== undefined && obj.iconclass !== '') {
              modEdit.classList.add(obj.iconclass);
            }
            if (obj.icon !== null && obj.icon !== undefined && obj.icon !== '') {
              modEdit.style.backgroundImage = "url('" + obj.icon + "')";
            }
            modEdit.setAttribute('title', objLabels.element_mod);
            modEdit.onclick = function () {
              dispatchEvent({
                targetType: TARGETTYPE_MOD,
                targetDo: obj.act
              });
            };
          }
        } else if (obj.type === TARGETTYPE_MOD) {
          c.classList.add('alpdeskfee-utilscontainer-custommodule');
          const parentEdit = createContainerElement(c, 'alpdeskfee-utilscontainer-module');
          if (obj.iconclass !== null && obj.iconclass !== undefined && obj.iconclass !== '') {
            parentEdit.classList.add(obj.iconclass);
          }
          if (obj.icon !== null && obj.icon !== undefined && obj.icon !== '') {
            parentEdit.style.backgroundImage = "url('" + obj.icon + "')";
          }
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
              const subMod = createContainerElement(c, 'alpdeskfee-utilscontainer-module');
              if (obj.subviewitems[si].iconclass !== null && obj.subviewitems[si].iconclass !== undefined && obj.subviewitems[si].iconclass !== '') {
                subMod.classList.add(obj.subviewitems[si].iconclass);
              }
              if (obj.subviewitems[si].icon !== null && obj.subviewitems[si].icon !== undefined && obj.subviewitems[si].icon !== '') {
                subMod.style.backgroundImage = "url('" + obj.subviewitems[si].icon + "')";
              }
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

    function setContextMenu(element, classname, selector) {
      element.oncontextmenu = function (e) {
        e.preventDefault();
        let data = document.querySelectorAll(selector);
        for (let k = 0; k < data.length; k++) {
          if (data[k] !== this) {
            data[k].classList.remove(classname);
          }
        }
        if (this.classList.contains(classname)) {
          this.classList.remove(classname);
        } else {
          this.classList.add(classname);
        }
      };
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
              appendUtilsContainer(obj, data[i], false, objLabels, true);
              parentNode.onmouseover = function () {
                data[i].classList.add("alpdeskfee-parent-active");
              };
              parentNode.onmouseout = function () {
                data[i].classList.remove("alpdeskfee-parent-active");
              };
            } else {
              data[i].classList.add('alpdeskfee-ce-container');
              appendUtilsContainer(obj, data[i], true, objLabels, true);
              data[i].onmouseover = function () {
                data[i].classList.add('alpdeskfee-active');
              };
              data[i].onmouseout = function () {
                data[i].classList.remove('alpdeskfee-active');
              };
              setContextMenu(data[i], 'alpdeskfee-active-force', '*[data-alpdeskfee]');
            }
          }
        }
      }
    }

    function checkInIframe() {
      return (window.location !== window.parent.location);
    }

    let objLabels = null;
    if (alpdeskfeeLabels !== null && alpdeskfeeLabels !== undefined && alpdeskfeeLabels !== '') {
      objLabels = JSON.parse(alpdeskfeeLabels);
      dispatchEvent({
        action: 'init',
        labels: objLabels
      });
    }

    // Maybe problem at MultiDomain-Webpage
    // Otherwise in future the complete Code can be in Backendjs and the access iframecontent from parent backend directly!
    /*if (checkInIframe() === true) {
     
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
     const jsonData = '{"type":"' + TARGETTYPE_PAGE + '","do":"' + TARGETTYPE_PAGE + '","id":"' + alpdeskfeePageid + '","pageid":"' + alpdeskfeePageid + '"}';
     const obj = JSON.parse(jsonData);
     if (obj !== null && obj !== undefined) {
     appendUtilsContainer(obj, bodyElement, true, objLabels, false);
     }
     }
     }
     
     }*/

  }, false);
})(window, document);