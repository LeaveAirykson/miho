- Static Site Generator/CMS ohne Datenbank
- Backend PHP
- CMS Frontend Angular
- Seiten können mittels Blöcke zusammengestellt werden
- Daten werden in json Dateien abgelegt
- Build Prozess über PHP
- Apache support (.htaccess)
- Kann lokal genutzt werden und der Build Ordner kann hochgeladen werden
- Oder direkt am Server laufen und build Ordner dient als Document Root
- Templating über php oder Template Engine?
- Daten Typen:
  - Pages
  - Objects
  - Assets

Colors:

```
/* CSS HEX */
--delft-blue: #2b3a67ff;
--paynes-gray: #496a81ff;
--sunglow: #fdca40ff;
--ghost-white: #f4f4f9ff;
```

- Dokumente verwenden Controller
- Controller können unterschiedliche Logik und Templates verwenden
- Templates sind unter template/[controller]/[]

Ignore für deploy:

- /interface
- /public/\*.html
- /public/.htaccess
- /public/assets
- /storage
- /log

shared between release:

- /storage
- /log
