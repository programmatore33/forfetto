# Sistema Demo - Forfetto

Il sistema demo di Forfetto permette a più utenti di accedere simultaneamente alla piattaforma con lo stesso account demo, ma con dati completamente isolati che si auto-distruggono dopo 24 ore.

## Architettura

### Database Schema

#### Tabella `users`

- Aggiunto campo `is_demo` (boolean) per identificare gli account demo

#### Tabella `user_sessions`

- `id` - Primary key
- `user_id` - Foreign key verso users
- `session_id` - UUID univoco per la sessione
- `expires_at` - Scadenza della sessione (24 ore)
- `created_at`, `updated_at` - Timestamps

#### Tutte le tabelle dati

Aggiunto campo `session_id` (nullable) a:

- `invoices`
- `customers`
- `expenses`
- `expense_categories`
- `ateco_codes`

### Componenti Software

#### 1. Trait `HasUserScope`

**File:** `app/Traits/HasUserScope.php`

Gestisce automaticamente il filtering e l'assegnazione di `user_id` e `session_id`:

- **Scope globale**: Filtra records per `user_id` e `session_id` (per utenti demo)
- **Auto-assignment**: Assegna automaticamente `user_id` e `session_id` alla creazione
- **Metodi helper**: `withoutUserScope()`, `forUser()`, `forDemoSession()`, `demoOnly()`

#### 2. Middleware `DemoSession`

**File:** `app/Http/Middleware/DemoSession.php`

Responsabilità:

- Verifica se l'utente è demo
- Crea/gestisce sessioni demo
- Popola dati demo per nuove sessioni
- Pulisce sessioni scadute

**Flusso di esecuzione:**

1. Controlla se utente è demo
2. Verifica esistenza sessione valida
3. Se scaduta → cleanup automatico
4. Se non esiste → crea nuova sessione
5. Popola dati demo iniziali

#### 3. Model `UserSession`

**File:** `app/Models/UserSession.php`

Metodi principali:

- `isExpired()` - Verifica se la sessione è scaduta
- `scopeExpired()` - Scope per sessioni scadute
- `scopeActive()` - Scope per sessioni attive

#### 4. Seeder `DemoDataSeeder`

**File:** `database/seeders/DemoDataSeeder.php`

**Metodo principale:** `seedForSession(int $userId, string $sessionId)`

Crea dati demo per una specifica sessione:

- 1 codice ATECO (software development)
- 3-5 clienti
- 5-10 fatture
- 4 categorie spese base
- 5-10 spese

#### 5. Comando `DemoCleanupCommand`

**File:** `app/Console/Commands/DemoCleanupCommand.php`

**Signature:** `demo:cleanup`

Opzioni:

- `--session-id=` - Pulisce una sessione specifica
- `--force` - Salta conferme
- `--dry-run` - Mostra cosa verrebbe cancellato

**Scheduling:**

- Ogni ora (automatico)
- Ogni giorno (backup)

#### 6. User Model Extensions

**File:** `app/Models/User.php`

Nuovi metodi:

- `isDemoUser()` - Verifica se è utente demo
- `getActiveDemoSession()` - Ottiene sessione demo attiva
- `createDemoSession()` - Crea nuova sessione demo
- `userSessions()` - Relazione con sessioni

#### 7. UserFactory Extension

**File:** `database/factories/UserFactory.php`

Nuovo state: `demo()`

- Email: `demo@forfetto.it`
- Password: `demo123`
- Dati fissi per consistenza

## Utilizzo

### Creazione Utente Demo

```php
$demoUser = User::factory()->demo()->create();
```

### Accesso alla Piattaforma Demo

1. Login con `demo@forfetto.it` / `demo123`
2. Il middleware `DemoSession` intercetta la richiesta
3. Crea automaticamente una nuova sessione con UUID
4. Popola dati demo tramite `DemoDataSeeder`
5. L'utente vede i propri dati isolati

### Isolamento Dati

Grazie al trait `HasUserScope`, ogni query automaticamente:

```sql
WHERE user_id = ? AND session_id = ?
```

### Cleanup Automatico

Il sistema pulisce automaticamente:

- **Sessioni scadute** (> 24 ore)
- **Dati associati** (fatture, clienti, spese, ecc.)
- **Record orfani**

### Comandi Manuali

```bash
# Visualizza sessioni che verrebbero cancellate
php artisan demo:cleanup --dry-run

# Forza pulizia di tutte le sessioni scadute
php artisan demo:cleanup --force

# Pulisce una sessione specifica
php artisan demo:cleanup --session-id=uuid --force
```

## Sicurezza

1. **Isolamento completo** - Nessun dato condiviso tra sessioni
2. **Auto-cleanup** - Dati temporanei si auto-distruggono
3. **Validazione rigorosa** - Solo utenti demo possono creare sessioni demo
4. **Rate limiting** - Prevenzione abusi (da implementare se necessario)

## Performance

### Indici Ottimizzati

- `(user_id, session_id)` su tutte le tabelle
- `session_id` per cleanup rapido
- `expires_at` per identificazione sessioni scadute

### Considerazioni

- Query leggermente più complesse (due WHERE condition)
- Cleanup regolare previene accumulo dati
- Possibile monitoring del numero di sessioni attive

## Monitoraggio

### Metriche Utili

- Numero sessioni demo attive
- Frequenza creazione nuove sessioni
- Tempo medio permanenza utenti demo
- Volume dati generati per sessione

### Log Events

Il sistema logga:

- Creazione nuove sessioni demo
- Cleanup sessioni scadute
- Errori durante popolazione dati

## Estensioni Future

1. **Rate Limiting** per creazione sessioni
2. **Analytics** su utilizzo demo
3. **Template diversi** di dati demo
4. **Durata sessioni configurabile**
5. **Cleanup basato su memoria/storage**

## Testing

Il sistema include test per:

- Creazione e gestione sessioni
- Isolamento dati tra sessioni
- Cleanup automatico
- Middleware functionality
- Trait behavior

Per eseguire i test specifici del sistema demo:

```bash
php artisan test --filter=Demo
```
