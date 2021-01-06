import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

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
    BaseItemComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
