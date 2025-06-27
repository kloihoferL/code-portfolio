import {Component, Input, OnInit} from '@angular/core';
import {ActivatedRoute, RouterLink} from "@angular/router";
import {HttpClient} from '@angular/common/http';
import {NgClass} from '@angular/common';

@Component({
  selector: 'app-data-detail',
  imports: [
    RouterLink,
    NgClass
  ],
  templateUrl: './tipps-detail.component.html',
  styleUrl: './tipps-detail.component.css'
})
export class TippsDetailComponent implements OnInit{
  status:string = '';
  tipps: any[] = [];
  opened: boolean[] = [];

  constructor(private route: ActivatedRoute,
              private http: HttpClient) {}


  ngOnInit(): void {
    this.status =  this.route.snapshot.params['title'];

    this.tipps = [
      {
        "titel": "Vor dem Gespräch",
        "einleitung": "Hier erfährst du alles über die Vorbereitung auf dein Bewerbungsgespräch.",
        "beschreibung": [
          "Informiere dich gründlich über das Unternehmen.",
          "Bereite Antworten auf typische Fragen vor.",
          "Drucke alle wichtigen Unterlagen aus."
        ]
      },
      {
        "titel": "Vor dem Gespräch",
        "einleitung": "Hier erfährst du alles über die Vorbereitung auf dein Bewerbungsgespräch.",
        "beschreibung": [
          "Informiere dich gründlich über das Unternehmen.",
          "Bereite Antworten auf typische Fragen vor.",
          "Drucke alle wichtigen Unterlagen aus."
        ]
      },
      {
        "titel": "Vor dem Gespräch",
        "einleitung": "Hier erfährst du alles über die Vorbereitung auf dein Bewerbungsgespräch.",
        "beschreibung": [
          "Informiere dich gründlich über das Unternehmen.",
          "Bereite Antworten auf typische Fragen vor.",
          "Drucke alle wichtigen Unterlagen aus."
        ]
      }
    ];
  }

  toggle(index: number): void {
    this.opened[index] = !this.opened[index];
  }

}
