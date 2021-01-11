import { Component, Input } from '@angular/core';
import { Constants } from 'src/app/classes/constants';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-delete',
  templateUrl: './item-delete.component.html',
  styleUrls: ['./item-delete.component.scss']
})
export class ItemDeleteComponent extends BaseItemComponent {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';

  @Input() pageEdit: boolean = false;
  @Input() pageId: number = 0;

  @Input() base: string = '';
  @Input() rt: string = '';

  private generteRequestUrl(): string {
    let url: string = '';
    if (this.targetType === Constants.TARGETTYPE_CE) {
      url = '/contao?do=' + this.do + '&table=tl_content&act=delete&id=' + this.id + '&rt=' + this.rt;
    } else if (this.targetType === Constants.TARGETTYPE_ARTICLE) {
      url = '/contao?do=' + this.targetType + '&act=delete' + '&id=' + this.id + '&rt=' + this.rt;
    }
    return url;
  }

  click() {
    if (confirm("Really delete?")) {
      const url: string = this.generteRequestUrl();
      this.dispatchEvent({
        reloadFrame: true,
        preRequestGet: true,
        url: url,
        dialog: false,
        action: this.action,
        targetType: this.targetType,
        do: this.do,
        id: this.id,
        pageEdit: this.pageEdit,
        pageId: this.pageId
      });
    }
  }

}
