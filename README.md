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
