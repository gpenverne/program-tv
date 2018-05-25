# ProgramTv

Ce script parse le programme tv (récupéré depuis [http://www.xmltv.fr/guide/tvguide.xml](http://www.xmltv.fr/guide/tvguide.xml)) et retourne la chaîne correspondante à ce qui est cherché.
Exemples:
```bash
$ ./run.php "documentaire"
# Ok j'ai mis titre du documentaire sur france5
$ ./run.php "scènes de ménage"
# Ok j'ai mis scènes de ménage sur m6
```

## Callback
Le fichier ``callback.php`` est appelé lorsqu'un programme est trouvé.
Le fichier est généré depuis ``callback.php.dist``
Les infos sur le programme sont récupérables comme suit:
```php
<?php
    // callback.php
    return 'Ok, j\'ai mis "'.$program->title.'" sur '.$program->channel_name.' sur le canal '.$program->channel;

    // On peut imaginer un shell_exec
    // shell_exec('/chemin/command.sh '.$program->channel);
?>

```
## Mise à jour du programme tv

```bash
$ cron.sh
```

## channels.php
Le fichier channel.php fait la correspondance entre les noms de chaînes et leur numéros.
En commentant une chaîne, cela l'exclut de la recherche.
