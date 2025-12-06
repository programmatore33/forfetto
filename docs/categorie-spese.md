# Categorie Spese

## Architettura

Il sistema delle categorie spese è stato progettato per fornire flessibilità e facilità d'uso agli utenti.

### Categorie Globali (Default)

- **Cosa sono**: Categorie predefinite disponibili per tutti gli utenti
- **Caratteristiche**: 
  - `user_id` è `null`
  - Create automaticamente tramite seeder
  - Non possono essere modificate dagli utenti
  - Forniscono un set base di categorie comuni

### Categorie Personalizzate

- **Cosa sono**: Categorie create dagli utenti per le proprie esigenze specifiche
- **Caratteristiche**:
  - `user_id` corrisponde all'ID dell'utente che le ha create
  - Completamente personalizzabili
  - Visibili solo al proprietario

## Scope del Model

Il model `ExpenseCategory` fornisce diversi scope per gestire le categorie:

### `global()`
Restituisce solo le categorie globali (default)
```php
ExpenseCategory::global()->get()
```

### `forUser($userId)`
Restituisce solo le categorie personalizzate di un utente specifico
```php
ExpenseCategory::forUser($userId)->get()
```

### `availableForUser($userId)`
Restituisce tutte le categorie disponibili per un utente (globali + personalizzate)
```php
ExpenseCategory::availableForUser($userId)->get()
```

## Metodi Helper

### `isGlobal()`
Verifica se una categoria è globale
```php
if ($category->isGlobal()) {
    // È una categoria globale
}
```

## Seeder

Le categorie globali vengono create tramite `ExpenseCategorySeeder`:

```bash
php artisan db:seed --class=ExpenseCategorySeeder
```

Le categorie predefinite includono:
- Software
- Hardware  
- Trasporti
- Formazione
- Materiale Ufficio
- Marketing
- Consulenze
- Utenze
- Affitto
- Assicurazioni
- Tasse e Tributi
- Altro

## Utilizzo Consigliato

1. **Per ottenere tutte le categorie di un utente**:
   ```php
   $categories = ExpenseCategory::availableForUser(auth()->id())->get();
   ```

2. **Per creare una categoria personalizzata**:
   ```php
   ExpenseCategory::create([
       'user_id' => auth()->id(),
       'name' => 'Categoria Personalizzata',
       'description' => 'Descrizione...',
       'color' => '#FF5722'
   ]);
   ```

3. **Per verificare se può essere modificata**:
   ```php
   if (!$category->isGlobal()) {
       // L'utente può modificare questa categoria
   }
   ```