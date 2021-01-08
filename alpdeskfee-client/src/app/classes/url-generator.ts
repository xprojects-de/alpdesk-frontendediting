export class UrlGenerator {

    public static TARGETTYPE_PAGE = 'page';
    public static TARGETTYPE_ARTICLE = 'article';
    public static TARGETTYPE_CE = 'ce';
    public static TARGETTYPE_MOD = 'mod';

    public static ACTION_PARENT_EDIT = 'parent_edit';
    public static ACTION_ELEMENT_EDIT = 'element_edit';
    public static ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    public static ACTION_ELEMENT_DELETE = 'element_delete';
    public static ACTION_ELEMENT_SHOW = 'element_show';
    public static ACTION_ELEMENT_NEW = 'element_new';
    public static ACTION_ELEMENT_COPY = 'element_copy';

    public generateUrl(data: any, base: string, rt: string): string {

        let url: string = '';

        if (data.targetType === UrlGenerator.TARGETTYPE_PAGE) {
            if (data.action === UrlGenerator.ACTION_ELEMENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&act=edit&rt=' + rt + '&id=' + data.id;
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_SHOW) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&pn=' + data.targetPageId + '&rt=' + rt;
            }
        } else if (data.targetType === UrlGenerator.TARGETTYPE_ARTICLE) {
            if (data.action === UrlGenerator.ACTION_PARENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&act=edit&rt=' + rt + '&id=' + data.id;
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&table=tl_content&rt=' + rt + '&id=' + data.id
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_SHOW) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&pn=' + data.targetPageId + '&rt=' + rt;
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_NEW) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&table=tl_content&id=' + data.id + '&act=create&mode=2&pid=' + data.id + '&rt=' + rt;
            }
        } else if (data.targetType === UrlGenerator.TARGETTYPE_CE) {
            if (data.action === UrlGenerator.ACTION_PARENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&alpdesk_hideheader=1&alpdeskfocus_listitem=' + data.id + '&do=' + data.do + '&table=tl_content&rt=' + rt + '&id=' + data.pid;
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_EDIT) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&table=tl_content&rt=' + rt + '&act=edit&id=' + data.id;
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_COPY) {
                url = '/contao?alpdeskmodal=1&popup=1&alpdeskfocus_listitem=' + data.id + '&alpdeskredirectcopy=1&do=' + data.do + '&table=tl_content&rt=' + rt + '&id=' + data.pid;
            } else if (data.action === UrlGenerator.ACTION_ELEMENT_NEW) {
                url = '/contao?alpdeskmodal=1&popup=1&do=' + data.do + '&table=tl_content&id=' + data.pid + '&act=create&mode=1&pid=' + data.id + '&rt=' + rt;
            }
        } else if (data.targetType === UrlGenerator.TARGETTYPE_MOD) {
            url = '/contao?alpdeskmodal=1&popup=1&' + data.do + '&rt=' + rt;
        }

        return url;

    }

}
