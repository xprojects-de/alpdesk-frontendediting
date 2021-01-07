import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

export interface DialogData {
  url: string;
}

@Component({
  selector: 'app-modal-iframe',
  templateUrl: './modal-iframe.component.html',
  styleUrls: ['./modal-iframe.component.scss']
})
export class ModalIframeComponent{

  constructor(public dialogRef: MatDialogRef<ModalIframeComponent>, @Inject(MAT_DIALOG_DATA) public dataRef: DialogData) {
  }

}
