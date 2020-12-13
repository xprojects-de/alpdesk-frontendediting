
(function (window, document) {
  document.addEventListener('DOMContentLoaded', function () {

    const ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

    var dispatchAlpdeskEvent = function (type, id) {
      window.parent.document.dispatchEvent(new CustomEvent(ALPDESK_EVENTNAME, {
        detail: {
          type: type,
          id: id
        }
      }));
    };

    var scanAlpdeskElements = function () {
      let data = document.querySelectorAll("div[data-alpdeskfee-id]");
      for (let i = 0; i < data.length; i++) {
        data[i].onclick = function (e) {
          let targetId = data[i].getAttribute("data-alpdeskfee-id");
          if (targetId !== null) {
            dispatchAlpdeskEvent('content', targetId);
          }
        };
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