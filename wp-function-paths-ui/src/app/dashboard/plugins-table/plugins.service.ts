import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import {WordPressPlugin} from '../../models/plugin';

@Injectable({
  providedIn: 'root',
})
export class PluginsService {

  private PLUGIN_BASE_URL = 'http://localhost/wp-function-paths/plugin/';
  private SCANNER_BASE_URL = 'http://localhost/wp-function-paths/scanner/';

  constructor(private http: HttpClient) { }

  public getAllPlugins(): Observable<WordPressPlugin[]> {
    return this.http.get<WordPressPlugin[]>(this.PLUGIN_BASE_URL);
  }

  public downloadPlugin(slug: string, version: string): Observable<WordPressPlugin> {
    return this.http.post<WordPressPlugin>(this.PLUGIN_BASE_URL + 'download', {plugin: slug, version});
  }

  public scanPlugin(slug: string, version: string): Observable<WordPressPlugin> {
    return this.http.post<WordPressPlugin>(this.SCANNER_BASE_URL, {plugin: slug, version});
  }

  public delete(uuid: string): Observable<WordPressPlugin> {
    return this.http.delete<WordPressPlugin>(this.PLUGIN_BASE_URL + uuid);
  }

  public uploadFile(file: File): Observable<WordPressPlugin> {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('pluginName', file.name.replace('.zip', ''));
    return this.http.post<WordPressPlugin>(this.PLUGIN_BASE_URL + 'upload', formData);
  }
}
