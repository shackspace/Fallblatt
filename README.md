# Fallblattanzeige

Ansteuern einer Funkwerk ITK Fallblattanzeige mit einem ESP32

## Projektziel
Die Fallblattanzeige in der Lounge deployen und versch. Möglichkeiten bieten,
diese mit Inhalten zu bespaßen.

## Anforderungen
- HTTP-API
- MQTT-API
- Rohdaten-API (TCP,UDP)
- Differenzielles Update (nur Zeichen ändern, die sich tatsächlich geändert haben)
- UTF-8-Input auf Fallblattanzeige übersetzen

## Deployment (physisch)
- Auf "Serverschrank" in der Lounge
- Angeleuchtet mit LED-Streifen

## Technik
Das Projekt verwendet das [ESP-IDF](https://github.com/espressif/esp-idf) als Framework
für den ESP32.

Dieser kommuniziert dann über einen UART mit den Modulen auf der Fallblattanzeige.

## API-Features

- UDP-API
  - Line Mode
    - Port: 8000
    - Paket enthält Textzeilen mit LF (0x0A) getrennt, diese werden wie in einem Terminal nach oben geschoben
  - Full Refresh
    - Port: 8001
    - Paket enthält exakt 4*20 Zeichen, es werden fehlende Zeichen mit Leerstellen aufgefüllt. Newlines gibt es nicht.
  - XY-Access
    - Port: 8002
    - Paket enthält `X;Y;…`
- TCP-API
  - Standard: Append Mode → alles was übertragen wird, wird an die Anzeige angehängt
  - DC1: Clear Screen and Home → Lösche Anzeige und bewege Cursor nach (0,0)
  - DC2: Home Cursor
  - DC3: Set Cursor → Lese `{DC2}x;y;` und setze den Cursor auf (x,y).
- MQTT-API
  - `fallblatt/line` → publish eines utf8-strings, welcher als zeile angehängt wird
  - `fallblatt/screen` → publish, welcher den ganzen Inhalt ersetzt
  - `fallblatt/pos/x/y` → publish, welcher an (x,y) geschrieben wird
- REST-API
  - `PUT /api/v1/line?text=…` → hänge utf8-string als zeile an
  - `PUT /api/v1/screen?text=…` → ersetze inhalt mit utf8-string
  - `PUT /api/v1/location?x=…&y=…&text=…` → schreibe utf8-string an (x,y)
  - `GET /api/v1/current` → liefert den aktuellen Inhalt
- HTML-Frontend
  - Spricht mit der REST-API
  - Bietet die drei API-Interfaces als "hübsches Element" an

## Test-Sequenzen

### Full Mode
Praktisch zum Testen in der Entwicklungsphase, löscht alle Anzeigen
und ersetzt den Inhalt der Anzeige mit dem gesendeten Datagramm:

```
nc -u 10.42.25.49 8001
```

### Line Mode
Praktisch zum Testen der gesamten Anzeige. Pusht eine Zeile Text an die
Fallblattanzeige und scrollt ggf. den Rest nach oben.

```
nc -u 10.42.25.49 8000
```