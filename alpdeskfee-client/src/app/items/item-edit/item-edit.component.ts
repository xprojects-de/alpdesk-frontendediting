import { Component, Input } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-edit',
  templateUrl: './item-edit.component.html',
  styleUrls: ['./item-edit.component.scss']
})
export class ItemEditComponent extends BaseItemComponent {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';

  @Input() pageEdit: boolean = false;
  @Input() pageId: number = 0;

  click() {
    this.dispatchEvent({
      dialog: true,
      action: this.action,
      targetType: this.targetType,
      do: this.do,
      id: this.id,
      pageEdit: this.pageEdit,
      pageId: this.pageId
    });
  }

}
