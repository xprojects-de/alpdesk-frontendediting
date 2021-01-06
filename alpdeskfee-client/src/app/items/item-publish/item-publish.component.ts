import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-item-publish',
  templateUrl: './item-publish.component.html',
  styleUrls: ['./item-publish.component.scss']
})
export class ItemPublishComponent implements OnInit {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';
  @Input() pid: string = '';
  @Input() state: boolean = true;

  constructor() { }

  ngOnInit(): void {
  }

}
