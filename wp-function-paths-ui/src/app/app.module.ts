import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { SharedModule } from "./shared/shared.module";
import { DashboardComponent } from "./dashboard/dashboard.component";
import { PluginsTableComponent } from "./dashboard/plugins-table/plugins-table.component";
import { HttpClientModule } from "@angular/common/http";
import { ToastrModule } from "ngx-toastr";
import {ModalDialogComponent} from "./dashboard/plugins-table/modal/modal-dialog.component";

@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    PluginsTableComponent,
    ModalDialogComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    BrowserAnimationsModule,
    SharedModule,
    HttpClientModule,
    ToastrModule.forRoot()
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
