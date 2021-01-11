import { BrowserModule } from '@angular/platform-browser';
import { Injector, NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ItemContainerComponent } from './item-container/item-container.component';
import { ItemDescComponent } from './items/item-desc/item-desc.component';
import { ItemOverviewComponent } from './items/item-overview/item-overview.component';
import { ItemEditComponent } from './items/item-edit/item-edit.component';
import { ItemCopyComponent } from './items/item-copy/item-copy.component';
import { ItemPublishComponent } from './items/item-publish/item-publish.component';
import { ItemDeleteComponent } from './items/item-delete/item-delete.component';
import { ItemNewComponent } from './items/item-new/item-new.component';
import { BaseItemComponent } from './items/base-item/base-item.component';
import { ItemParentComponent } from './items/item-parent/item-parent.component';
import { ItemPageComponent } from './items/item-page/item-page.component';
import { ItemArticleComponent } from './items/item-article/item-article.component';
import { ItemCustomModuleComponent } from './items/item-custom-module/item-custom-module.component';
import { ItemMoveComponent } from './items/item-move/item-move.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { ModalIframeComponent } from './utils/modal-iframe/modal-iframe.component';
import { createCustomElement } from '@angular/elements';
import { HttpClientModule } from '@angular/common/http';
import { ItemBarDirective } from './directives/item-bar.directive';
import { DragDropModule } from '@angular/cdk/drag-drop';
import { MatIconModule } from '@angular/material/icon';

@NgModule({
  declarations: [
    AppComponent,
    ItemContainerComponent,
    ItemDescComponent,
    ItemOverviewComponent,
    ItemEditComponent,
    ItemCopyComponent,
    ItemPublishComponent,
    ItemDeleteComponent,
    ItemNewComponent,
    BaseItemComponent,
    ItemParentComponent,
    ItemPageComponent,
    ItemArticleComponent,
    ItemCustomModuleComponent,
    ItemMoveComponent,
    ModalIframeComponent,
    ItemBarDirective
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    AppRoutingModule,
    BrowserAnimationsModule,
    MatDialogModule,
    MatButtonModule,
    DragDropModule,
    MatIconModule
  ],
  //entryComponents: [AppComponent],
  providers: [],
  // Bootstrapping is done int the ngDoBootstrap-Method
  bootstrap: []
  // To develop locally enable this line!
  // To Deploy disable this line
  //bootstrap: [AppComponent]
})
export class AppModule {
  constructor(private injector: Injector) {

  }
  ngDoBootstrap() {
    const alpdeskfeecustom = createCustomElement(AppComponent, { injector: this.injector });
    customElements.define('app-alpdeskfee', alpdeskfeecustom);
  }
}
