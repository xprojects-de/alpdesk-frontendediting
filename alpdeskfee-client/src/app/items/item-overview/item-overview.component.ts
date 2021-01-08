import { ThisReceiver } from '@angular/compiler';
import { Component, Input } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-overview',
  templateUrl: './item-overview.component.html',
  styleUrls: ['./item-overview.component.scss']
})
export class ItemOverviewComponent extends BaseItemComponent{

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';
  @Input() pid: string = '';

  @Input() pageEdit: boolean = false;
  @Input() pageId: number = 0;

  click() {
    this.dispatchEvent({
      dialog: true,
      action: this.action,
      targetType: this.targetType,
      do: this.do,
      id: this.id,
      pid: this.pid,
      pageEdit: this.pageEdit,
      pageId: this.pageId
    });
  }

}
