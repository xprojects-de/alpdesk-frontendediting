import { Component, Input } from '@angular/core';
import { Constants } from 'src/app/classes/constants';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-publish',
  templateUrl: './item-publish.component.html',
  styleUrls: ['./item-publish.component.scss']
})
export class ItemPublishComponent extends BaseItemComponent {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';
  @Input() pid: string = '';
  @Input() state: boolean = true;

  @Input() pageEdit: boolean = false;
  @Input() pageId: number = 0;

  @Input() base: string = '';
  @Input() rt: string = '';

  private generteRequestUrl(): string {
    let url: string = '';
    if (this.targetType === Constants.TARGETTYPE_CE) {
      url = '/contao?do=' + this.do + '&table=tl_content&id=' + this.pid + '&cid=' + this.id + '&state=' + this.state + '&rt=' + this.rt;
    } else if (this.targetType === Constants.TARGETTYPE_ARTICLE) {
      url = '/contao?do=' + this.targetType + '&tid=' + this.id + '&state=' + this.state + '&rt=' + this.rt;
    }
    return url;
  }

  click() {
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
      pid: this.pid,
      state: this.state,
      pageEdit: this.pageEdit,
      pageId: this.pageId
    });
  }

}
