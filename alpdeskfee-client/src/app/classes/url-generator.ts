export class UrlGenerator {

    TARGETTYPE_PAGE = 'page';
    TARGETTYPE_ARTICLE = 'article';
    TARGETTYPE_CE = 'ce';
    TARGETTYPE_MOD = 'mod';

    ACTION_PARENT_EDIT = 'parent_edit';
    ACTION_ELEMENT_EDIT = 'element_edit';
    ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    ACTION_ELEMENT_DELETE = 'element_delete';
    ACTION_ELEMENT_SHOW = 'element_show';
    ACTION_ELEMENT_NEW = 'element_new';
    ACTION_ELEMENT_COPY = 'element_copy';

    public generateUrl(data: any, base: string, rt: string): string {        

        let url: string = '';

        if (data.targetType === this.TARGETTYPE_PAGE) {
            if (data.action === this.ACTION_ELEMENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&act=edit&rt=' + rt + '&id=' + data.id;
            } else if (data.action === this.ACTION_ELEMENT_SHOW) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&pn=' + data.targetPageId + '&rt=' + rt;
            }
        } else if (data.targetType === this.TARGETTYPE_ARTICLE) {
            if (data.action === this.ACTION_PARENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&act=edit&rt=' + rt + '&id=' + data.id;
            } else if (data.action === this.ACTION_ELEMENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&table=tl_content&rt=' + rt + '&id=' + data.id
            } else if (data.action === this.ACTION_ELEMENT_SHOW) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&pn=' + data.targetPageId + '&rt=' + rt;
            } else if (data.action === this.ACTION_ELEMENT_NEW) {
                //AlpdeskBackend.newElement(data, true);
            } else if (data.action === this.ACTION_ELEMENT_VISIBILITY) {
                //AlpdeskBackend.callVisibilityArticle(data);
            } else if (data.action === this.ACTION_ELEMENT_DELETE) {
                //AlpdeskBackend.callDeleteArticle(data);
            }
        } else if (data.targetType === this.TARGETTYPE_CE) {
            if (data.action === this.ACTION_PARENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&alpdesk_hideheader=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.do + '&table=tl_content&rt=' + rt + '&id=' + data.pid;
            } else if (data.action === this.ACTION_ELEMENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&table=tl_content&rt=' + rt + '&act=edit&id=' + data.id;
            } else if (data.action === this.ACTION_ELEMENT_COPY) {
                //AlpdeskBackend.copyElement(data);
            } else if (data.action === this.ACTION_ELEMENT_NEW) {
                //AlpdeskBackend.newElement(data, false);
            } else if (data.action === this.ACTION_ELEMENT_VISIBILITY) {
                //AlpdeskBackend.callVisibilityElement(data);
            } else if (data.action === this.ACTION_ELEMENT_DELETE) {
                //AlpdeskBackend.callDeleteElement(data);
            }
        } else if (data.targetType === this.TARGETTYPE_MOD) {
            url = '/contao?alpdeskmodal=1&popup=1&' + data.do + '&rt=' + rt;
        }

        return url;

    }

}
