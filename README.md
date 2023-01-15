# WordPress Website.

## Development
Si sviluppa in genere sul branch development che deve sempre essere allineato al branch master.
Per tenere allineato development a master bisogna fare ```git checkout development && git merge master```, se ci sono degli hotfix si possono fare direttamente su master 

I file di configurazione sono: **`.env`** e **`wp-cli.local.yml`**, vengono generati dallo script di build e non sono versionati perché legati all'ambiente in cui si trovano
 
## Deploy
Per mandare le modifiche in produzione basta fare  ```git checkout master && git merge development && git push```

per allineare il sito di test basta fare ```git push``` sul branch development


## Structure
Tutte le modifiche che non impattano aspetti grafici o puramente nell'area d'azione di un tema WordPress
vanno fatte nel plugin mu-plugins/my-custom-functions.php
il plugin è sempre attivo e nel caso in cui si volesse cambiare tema queste modifiche non vanno perse



## Installare un plugin
Se il plugin si trova su [WordPress Packagist] (https://wpackagist.org/)
```bash
lando composer require "wpackagist-plugin/plugin-name"
```
Se invece il plugin non e disponibile su WordPress Packagist
caricare il plugin zippato nella cartella zip e aggiungere al file **`composer.json`** le seguenti linee di codice
```json
        {
            "type": "package",
            "package": {
                "name": "autore-plugin/nome-cartella-plugin",
                "version": "numero.versione.plugin",
                "dist": {
                    "url": "wp-content/artifact/nome-cartella.zip",
                    "type": "zip"
                },
                "type": "wordpress-plugin"
            }
        },
```

## Controllare lo stato dei plugin
per conoscere slug del plugin (nome-plugin), versione e se il plugin è attivo o no
eseguire il comando 

```bash
lando wp @envplaceholder plugin list
```
@envplaceholder è un alias per il server di test, se si vuole runnare il comando sul sito di test, usare @staging, per l'ambiente online @production


## Disinstallare un plugin
Guardare i plugin presenti sul sito e disinstallare il plugin
```bash
lando wp @envplaceholder plugin uninstall nome-plugin --deactivate
```


## Aggiornare un plugin 

```bash
lando wp @envplaceholder plugin list
```
tutti i plugin che hanno update available sono da aggiornare, per aggiornarli cambiare la versione nel file **`composer.json`**




## Allineamento database

Per portare il database da produzione a locale eseguire il seguente comando
```bash
lando wp @envplaceholder db export - | lando wp db import -
```

## Rigenerare i salts

Eseguire il seguente comando dalla root e controllare che le secret keys
siano state create o rigenerate se già presenti nel file **`wp-salts/wp-config.php`**

```bash
wp config shuffle-salts --path=wp-salts
wp config shuffle-salts WP_CACHE_KEY_SALT --force --path=wp-salts
```

