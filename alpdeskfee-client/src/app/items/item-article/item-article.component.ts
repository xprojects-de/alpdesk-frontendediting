import { Component, Input } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-article',
  templateUrl: './item-article.component.html',
  styleUrls: ['./item-article.component.scss']
})
export class ItemArticleComponent extends BaseItemComponent {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';

  @Input() pageEdit: boolean = false;
  @Input() pageId: number = 0;

  click() {
    this.dispatchEvent({
      dialog: true,
      action: this.action,
      targetType: this.targetType,
      do: this.do,
      pageEdit: this.pageEdit,
      pageId: this.pageId
    });
  }

}
