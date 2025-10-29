# Configurazione Husky e Lint Pre-commit

## Panoramica

Il progetto utilizza **Husky** e **lint-staged** per eseguire automaticamente controlli di qualità del codice prima di ogni commit. Questo garantisce che tutto il codice commesso rispetti gli standard di formattazione e qualità del progetto.

## Cosa viene controllato

Quando fai un commit, vengono eseguiti automaticamente i seguenti controlli:

### File JavaScript/TypeScript/Vue (_.js, _.ts, \*.vue)

- **ESLint**: Controlla e corregge automaticamente problemi di stile e possibili errori
- **Prettier**: Formatta automaticamente il codice secondo le regole configurate

### File PHP (\*.php)

- **Laravel Pint**: Applica le regole di formattazione PSR-12 e Laravel

## Come funziona

1. Quando esegui `git commit`, Husky intercetta il comando
2. Viene eseguito `lint-staged` che:
    - Identifica solo i file modificati/aggiunti (staged)
    - Applica i tool appropriati (ESLint, Prettier, Pint) solo a quei file
    - Se tutto va bene, il commit procede
    - Se ci sono errori non correggibili automaticamente, il commit viene bloccato

## Configurazione

### Package.json

```json
"lint-staged": {
  "*.{js,ts,vue}": [
    "eslint --fix",
    "prettier --write"
  ],
  "*.php": [
    "vendor/bin/pint"
  ]
}
```

### Husky Hook (.husky/pre-commit)

```bash
#!/usr/bin/env sh
. "$(dirname -- "$0")/_/husky.sh"

# Run lint-staged to check and fix code before commit
npx lint-staged
```

## Comandi utili

### Eseguire il lint manualmente

```bash
# Solo JavaScript/TypeScript/Vue
npm run lint

# Solo PHP
vendor/bin/pint

# Formattazione manuale
npm run format
```

### Saltare temporaneamente il pre-commit (sconsigliato)

```bash
git commit --no-verify -m "messaggio commit"
```

## Risoluzione problemi

### Il commit viene bloccato

1. Controlla l'output dell'errore
2. Correggi manualmente i problemi evidenziati
3. Aggiungi nuovamente i file: `git add .`
4. Riprova il commit

### Aggiornare le dipendenze

```bash
npm install --save-dev husky lint-staged
```

## Benefici

- **Qualità del codice**: Garantisce standard uniformi
- **Automazione**: Riduce la necessità di controlli manuali
- **Collaboration**: Tutti i membri del team seguono le stesse regole
- **CI/CD**: Riduce i fallimenti nelle pipeline di build
