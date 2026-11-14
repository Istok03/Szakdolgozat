# Istók IT – Informatikai Garázs

Laravel-alapú informatikai webáruház, amely programtervező informatikus szakdolgozat részeként készült. Az alkalmazás vásárlói és adminisztrációs felületet tartalmaz: a termékek böngészésétől és kosárba helyezésétől a rendelés leadásáig, illetve a termékek, készletek és rendelések kezeléséig mutatja be egy webshop működését.

A Laravel-projekt a **SzakDogaWebShop** mappában található. Az alábbi parancsokat ebben a mappában kell futtatni, kivéve a belépéshez megadott `cd` parancsot.

## Funkciók

### Vásárlói felület

- Regisztráció és bejelentkezés magyar hibaüzenetekkel.
- Profiladatok és jelszó módosítása.
- Termékkatalógus, termékadatlapok és akciós termékek.
- Szűrés terméknév, kategória, ár és akció alapján.
- Vendégkosár és bejelentkezett felhasználóhoz tartozó kosár; belépéskor a vendégkosár összevonása.
- Mennyiségek módosítása, termékek eltávolítása és készletellenőrzés.
- Házhoz szállítás vagy személyes átvétel választása.
- Utánvét, helyszíni fizetés és szimulált bankkártyás fizetés.
- Rendelési visszaigazolás, rendelési előzmények és rendelésrészletek.
- Termékértékelések és kapcsolatfelvételi űrlap.
- Mobilképernyőkhöz is igazodó megjelenés.

### Adminisztráció

- Termékek, termékképek, árak, kedvezmények és készletek kezelése.
- Kategóriák kezelése.
- Rendelések megtekintése és állapotuk módosítása.
- Felhasználói adatok és jogosultságok kezelése.
- A kapcsolatfelvételi űrlapon érkező üzenetek megtekintése.

Az adminisztrációs útvonalak bejelentkezést és adminjogosultságot igényelnek. A rendelés összege és szállítási díja szerveroldalon számolódik, a készlet módosítása adatbázis-tranzakcióban történik.

## Technológiák

| Terület | Megoldás |
| --- | --- |
| Backend | PHP 8.2+, Laravel 12 |
| Felület | Blade, CSS, JavaScript, Tailwind CSS, Alpine.js |
| Adatbázis | MySQL; helyi fejlesztéshez XAMPP is használható |
| Frontend összeállítása | Node.js, npm, Vite |
| PHP-tesztek | PHPUnit, elkülönített SQLite-adatbázissal |
| Böngészős tesztek | Cypress |

## Helyi telepítés

### 1. Előkészületek

Szükséges a PHP 8.2 vagy újabb verziója, Composer, MySQL, valamint Node.js és npm. A böngészős tesztek Node.js 24 környezetben is ellenőrizve lettek. A PHP-ban legyen elérhető a MySQL PDO-meghajtó, a teszteléshez pedig a SQLite PDO-meghajtó is.

XAMPP használatakor indítsd el a MySQL szolgáltatást. A `php artisan serve` használatához nem szükséges külön Apache-ot indítani.

A repó letöltése vagy klónozása után lépj be az alkalmazás mappájába:

```bash
cd SzakDogaWebShop
composer install
npm ci
```

Az npm telepítés a Cypresshez szükséges tesztböngészőt is letöltheti, ezért az első telepítés hosszabb ideig tarthat.

### 2. Környezeti beállítások

Első telepítéskor másold a `.env.example` fájlt `.env` néven. PowerShellben:

```powershell
Copy-Item .env.example .env
```

Meglévő `.env` fájlt ne írj felül. Állítsd be benne a saját helyi adatbázis-kapcsolatodat. Példa helyi XAMPP-környezethez:

```dotenv
APP_NAME="Istók IT"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=szakdogawebshop
DB_USERNAME=root
DB_PASSWORD=
```

A felhasználónevet és a jelszót a saját MySQL-beállításaidhoz igazítsd. A `.env` fájl helyi konfiguráció, nem része a verziókezelt forráskódnak.

Új környezetben generáld az alkalmazáskulcsot:

```bash
php artisan key:generate
php artisan config:clear
```

### 3. Adatbázis létrehozása vagy importálása

Hozd létre a `szakdogawebshop` adatbázist például phpMyAdminban, UTF-8 karakterkészlettel, majd válaszd a megfelelő lehetőséget:

- **Új, üres telepítés bemutatóhoz:** futtasd a migrációt, majd a `php artisan db:seed` parancsot. Ez 1 adminfiókot, 1 vásárlói fiókot és 5 kategóriában összesen 25 terméket hoz létre.
- **Meglévő bemutatóadatok használata:** előbb importáld a külön átadott SQL-mentést, utána futtasd az alábbi migrációt a még hiányzó sémaváltozások alkalmazásához.

```bash
php artisan migrate
```

A GitHubról letöltött forráskód önmagában nem másolja át a helyi MySQL-adatokat. A teljes bemutatókatalógushoz külön adatbázis-export szükséges; a hozzá tartozó képfájloknak is rendelkezésre kell állniuk a `public/images/products` mappában.

Új, üres adatbázis bemutatóadatokkal egy lépésben is előkészíthető:

```bash
php artisan migrate --seed
```

A seeder 5–5 terméket hoz létre a Laptopok, Egerek, Billentyűzetek, Monitorok és Fejhallgatók kategóriában. Minden termékhez magyar leírás, bemutatóár, pozitív készlet és termékfotó tartozik. A laptopok között MacBook és ASUS modellek is szerepelnek. Kategóriánként egy termék akciós. A seeder a `public/images/products` meglévő PNG-képeiből készít termékenként külön másolatot, internetes letöltés és SVG-helykitöltők nélkül; ennek a mappának írhatónak kell lennie. Öt monitorhoz három különböző monitorfotó szolgál illusztrációként.

A korábbi seeder által létrehozott SVG-s mintatermékeket a `php artisan db:seed --class=ProductSeeder` parancs képes fotós változatra frissíteni. Az érintett laptopok neve is frissül; az árak, készletek és rendelési kapcsolatok megmaradnak. A már saját fotóval ellátott termékek képét nem írja felül. A lecserélt, több termék által nem használt generált SVG-ket eltávolítja.

Az ismételt futtatás nem duplázza a meglévő nevű termékeket és kategóriákat, illetve e-mail-című fiókokat. A már létező adatok, jelszavak, árak és készletek nem íródnak felül. A pontos 2 fiók / 5 kategória / 25 termék üres adatbázisra vonatkozik; meglévő adatbázison a korábbi adatok megmaradnak. A bemutatótermékek neve az újrafuttatáskor azonosítóként szolgál, ezért egy átnevezett mintatermék eredeti változata újra létrejöhet.

**Meglévő vagy importált adatbázison ne futtasd a `php artisan migrate:fresh` parancsot, mert törli a táblákat és az adatokat.** A teljes, kézzel összeállított katalógushoz továbbra is külön SQL-mentés használható; a seeder önállóan kipróbálható, kisebb mintakatalógust biztosít.

### 4. Indítás

```bash
npm run build
php artisan serve
```

Az alkalmazás alapértelmezett helyi címe: **http://127.0.0.1:8000**.

Frontend-fejlesztés közben a build helyett külön terminálban futtatható az `npm run dev`. Ha leállítod a Vite fejlesztői szervert, és az oldal továbbra is azt próbálja elérni, ellenőrizd a `public/hot` fájlt; leállított Vite mellett törölhető, majd újra futtatható az `npm run build`.

Ha Windows alatt a `php` parancs nem található, add a PHP könyvtárát a PATH-hoz, vagy használd a teljes elérési utat, például: `C:\xampp\php\php.exe artisan serve`.

## Adminisztrátori hozzáférés

A `php artisan db:seed` futtatása után új telepítésen a következő helyi bemutatófiókokkal lehet belépni:

| Jogosultság | E-mail-cím | Jelszó |
| --- | --- | --- |
| Adminisztrátor | `admin@example.com` | `admin123` |
| Vásárló | `vasarlo@example.com` | `vasarlo123` |

Ezek nyilvános tesztadatok, kizárólag helyi bemutatóhoz valók. Meglévő, azonos e-mail-című fiók jelszavát és jogosultságát a seeder nem változtatja meg.

A vásárlói fiókkal válassz terméket, helyezd kosárba, majd adj le rendelést. Kijelentkezés után az adminfiókkal az `/admin/orders` oldalon megtekinthető és kezelhető a rendelés. A rendszer induláskor nem generál véletlenszerű rendeléseket vagy további felhasználókat.

Saját adminfiók létrehozásához regisztrálj egy felhasználót, majd a helyi adatbázisban a hozzá tartozó `users.is_admin` mezőt állítsd `1` értékre.

Ez phpMyAdminban vagy Tinkerben is megtehető:

```bash
php artisan tinker
```

A Tinkerben az alábbi példában szereplő címet cseréld a saját regisztrált e-mail-címedre:

```php
App\Models\User::where('email', 'sajat@email.hu')->update(['is_admin' => true]);
```

Ezután a bejelentkezett felhasználó az `/admin` útvonalon érheti el az adminfelületet. Új telepítésnél itt hozhatók létre a kategóriák és a termékek. Vásárlás teszteléséhez a termékekhez pozitív készletmennyiséget adj meg.

## Szállítás és fizetés

A szállítási módok, díjak és az átvételi cím a `config/shipping.php` fájlban módosíthatók.

- Házhoz szállítás alapdíja: **1 990 Ft**; szállítási cím megadása kötelező.
- Személyes átvétel díja: **0 Ft**; nem szükséges vásárlói szállítási cím.
- A beállított átvételi cím bemutatócím.

### Demó bankkártyás fizetés

A kártyás fizetés **szimuláció**, nem kapcsolódik bankhoz vagy fizetési szolgáltatóhoz, és nem von le pénzt. Kizárólag az alábbi tesztkártyák használhatók:

| Kártyatípus | Tesztkártyaszám | Eredmény |
| --- | --- | --- |
| Visa | `4242 4242 4242 4242` | Sikeres szimuláció |
| Mastercard | `5555 5555 5555 4444` | Sikeres szimuláció |
| Visa | `4000 0000 0000 0002` | Elutasítás |
| Mastercard | `5105 1051 0510 5100` | Elutasítás |

Lejáratként aktuális vagy későbbi hónapot adj meg `HH/ÉÉ` formátumban, például `12/30`. A teszt CVC bármely három számjegy lehet, például `123`.

Az ellenőrzés része a Luhn-algoritmus, a lejárat és a CVC formátumának vizsgálata, valamint a tesztkártyák listájával történő összevetés. A teljes kártyaszám és a CVC nem kerül a rendelésbe: csak a kártyatípus, az utolsó négy számjegy, a demófizetés állapota és egy generált referencia tárolódik. Elutasított fizetéskor a kosár és a készlet változatlan marad.

## Tesztelés

### PHP-tesztek

```bash
composer test
```

A tesztek a `phpunit.xml` beállításai alapján memóriabeli SQLite-adatbázist használnak. Többek között a hitelesítést, jogosultságokat, kosárkezelést, rendeléseket, készletváltozásokat, szállítást és demófizetést ellenőrzik.

### Cypress UI-tesztek

Automatikus futtatás:

```bash
npm run test:ui
```

Interaktív tesztfelület:

```bash
npm run test:ui:open
```

A parancsok először elkészítik a frontend buildet, majd külön helyi tesztszervert indítanak a **8877-es porton**. Ennek a portnak szabadnak kell lennie. A tesztek saját adatbázisa a `storage/framework/cypress/database.sqlite`; ez minden teszteset előtt újra létrejön, a webshop MySQL-adatbázisát nem használja.

A UI-tesztek a bejelentkezést, a kosárműveleteket, a szállítási mód váltását, a demófizetés sikeres és sikertelen eseteit, valamint a lenyitható segítséget és több képernyőszélességet vizsgálnak.

A tesztfuttató Windows alatt felismeri a `C:\xampp\php\php.exe` elérési utat. Más telepítési hely esetén a `PHP_BINARY` környezeti változóval adható meg a PHP futtatható fájlja.

## A projekt felépítése

```text
SzakDogaWebShop/
├── app/Http/Controllers/   Vásárlói és admin vezérlők
├── app/Http/Requests/      Kérések validálása
├── app/Models/             Adatbázismodellek
├── app/Services/           Kosár-, rendelési és demófizetési logika
├── config/                Alkalmazás- és szállítási beállítások
├── database/migrations/   Az adatbázis szerkezetének változásai
├── database/seeders/      Bemutatófiókok és 25 termékből álló mintakatalógus
├── public/                Képek, CSS- és JavaScript-fájlok
├── resources/views/       Blade-nézetek
├── routes/                Webes útvonalak
├── tests/                 PHP-tesztek és a UI-tesztek kiszolgálása
└── cypress/e2e/           Böngészős tesztesetek
```

## A bemutató korlátai

A projekt szakdolgozati bemutatóalkalmazás. A termékárak és készletek bemutatóadatok, egyes termékképek helykitöltők lehetnek. A kártyás fizetés nem éles fizetési megoldás. A `.env.example` alapértelmezett `MAIL_MAILER=log` beállítása az alkalmazás által küldött leveleket naplózza, nem kézbesíti őket valódi postafiókba.
