uso: git tag [-a | -s | -u <ID chiave>] [-f] [-m <messaggio> | -F <file>]
		<nome tag> [<head>]
   oppure: git tag -d <nome tag>...
   oppure: git tag -l [-n[<numero>]] [--contains <commit>] [--no-contains <commit>] [--points-at <oggetto>]
		[--format=<formato>] [--[no-]merged [<commit>]] [<pattern>...]
   oppure: git tag -v [--format=<formato>] <nome tag>...

    -l, --list            elenca i nomi dei tag
    -n[<n>]               stampa le prime <n> righe di ogni messaggio tag
    -d, --delete          elimina tag
    -v, --verify          verifica tag

Opzioni creazione tag
    -a, --annotate        tag annotato, richiede un messaggio
    -m, --message <messaggio>
                          messaggio tag
    -F, --file <file>     leggi il messaggio da un file
    -e, --edit            forza modifica del messaggio tag
    -s, --sign            tag annotato e firmato con GPG
    --cleanup <modo>      come rimuovere gli spazi e i #commenti dal messaggio
    -u, --local-user <ID chiave>
                          usa un'altra chiave per firmare il tag
    -f, --force           sostituisci il tag se esiste
    --create-reflog       crea un registro riferimenti

Opzioni elenco tag
    --column[=<stile>]    visualizza l'elenco dei tag in colonne
    --contains <commit>   stampa solo i tag che contengono il commit
    --no-contains <commit>
                          stampa solo i tag che non contengono i commit
    --merged <commit>     stampa solo i tag sottoposti a merge
    --no-merged <commit>  stampa solo i tag non sottoposti a merge
    --sort <chiave>       nome campo in base a cui ordinare
    --points-at <oggetto>
                          stampa solo i tag dell'oggetto
    --format <formato>    formato da usare per l'output
    --color[=<quando>]    rispetta le stringhe di formato per i colori
    -i, --ignore-case     l'ordinamento e il filtraggio non fanno differenza tra maiuscole e minuscole

