import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';

export interface DialogData {
  url: string;
}

@Component({
  selector: 'app-modal-iframe',
  templateUrl: './modal-iframe.component.html',
  styleUrls: ['./modal-iframe.component.scss']
})
export class ModalIframeComponent implements OnInit{

  url: any;
  height = 500;

  constructor(private _sanitizer: DomSanitizer, public dialogRef: MatDialogRef<ModalIframeComponent>, @Inject(MAT_DIALOG_DATA) public dataRef: DialogData) {
    //this.height = (window.innerHeight - 10);
  }

  ngOnInit() {
    this.url = this._sanitizer.bypassSecurityTrustResourceUrl(this.dataRef.url);
  }

}
