UNRELEASED



- Mailversand angepasst an Shopversion 3.1.5 (Variable für Priorität)

1.0.6 [30.07.2025]

- Fehler in der Berechnung Mindestlagerbestand für geplante Aufgaben - behoben
- PHP-Warnmeldung bei nicht aktivierten E-Mailadressen - behoben
- Auto-Update Versionsnummer in der Tabelle "configuration"

1.0.5 [22.07.2025]

- aus der Erinnerungsliste kann direkt eine Erinnerung versandt werden
- Zusätzlich wird beim Produkt eine Info angezeigt, sobald sich mehrere Kunden in die Erinnerungsliste eingetragen haben
- Benachrichtigung ab prozentualem Mindestlagerbestand hinzugefügt
- Mailversand als "geplante Aufgabe" hinzugefügt
- Hinweis für das "Double Opt In" werden mit CSS-Klasse "errormessage" ausgegeben
- Frontendtexte "Kundenerinnerung" in "Produkterinnerung" umbenannt
- im tpl_modified wurden durch das Systemmodul nicht alle Dateien gelöscht

1.0.4 [02.08.2024]

- Neue Funktion "Update" hinzugefügt
- Neue Option "Double-Opt-In für Kundenerinnerung"
- Neue Option "Unterzeichnen des Datenschutzes auch für angemeldete Kunden?"
- Neue Option "Mailversand sofort?"
- Anpassung PHP 8.2


1.0.3 [09.06.2024]

- Wenn der Artikelname das Zeichen "%" enthielt kam es zu einem PHP-Fehler - behoben
- Mailversand nur einmal täglich - Code geändert

1.0.2 [24.04.2024]

- Kundenerinnerung wurde auch ohne Opt-in versandt
- deutsche Selbstlaute in E-Mails
- customers_remind.php wird nach dem Login nicht mehr als "letzte aufgerufen Seite" behandelt - danke fiveBytes

1.0.1 [18.04.2024]

- neue Einstellmöglichkeiten im Systemmodul
	-- Button-Bild wählen
	-- Individueller Button-Text
	-- Warenkorb-Button anzeigen
- mail/german/remind_activate_mail.txt Fehler "ü" statt "&uuml;"


1.0.0 [15.04.2024]

- Übertragung der Dateien zu Github
