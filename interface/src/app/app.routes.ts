import { Routes } from '@angular/router';
import { DocumentComponent } from './document/document.component';
import { MediaComponent } from './media/media.component';
import { DatasetComponent } from './dataset/dataset.component';

export const routes: Routes = [
  {
    path: 'document',
    component: DocumentComponent
  },
  {
    path: 'media',
    component: MediaComponent
  },
  {
    path: 'data',
    component: DatasetComponent
  },
  {
    path: 'data/article',
    component: DatasetComponent
  }
];
