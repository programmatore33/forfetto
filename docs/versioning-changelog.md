# Sistema di Versioning e Changelog

Questo documento spiega come utilizzare il sistema automatico di versioning e generazione changelog in Forfetto.

## Panoramica

Il progetto utilizza:

- **Conventional Commits**: Standard per i messaggi di commit
- **Commitlint**: Validazione automatica dei messaggi di commit
- **Release-it**: Automazione di versioning, changelog e git tags
- **Semantic Versioning**: Schema di numerazione versioni (MAJOR.MINOR.PATCH)

## Conventional Commits

### Formato

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Tipi di Commit

- `feat`: Nuova funzionalità (incrementa MINOR)
- `fix`: Correzione bug (incrementa PATCH)
- `docs`: Solo documentazione
- `style`: Formattazione codice (non cambia logica)
- `refactor`: Refactoring senza nuove feature o fix
- `perf`: Miglioramento performance
- `test`: Aggiunta o correzione test
- `build`: Modifiche al sistema di build
- `ci`: Modifiche a CI/CD
- `chore`: Altre modifiche (non modifica src o test)
- `revert`: Annulla commit precedente

### Esempi

```bash
# Feature (incrementa versione MINOR: 1.0.0 -> 1.1.0)
git commit -m "feat(invoice): add bulk export to PDF"

# Bug fix (incrementa versione PATCH: 1.1.0 -> 1.1.1)
git commit -m "fix(customer): resolve validation error on VAT number"

# Breaking change (incrementa versione MAJOR: 1.1.1 -> 2.0.0)
git commit -m "feat(api)!: change invoice API response structure

BREAKING CHANGE: Invoice API now returns nested items array"

# Documentazione (non incrementa versione)
git commit -m "docs: update README with installation steps"

# Con scope
git commit -m "refactor(auth): improve 2FA validation logic"
```

## Processo di Release

### 1. Release Automatica

Il comando principale analizza i commit e determina automaticamente il bump:

```bash
npm run release
```

Questo:

1. ✅ Esegue lint e format
2. 📝 Analizza commit dal tag precedente
3. 🔢 Determina bump versione (MAJOR/MINOR/PATCH)
4. 📋 Aggiorna CHANGELOG.md
5. 🏷️ Crea git tag
6. 🚀 Committa e pusha le modifiche

### 2. Release Specifica

Puoi forzare un tipo specifico di bump:

```bash
# Patch: 1.2.3 -> 1.2.4 (bug fixes)
npm run release:patch

# Minor: 1.2.3 -> 1.3.0 (new features)
npm run release:minor

# Major: 1.2.3 -> 2.0.0 (breaking changes)
npm run release:major
```

### 3. Dry Run (Test)

Per vedere cosa succederebbe senza eseguire:

```bash
npm run release:dry
```

## Workflow Tipico

### 1. Durante lo Sviluppo

```bash
# Sviluppa feature
git add .
git commit -m "feat(customer): add customer search functionality"

# Il commit hook valida automaticamente il formato
# Se non è valido, il commit viene rifiutato
```

### 2. Prima della Release

```bash
# Verifica cosa cambierebbe
npm run release:dry

# Esegui release
npm run release

# Segui le istruzioni interattive:
# - Conferma versione
# - Conferma changelog
# - Conferma tag e push
```

### 3. Deploy

Dopo la release, il tag e il CHANGELOG.md sono pronti:

```bash
git push origin develop --tags
```

## CHANGELOG.md

Il file viene aggiornato automaticamente con:

### Sezioni per Tipo

- ✨ **Features**: Nuove funzionalità
- 🐛 **Bug Fixes**: Correzioni bug
- ⚡ **Performance**: Miglioramenti performance
- ♻️ **Refactoring**: Refactoring codice
- 📚 **Documentation**: Documentazione
- 🔨 **Build System**: Modifiche build
- 👷 **CI/CD**: Modifiche CI/CD

### Formato

```markdown
## [1.2.0] - 2025-12-06

### ✨ Features

- **customer**: add bulk export to Excel ([a1b2c3d](link))
- **invoice**: implement recurring invoices ([e4f5g6h](link))

### 🐛 Bug Fixes

- **auth**: fix 2FA validation timeout ([i7j8k9l](link))
```

## Configurazione

### commitlint.config.js

Definisce le regole di validazione dei commit:

- Tipi permessi
- Lunghezza massima subject/body
- Case sensitivity

### .release-it.json

Configura il comportamento di release-it:

- Template commit message
- Nome tag (v${version})
- Formato changelog
- Hook pre-release (lint, format)
- Sezioni e emoji per changelog

## Hooks Husky

### commit-msg

Valida il formato del messaggio di commit usando commitlint:

```bash
npx --no -- commitlint --edit $1
```

Se il commit non rispetta il formato, viene rifiutato con messaggio di errore.

## Best Practices

### 1. Commit Atomici

Ogni commit dovrebbe rappresentare un singolo cambiamento logico:

```bash
# ✅ Buono
git commit -m "feat(invoice): add PDF export"
git commit -m "fix(invoice): resolve currency formatting"

# ❌ Male
git commit -m "feat: add PDF export and fix currency and update docs"
```

### 2. Scope Descrittivi

Usa scope che identificano l'area del codice:

```bash
feat(customer): ...
fix(invoice): ...
docs(readme): ...
refactor(auth): ...
```

### 3. Subject Chiari

- Usa imperativo ("add" non "added")
- Non iniziare con maiuscola
- Non terminare con punto
- Max 100 caratteri

```bash
# ✅ Buono
feat(invoice): add bulk delete functionality

# ❌ Male
feat(Invoice): Added the bulk delete functionality.
```

### 4. Breaking Changes

Usa `!` dopo il tipo e `BREAKING CHANGE:` nel footer:

```bash
git commit -m "feat(api)!: change response structure

BREAKING CHANGE: API responses now use camelCase instead of snake_case"
```

## Troubleshooting

### Commit Rifiutato

Se commitlint rifiuta il commit:

1. Controlla il formato del messaggio
2. Verifica che il tipo sia valido
3. Assicurati che subject non superi 100 caratteri

### Release Fallita

Se release-it fallisce:

1. Verifica che il working directory sia pulito
2. Controlla che ci siano commit da rilasciare
3. Esegui `npm run release:dry` per debug

### Changelog Non Aggiornato

Se il changelog non si aggiorna:

1. Verifica che i commit seguano Conventional Commits
2. Controlla che i commit siano dopo l'ultimo tag
3. I commit `chore` sono nascosti per default

## Riferimenti

- [Conventional Commits](https://www.conventionalcommits.org/)
- [Semantic Versioning](https://semver.org/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Release-it](https://github.com/release-it/release-it)
- [Commitlint](https://commitlint.js.org/)
