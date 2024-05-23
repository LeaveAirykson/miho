import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [
    CommonModule,
    RouterOutlet,
    RouterLink,
    RouterLinkActive
  ],
  templateUrl: './sidebar.component.html',
  styleUrl: './sidebar.component.css'
})
export class SidebarComponent {

  parents: HTMLElement[] = []

  items = [
    {
      link: '/',
      icon: 'home',
      label: 'Dashboard'
    },
    {
      link: '/document',
      icon: 'doc',
      label: 'Dokumente'
    },
    {
      link: '/media',
      icon: 'picture',
      label: 'Media'
    },
    {
      link: '/data',
      icon: 'database',
      label: 'Datensätze',
      items: [
        {
          link: '/data/article',
          label: 'Artikel',
        },
        {
          link: '/data/gerichte',
          label: 'Gerichte',
        }
      ]
    },
    {
      link: '/user',
      icon: 'user',
      label: 'Benutzer'
    },
    {
      link: '/settings',
      icon: 'cog',
      label: 'Einstellungen'
    },
  ]

  toggle(event: Event) {
    const element = event.currentTarget as HTMLElement;

    if (!this.parents.find(e => e === element)) {
      this.parents.push(element);
    }

    element.classList.toggle('open');
    console.log(this.parents);
  }

  resetParents(reset = false) {
    if (!reset || !this.parents.length) {
      return;
    }

    this.parents.forEach((p) => p.classList.remove('open'));
    this.parents = [];
  }
}
