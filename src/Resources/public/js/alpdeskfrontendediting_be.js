(function (window, document) {

    function ready(callback) {
        if (document.readyState !== 'loading') {
            callback();
        } else if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', callback);
        }
    }

    ready(function () {

        document.getElementById('pageselect').onclick = function () {
            Backend.openModalSelector({
                'id': 'tl_listing',
                'title': 'Frontend-Editing',
                'url': Contao.routes.backend_picker + '?context=page' + '&amp;extras[fieldType]=radio&amp;value=&amp;popup=1',
                'callback': function (table, value) {
                    if (value !== null && value !== undefined) {
                        if (value.length > 0) {
                            window.location.href = window.location.href + "?pageselect=" + value[0];
                        }
                    }
                }
            });
        };

    }, false);
})(window, document);
