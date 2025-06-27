import { Component } from '@angular/core';
import {RouterLink} from '@angular/router';
import {NgForOf, NgIf} from '@angular/common';

@Component({
  selector: 'app-settings',
  imports: [
    RouterLink,
    NgIf,
    NgForOf
  ],
  templateUrl: './settings.component.html',
  styles: ``
})
export class SettingsComponent {
  documentUploaded = false;
  documentName = ''; // z.B. Dateiname für Anzeige

  documentFilesUploaded = false;
  documentNames: string[] = [];

  onFileSelected(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('cv', file);

    fetch('http://localhost:3001/uploadCV', {
      method: 'POST',
      body: formData
    })
      .then(res => res.text())
      .then(data => {
        console.log('Upload erfolgreich:', data);
        this.documentUploaded = true;
        this.documentName = file.name;
      })
      .catch(err => {
        console.error('Fehler beim Upload:', err);
      });
  }


  deleteDocument(){
    this.documentUploaded = false;
    this.documentName = '';
  }

  onOtherFilesSelected(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('document', file);

    fetch('http://localhost:3001/uploadFiles', {
      method: 'POST',
      body: formData
    })
      .then(res => res.text())
      .then(data => {
        console.log('Upload erfolgreich:', data);
        this.documentFilesUploaded = true;
        this.documentNames.push(file.name);
      })
      .catch(err => {
        console.error('Fehler beim Upload:', err);
      });
  }

  deleteDocumentFile(name: string){
    this.documentNames = this.documentNames.filter(n => n !== name);
  }

}
