import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ItemContainerComponent } from './item-container/item-container.component';
import { ItemDescComponent } from './items/item-desc/item-desc.component';
import { ItemOverviewComponent } from './items/item-overview/item-overview.component';

@NgModule({
  declarations: [
    AppComponent,
    ItemContainerComponent,
    ItemDescComponent,
    ItemOverviewComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
