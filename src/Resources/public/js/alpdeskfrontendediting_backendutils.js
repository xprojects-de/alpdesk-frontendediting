(function (window, document) {

    function ready(callback) {
        if (document.readyState !== 'loading') {
            callback();
        } else if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', callback);
        }
    }

    ready(function () {

        function getUrlParams(k) {
            let p = {};
            location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (s, k, v) {
                p[k] = v;
            });
            return k ? p[k] : p;
        }

        let displayHeader = true;
        let hideHeader = getUrlParams('alpdesk_hideheader');
        if (hideHeader !== null && hideHeader !== undefined && hideHeader === '1') {
            displayHeader = false;
        }
        if (displayHeader === true) {
            let hideHeaderElement = document.querySelectorAll('.tl_header');
            for (let i = 0; i < hideHeaderElement.length; i++) {
                hideHeaderElement[i].style.display = 'block';
            }
        }

        let focusElementId = getUrlParams('alpdeskfocus_listitem');
        if (focusElementId !== null && focusElementId !== undefined) {
            let e = document.getElementById('li_' + focusElementId);
            if (e !== null && e !== undefined) {
                e.classList.add('listing_focus');
                e.style.display = 'block';
            }
        }

    }, false);
})(window, document);