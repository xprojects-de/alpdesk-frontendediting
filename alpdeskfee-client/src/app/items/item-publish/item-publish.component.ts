import { Component, Input } from '@angular/core';
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

  click() {
    this.dispatchEvent({
      action: this.action,
      targetType: this.targetType,
      do: this.do,
      id: this.id,
      pid: this.pid,
      state: this.state
      /*targetPageId: this.pageid*/
    });
  }

}
