import { Component, Input, OnChanges, OnInit } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-custom-module',
  templateUrl: './item-custom-module.component.html',
  styleUrls: ['./item-custom-module.component.scss']
})
export class ItemCustomModuleComponent extends BaseItemComponent implements OnInit, OnChanges {

  @Input() title: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() iconclass: string = '';
  @Input() icon: string = '../../../system/themes/flexible/icons/modules.svg';

  @Input() pageEdit: boolean = false;
  @Input() pageId: number = 0;

  iconDefault: string = '../../../system/themes/flexible/icons/modules.svg';
  iconUrl: string = '';

  ngOnInit() {
    this.setIcon();
  }

  ngOnChanges() {
    this.setIcon();
  }

  private setIcon() {
    if (this.icon !== null && this.icon !== '' && this.icon !== undefined) {
      this.iconUrl = 'url(\'' + this.icon + '\')';
    } else {
      this.iconUrl = 'url(\'' + this.iconDefault + '\')';
    }
  }

  click() {
    this.dispatchEvent({
      dialog: true,
      targetType: this.targetType,
      do: this.do,
      iconclass: this.iconclass,
      icon: this.icon,
      pageEdit: this.pageEdit,
      pageId: this.pageId
    });
  }

}