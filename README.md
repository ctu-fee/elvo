# Elvo - elektronické volby

## Úvod

Elvo je aplikace na elektronické volby do Akademického senátu ČVUT. V současné době je potřeba zvláštní instance aplikace pro každý volební obvod (fakulta). Danou instanci aplikace provozuje volební komise příslušného volebního obvodu.

Aplikace ověřuje voliče pomocí autentizačního systému **cvutID**, který nabízí následující možnosti:

- autentizace uživatele pomocí jeho ČVUT uživatelského jména a hesla
- ověření, zda uživatel smí volit v daném volebním obvodě
- určení volební roli voliče - student nebo zaměstnanec

Pro ukládání elektronických volebních lístku používá aplikace lokální databáze. Před uložením je každý volební lístek zašifrován pomocí veřejného SSL klíče. Pro vyhodnocení výsledků voleb je pak potřeba jednotlivé lístky dešifrovat pomocí odpovídajícího privátního klíče. Šifrování volebních lísků zajistí, že volební data nelze v průběhu číst ani měnit. 

K aplikaci patří i utilita pro příkazovou řádku, která zjednodušuje provádění některých úkonů.

## Požadavky

- PHP >= 5.3.3
- PHP rozšíření - pdo, intl
- [Shibboleth Service Provider](http://shibboleth.net/products/service-provider.html)
- [GIT](http://git-scm.com/)

## Příprava prostředí

### OS, hostname a SSL certifikát

Je potreba mit k dispozici server s OS Linux. Doporucujeme, posledni verzi Debian Linux, ale v zasade by nemel byt problem i s jinou distribuci. Server by mel mit vhodne nastavene jmeno, napriklad pro FEL je to volby.fel.cvut.cz.

Je nezbytne, aby veskera komunikace probihala po SSL, takze pro dane jmeno serveru je potreba poridit SSL certifikat, ktery je overitelny v beznych prohlizecich. Informace o tom, jak zazadat o takovy certifikat najdete zde:

http://pki.cesnet.cz/cs/st-guide-tcs-server.html

Zadost lze vyridit i velmi rychle, ale vzhledem k tomu, ze je potreba potvrzeni od dvou zodpovednych osob (na urovni FEL a na urovni CVUT) a dale zalezi na tom, jak rychle budou jednat zamestnanci CESNETu a jak rychle vubec vyhovi certifikacni autorita, je treba pocitat s tim, ze se to muze zdrzet. Obvykle to trva do dalsiho dne.

### Autentizace pomocí cvutID

Volební aplikace využívá centralizovaný autentizační systém cvutID, který je postaven na systému [Shibboleth](http://shibboleth.net/). Tímto způsobem lze autentizovat uživatele (voliče) bez nutnosti implementovat vlastní způsob přihlášení. Uživatele musí zadat svoje ČVUT uživatelské jméno a heslo. Následně volební aplikace získá unikátní anonymní identifikátor uživatele a také jeho volební roli - student nebo učitel. 

Pro využití služeb cvutID je potřeba nainstalovat [Shibboleth Service Provider](http://shibboleth.net/products/service-provider.html). Pro instalaci a konfiguraci lze použít [informace a návody k systému FELid](https://wiki.fel.cvut.cz/net/admin/aai/index), který je na stejném principu.

## Instalace

Zdrojové kódy lze získat klonováním GIT repozitáře. Zvolte vhodný adresář a použijte následující příkaz:

```
$ git clone <repository>
```

Tento příkaz vytvoří lokální kopii zdrojových kodu v podadresáři `elvo`. 

Je potřeba doinstalovat knihovny, na kterém je aplikace závislá. Toto lze udělat jednoduše pomocí nástroje [Composer](http://getcomposer.org/). V kořenovém adresáři stačí spustit:

```
$ cd elvo
$ ./composer.phar --no-dev install
```

Tento příkaz stahne a nainstaluje potřebné knihovny do podadresáře `vendor/`.

## Klíče pro šifrování volebních lístků

Každý elektronický volevní lístek se před uložením nejdříve zašifruje. Tímto se zajistí, že nebude pozměněn a také nebude možné zjišťovat průběžné výsledky. Pro šifrování aplikace používá SSL certifikát a klíč. Pomocí certifikátu (veřejného klíče) se volební lístek zašifruje a pomocí privátního klíče lze pak lístek dešifrovat.

Pro každé volby je potřeba vygenerovat nový pár klíčů. Certifikát je pak potřeba uložit do souboru `data/ssl/crypt.crt`. Privátní klíč je potřeba držet na bezpečném místě mimo systém, na kterém pobeží volební aplikace. Doporučuje se vytvořit alespoň dvě kopie na externím flash disku. Privátní klíč se do systému nahraje až po oficiálním ukončením voleb a s jeho pomocí se zpracují výsledky.

Nový pár klíčů lze jednoduše vygenerovat pomocí přiloženého skriptu v podadresáři `data/ssl/`:

```
$ cd data/ssl
$ ./openssl.sh
```

Po spuštěním skriptu můžete potvrdit všechny dotazy zmačknutím ENTER. Tyto údaje nejsou relevantní, důležité jsou samotne klíče, které budou vygenerovány. Pokud vše proběhne v pořádku, v adresáři se objeví soubory `crypt.crt` (certifikát, veřejný klíč) a `crypt.key` (privátní klíč). Jak bylo napsáno výše, privátní klíč přesuňte mimo systém, nejlepe na přenosné flash paměti. Je vhodné též udělat kopii klíče, protože je to jediný způsob, jak dešifrovat volební data.

## Konfigurační soubor

Konfigurace aplikace je umístěna v podadresáři `config/autoload`. Použijeme vzorový konfigurační soubor `elvo.local.php.dist` a zkopírujeme jej bez přípony `.dist`:

```
$ cd config/autoload
$ cp elvo.local.php.dist elvo.local.php
```

Konfigurační soubor obsahuje výchozí nastavení, které je potřeba přizpůsobit pro konkrétní volby.

V sekci `vote_manager` je potřeba nastavit:

- `enabled` na _true_ - určuje zda je možné volit
- `start_time` a `end_time` - začátek a konec voleb
- `chamber_max_candidates` - maximální počet kandidátů, které je možné zvolit pro jednotlivé komory (studentská a akademická)
- `chamber_max_votes` - maxiamální počet hlasů, které může dát volič (maximální počet kandidátů, které může zvolit), bvykle jsou tato čísla shodna s údaji z `chamber_max_candidates`
- `electoral_name` - název volebního obvodu (název fakulty, součásti)
- `contact_email` - kontaktní email na volební komisi

V sekci `db` je možné nastavit cestu k databázovému souboru, kde budou uloženy volební lístky. Je nezbytné, aby web server měl v tomto adresáři právo zápisu.

V sekci `authentication` je potřeba nastavit způsob autentizace pomocí tzv. adapteru. Jsou k dispozici dva předkonfigurované adaptery:

- `ZfcShib\Authentication\Adapter\Shibboleth` - standardní adpater, který se má použít během voleb - využívá externi autentizaci pomocí systému Shibboleth (napojen na cvutID)
- `ZfcShib\Authentication\Adapter\Dummy` - testovací adapter, kterým je možné simulovat chování standardního adapteru. Je vhodný pro testování, když aplikace ještě neni napojena na cvutID. Ve výchozí konfiguraci se pokaždé generuje nové unikátní ID voliče, tudíž je možné hlasovat opakovaně. Zároveň jsou přítomny obě voličske role (student a zaměstnanec), tudíž je možné testovat volbu do obou komor senátu.

Je nezbytné, aby byl vždy aktivní pouze jeden autentizační adapter. Ten, ktery nepoužíváte, zakomentujte. Ve výchozím stavu se používá ten testovací.

V sekci `authentication` je ještě podsekce `role_extractor`. Tam je potřeba nastavit kód konkrétní součásti (fakulty), pro které se provádí volby. Například pro FEL je to `13000`.

Další konfigurační direktivy je nutné měnit pouze pokud potřebujete nastavit jiné, než výchozí hodnoty (umístění dat kandidátů, umístění SSL klíčů apod.).

## Inicializace databáze

Je potřeba vytvořit SQLite databázi a příslušné tabulky. K tomu použijeme utilitu pro příkazovou řádku:

```
$ bin/elvo.php db:init
```

Tento příkaz vytvoří SQLite databázi v místě, které bylo nastavené v konfiguračním souboru (sekce `db/database`).

## Data kandidátů

Pro jednoduchost se data kandidátů ukládají do textového souboru, který můžete editovt ručně. Ve výchozím nastavení je to soubor v JSON formátu `data/candidates/candidates.json`. Jako vzor můžete použít příložený příklad `data/candidates/candidates.json.dist`.

Příklad jednotlivých záznamů:

```json
[
     {
        "id" : 1,
        "first_name" : "Vladimír",
        "last_name" : "Hašek",
        "chamber" : "academic",
        "email" : "hasek@example.cz",
        "profile_url" : "http://hasek.cz/",
        "candidate_url" : "http://example.cz/hasek.pdf"
    },
    {
        "id" : 2,
        "first_name" : "Václav",
        "last_name" : "Novák",
        "chamber" : "student",
        "email" : "novak@example.cz",
        "profile_url" : "",
        "candidate_url" : "http://example.cz/novak.pdf"
    }
]
```

Vysvětleni jednotlivých polí:

- `id` - unikátní ID kandidáta
- `first_name` - jméno
- `last_name` - příjmení
- `chamber` - komora senátu, do které kandiduje, možné hodnoty jsou _student_ a _academic_
- `email`
- `profile_url` - URL domovské stránky kandidáta
- `candidate_url` - URL kandidátky

## Hledání příčin problémů a chyb

Během instalace a konfigurace se můžou vyskytnout různé problémy:

- chybějící závislosti - PHP rozšíření nebo knihovny
- chyby v konfiguraci - chybějící nebo špatně nastavené direktivy
- problémy s databázi - chybějící nebo špatně inicializovaná databáze
- problémy s autentizaci (Shibboleth)
- chybějící SSL klíče
- chybějící soubor s kandidáty
- ...

Informace o chybách lze většinou získat z PHP error logu. Logování do souboru neni implicitně povolené v konfiguraci PHP, je potřeba ho zapnout.

## Průběh voleb 

Volby jsou aktivní (lze volit) pokud jsou splněny následující podmínky:

- volení je povoleno (direktiva _enabled_ v konfiguraci `vote_manager`)
- aktuální čas je mezi `start_time` a `end_time` (též v konfiguraci `vote_manager`)

Většinou neni potřeba během voleb nijak zasahovat do aplikace. Pokud z nejakých důvodů je potřeba volby přerušit, stači nastavit _enabled_ na `false`.

## Vyhodnocení výsledků voleb

Před zpracováním výsledků se ujistěte, že privátní klíč je umístěn na správném místě. Ve výchozím nastavení je to soubor `data/ssl/crypt.key`.

Vyhodnocení výsledku lze udělat jednoduš pomocí utility pro příkazovou řádku:

```
$ bin/elvo.php vote:result

Total votes: 893
academic: 251
student: 742

academic
+----------------+-------+
| candidate      | votes |
+----------------+-------+
| Vladimír Hašek | 123   |
| Petr Pavel     | 101   |
| Franta Schlus  | 98    |
| Václav Novák   | 45    |
+----------------+-------+

student
+-------------+-------+
| candidate   | votes |
+-------------+-------+
| Jan Modrý   | 692   |
| Pavel Černý | 318   |
+-------------+-------+
```

Dále je možné exportovat všechny volební lístky pomocí příkazu:

```
$ bin/elvo.php vote:export
```

Výsledkem je výpis všech volebních lístku ve formátu JSON. Pokud je potřeba data uložit do souboru, lze to jednoduše udělat přesměrováním standardního výstupu:

```
$ bin/elvo.php vote:export > /tmp/votes.json
```

## Autor

- [Ivan Novakov](mailto:ivan.novakov@fel.cvut.cz)

## Licence

- [BSD 3 Clause](debug.cz/license/bsd-3-clause)