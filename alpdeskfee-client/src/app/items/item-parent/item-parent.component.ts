import { Component, Input, OnInit } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-parent',
  templateUrl: './item-parent.component.html',
  styleUrls: ['./item-parent.component.scss']
})
export class ItemParentComponent extends BaseItemComponent {

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
