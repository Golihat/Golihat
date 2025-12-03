# Kryddbox föreningsportal – modulär plugin-arkitektur

Den här designen beskriver hur vi kan bygga WordPress-pluginet så att varje större funktion aktiveras/avaktiveras separat, med en central setup-sida för superadmin.

## Översikt
- **Huvudplugin**: ansvarar för bootstrap, registrerar moduler och renderar setup-sidan.
- **Moduler**: kapslade i egna klasser/mappar, med samma livscykel-API (`register`, `boot`, `deactivate`).
- **Feature toggles**: lagras i `options` eller egen tabell (`kb_feature_flags`) och caches med `wp_cache`/transients.
- **Admin-UI**: en “Setup & funktioner”-sida som listar alla moduler med switchar, beskrivningar, beroenden och larm/diagnostik.

## Filstruktur (kort)
```
/wp-content/plugins/kb-foreningsportal/
├─ kb-foreningsportal.php
├─ /includes/
│   ├─ class-kb-module-manager.php   (registrerar/modular loader)
│   ├─ modules/
│   │   ├─ class-kb-module-members.php
│   │   ├─ class-kb-module-materials.php
│   │   ├─ class-kb-module-qr.php
│   │   ├─ class-kb-module-admin-commission.php
│   │   ├─ class-kb-module-stats.php
│   │   ├─ class-kb-module-campaign-mailer.php
│   │   └─ class-kb-module-support-tools.php
│   └─ class-kb-feature-flags.php     (persistens + cache för toggles)
├─ /admin/
│   ├─ class-kb-admin-setup-page.php  (setup-sidan med switchar och status)
│   └─ /views/setup-page.php
```

## Modul-API (förslag)
```php
interface KB_Module {
    public function id(): string;          // t.ex. "members".
    public function label(): string;       // visningsnamn.
    public function description(): string; // kort info till setup-sidan.
    public function depends_on(): array;   // lista av modul-IDn.

    public function register(): void;      // registrera hooks/REST-routes.
    public function boot(): void;          // körs när WP laddar (om aktiv).
    public function deactivate(): void;    // städa cron/transients när av.
}
```
- Moduler registreras via `KB_Module_Manager`, men `boot()` körs bara om togglen är aktiv.
- Beroenden kontrolleras så att t.ex. `campaign_mailer` kräver `campaigns`-modulen.
- Varje modul kan exponera sin egen `settings_section` på setup-sidan.

## Feature toggles
- Lagra per modul i `kb_feature_flags` (modul_id, enabled, updated_at, updated_by).
- Cache:a i `wp_cache`/transient för färre DB-slag; invalidera på toggle-ändring.
- Koppla till befintliga flaggor (t.ex. `features.members.enabled`) för bakåtkompatibilitet.

## Setup-sida (superadmin)
- Eget menyalternativ: **Kryddbox » Setup & funktioner**.
- Innehåll:
  - Kort per modul med **switch**, beskrivning, beroenden, status (OK/varning/fel), och snabblänkar (t.ex. “Regenerera QR”, “Skicka tests mail”).
  - Summering över aktiva moduler och logg för senaste ändringar (skriv till `kb_logs`).
  - “Visa som”-funktion används inte på setup-sidan (endast superadmin har access).
- Åtgärder:
  - Toggle POST-anrop till REST-endpoint `/kb-forening/v1/features/{module}` (nonce-skydd, capability `manage_options`).
  - Bekräftelsemodal för moduler med databaspåverkan (t.ex. medlemmar/provision).

## Säkerhet och stabilitet
- Alla queries: preparerade statements/`$wpdb->prepare`.
- Logga alla toggle-ändringar i `kb_logs` (vem, vad, före/efter, IP).
- Vid avstängning: kör `deactivate()` för att rensa cronjob/queues/transients så inget hänger kvar.
- Health-check per modul: modul kan exponera `status()` som används på setup-sidan för att visa om beroenden eller externa tjänster (t.ex. mail/QR-renderer) fungerar.

## Migrering av befintliga feature toggles
- Kartlägg nuvarande flaggor (`features.members.enabled`, `features.qr.auto_generate`, m.fl.).
- Skapa en init-migration som lägger in default-rader i `kb_feature_flags` baserat på nuvarande optioner.
- Behåll läsning av gamla optioner som fallback för bakåtkompatibilitet tills helt avvecklat.

## Exempel på modulaktivering
1) Superadmin slår på **Adminprovision**.
2) `KB_Module_Manager` markerar flaggan aktiv och kör `register()` + `boot()` för `admin_commission`.
3) `class-kb-module-admin-commission.php` registrerar sina REST-routes och filtrerar WooCommerce-hookarna.
4) UI visar status “Aktiv” och senaste ändring i loggen.

## Nästa steg (implementationsordning)
1. Lägg till `KB_Module_Manager` och `KB_Feature_Flags` + DB-tabell.
2. Bryt ut befintliga delsystem till moduler (members, materials, qr, admin_commission, stats, campaign_mailer, support_tools).
3. Bygg setup-sidan (admin) med React/Vue-mini eller klassisk WP-lista + toggles.
4. Lägg till health-check/status per modul och loggning av ändringar.
5. Testa flödet: aktivera/deaktivera moduler, kontrollera att hooks/cron/REST bara laddas när aktiv.
