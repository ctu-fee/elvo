# Role

Jelikož se u voleb do senátu ČVUT volí zvlášť do studentské a zvlášť do akademické komory, rozlišujeme následující voličské role:

- student
- akademický pracovník

Většinou má volič pouze jednu roli. V některých případech může mít volič obě role. Jelikož každý volič může volit pouze jednou, musí si v takových případech předem vybrat, pod jakou roli si přeje volit.

Voličské role získává aplikace na základě dat, které dostane od Usermapu (uživatelské databáze ČVUT) během přihlášení voliče. Konkrétně jde o atribut ve tvaru:

```
B-13000-ZAMESTNANEC-AKADEMICKY
```

kde:

- **B** je označení pro "business" roli
- **13000** je kód fakulty
- **ZAMESTNANEC-AKADEMICKY** je popis role

Je možné, že volič má různé role na různých fakultách. Proto je pro konkrétní volby nezbytné nastavit v konfiguraci aplikace, o jakou fakultu se jedná:

```php
    'role_extractor' => array(
        'class' => 'Elvo\Mvc\Authentication\Role\CvutRoleExtractor',
        'options' => array(
            /*
             * The department (faculty) code(s) to extract roles for.
             */
            'department_code' => array(
                '13000'
            )
        )
    )
```
V případě, že se volby konají zároveň pro více součástí (fakult), je možné uvést více hodnot:

```php
    'role_extractor' => array(
        'class' => 'Elvo\Mvc\Authentication\Role\CvutRoleExtractor',
        'options' => array(
            /*
             * The department (faculty) code(s) to extract roles for.
             */
            'department_code' => array(
                '13000', '14000'
            )
        )
    )
```

V případě, že pro danou fakultu nemá volič žádnou relevantní roli ("student" nebo "akademický pracovník"), nemá právo zůčastnit se voleb a aplikace ukáže chybovou hlášku.
