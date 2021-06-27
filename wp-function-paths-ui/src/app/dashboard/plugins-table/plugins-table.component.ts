import { Component, ViewChild } from '@angular/core';
import { WordPressPlugin } from '../../models/plugin';
import { PluginsService } from './plugins.service';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';
import { ToastrService } from 'ngx-toastr';
import { MatDialog } from '@angular/material/dialog';
import { ModalDialogComponent } from './modal/modal-dialog.component';

@Component({
  selector: 'app-plugins-table',
  templateUrl: './plugins-table.component.html',
  styleUrls: ['./plugins-table.component.scss']
})
export class PluginsTableComponent {
  loading: boolean;
  displayedColumns: string[] = ['slug', 'version', 'files', 'downloadStatus', 'scannedStatus', 'isFromSvn', 'action'];
  allPlugins: WordPressPlugin[];
  dataSource = new MatTableDataSource<WordPressPlugin>();

  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private pluginsService: PluginsService, private toastr: ToastrService,
              private dialog: MatDialog) {
    this.dataSource.filterPredicate = (data: WordPressPlugin, filter: string): boolean => {
      return data.pluginSlug.toLowerCase().includes(filter);
    };
    this.refreshTableFromBackend();
  }

  applyFilter($event: KeyboardEvent): void {
    const filterValue = ($event.target as HTMLInputElement).value;
    this.dataSource.filter = filterValue.trim().toLowerCase();
  }

  download(element: WordPressPlugin): void {
    element.blocked = true;
    this.pluginsService.downloadPlugin(element.pluginSlug, element.version)
      .subscribe((res: WordPressPlugin) => {
        if (res.isDownloaded) {
          this.toastr
            .success(`Plugin ${element.pluginSlug} was downloaded successfully`, 'Download success');
        } else {
          this.toastr.error(`Plugin ${element.pluginSlug} download went wrong`, 'Download error');
        }
        element.isDownloaded = res.isDownloaded;
        element.files = res.files;
        element.blocked = false;
        console.log(element);
      }, () => {
        this.toastr.error(`Plugin ${element.pluginSlug} download went wrong`, 'Download error');
        element.blocked = false;
      });
  }

  scan(element: WordPressPlugin): void {
    if (element.files > 200) {
      const dialogRef = this.dialog.open(ModalDialogComponent, {
        width: '300px',
        data: {
          message: 'This plugins has a big amount of files and scan could take several minutes. Do you want to continue?'
        }
      });
      dialogRef.afterClosed().subscribe((result: boolean) => {
        if (result) {
          this.scanPluginAction(element);
        }
      });
    } else {
      this.scanPluginAction(element);
    }

  }

  delete(element: WordPressPlugin): void {
    const dialogRef = this.dialog.open(ModalDialogComponent, {
      width: '300px',
      data: {
        message: 'Plugin files will be deleted for this plugin. For future scans you will need to download it again. Do you want to continue?'
      }
    });
    dialogRef.afterClosed().subscribe((result: boolean) => {
      if (result) {
        element.blocked = true;
        this.pluginsService.delete(element.uuid)
          .subscribe(() => {
            this.toastr
              .success(`Plugin ${element.pluginSlug} was successfully deleted`, 'Delete success');
            this.refreshTableFromBackend();
          }, () => {
            this.toastr.error(`Plugin ${element.pluginSlug} deletion went wrong`, 'Delete error');
            element.blocked = false;
          });
      }
    });
  }

  openNeo4j(): void {
    window.open('http://localhost:7474/browser/', '_blank');
  }

  applyColumnFilter(property: string, $event: any): void {
    if ($event.value === 'true') {
      this.dataSource.data = this.allPlugins.filter(plugin => plugin[property] === JSON.parse($event.value));
    } else if ($event.value === 'false') {
      this.dataSource.data = this.allPlugins.filter(plugin => !plugin[property]);
    } else {
      this.dataSource.data = this.allPlugins;
    }
  }

  private scanPluginAction(element: WordPressPlugin): void {
    element.blocked = true;
    this.pluginsService.scanPlugin(element.pluginSlug, element.version)
      .subscribe((res: WordPressPlugin) => {
        this.toastr
          .success(`Plugin ${element.pluginSlug} was scanned successfully`, 'Scan success');
        element.isScanned = res.isScanned;
        element.blocked = false;
      }, () => {
        this.toastr.error(`Plugin ${element.pluginSlug} scan went wrong`, 'Scan error');
        element.blocked = false;
      });
  }

  zipInputChange($event: any): void {
    this.loading = true;
    this.pluginsService.uploadFile($event.target.files[0]).subscribe((res: WordPressPlugin) => {
      this.toastr.success(`Plugin ${res.pluginSlug} was uploaded successfully`, 'Upload success');
      this.dataSource.data.push(res);
      this.loading = false;
    }, () => {
      this.loading = false;
      this.toastr.error(`Plugin ${$event.target.files[0].name} upload went wrong`, 'Upload error');
    });
  }

  private refreshTableFromBackend(): void {
    this.loading = true;
    this.dataSource.filter = '';
    this.pluginsService.getAllPlugins().subscribe((plugins: WordPressPlugin[]) => {
      this.allPlugins = plugins;
      this.dataSource.data = plugins;
      this.dataSource.paginator = this.paginator;
      this.loading = false;
    });
  }

}
