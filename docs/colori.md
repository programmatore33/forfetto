# Palette Colori Forfetto

La palette colori di Forfetto è stata progettata per trasmettere professionalità, successo e chiarezza nell'interfaccia della gestione forfettaria.

## 🎨 Colori Principali

### Primario: Verde Menta `#4CAF50`

- **Utilizzo**: Pulsanti principali, elementi di successo, crescita del "netto"
- **Significato**: Successo, crescita, positività
- **Classi CSS**: `bg-primary`, `text-primary`, `border-primary`
- **Variabile CSS**: `var(--color-forfetto-primary)`

### Secondario: Giallo Caldo `#FFB300`

- **Utilizzo**: Elementi di attenzione, badge, elementi energici
- **Significato**: Energia, ottimismo, guadagno
- **Classi CSS**: `bg-secondary`, `text-secondary`, `border-secondary`
- **Variabile CSS**: `var(--color-forfetto-secondary)`

### Accent: Grigio Freddo `#CFD8DC`

- **Utilizzo**: Pulsanti secondari, link, dettagli
- **Significato**: Modernità, minimalismo, eleganza discreta
- **Classi CSS**: `bg-accent`, `text-accent`, `border-accent`
- **Variabile CSS**: `var(--color-forfetto-accent)`
- **⚠️ Importante**: Essendo un colore chiaro, richiede testo scuro (`#2E2E2E`) per la leggibilità

## 🎯 Colori Funzionali

### Background: Grigio Chiarissimo `#F9FAFB`

- **Utilizzo**: Sfondo principale dell'applicazione
- **Significato**: Pulizia, modernità
- **Classi CSS**: `bg-background`
- **Variabile CSS**: `var(--color-forfetto-background)`

### Testo Principale: Grigio Scuro `#2E2E2E`

- **Utilizzo**: Testo principale, contenuti
- **Significato**: Leggibilità, sobrietà
- **Classi CSS**: `text-foreground`
- **Variabile CSS**: `var(--color-forfetto-text)`

## 💰 Colori Contestuali

### Entrate: Verde `#4CAF50`

- **Utilizzo**: Indicatori di fatturato, entrate, ricavi
- **Classi CSS**: `text-forfetto-income`

### Spese: Rosso `#FF5722`

- **Utilizzo**: Indicatori di spese, uscite
- **Classi CSS**: `text-forfetto-expense`

### Avvisi: Giallo `#FFB300`

- **Utilizzo**: Notifiche, avvisi non critici
- **Classi CSS**: `text-forfetto-warning`

## 📊 Colori per Grafici

I colori per i grafici seguono la palette principale:

- **Chart 1**: Verde menta `hsl(122 39% 49%)`
- **Chart 2**: Grigio freddo `hsl(200 15% 86%)`
- **Chart 3**: Giallo caldo `hsl(42 100% 50%)`
- **Chart 4**: Giallo scuro `hsl(42 100% 35%)`
- **Chart 5**: Verde scuro `hsl(122 39% 35%)`

## 🌙 Modalità Scura

La modalità scura mantiene la stessa palette con luminosità adattate:

- I colori primari rimangono invariati per mantenere l'identità
- Gli sfondi diventano scuri con tonalità grigio-blu
- I testi si adattano per garantire il contrasto

## 💻 Utilizzo nel Codice

### Tailwind CSS

```html
<!-- Pulsante primario -->
<button class="bg-primary text-primary-foreground">Salva</button>

<!-- Pulsante accent con testo scuro -->
<button class="bg-accent text-accent-foreground">Secondario</button>

<!-- Card con accent -->
<div class="border-accent bg-card">Content</div>

<!-- Testo entrate/spese -->
<span class="text-forfetto-income">+€1,250.00</span>
<span class="text-forfetto-expense">-€350.00</span>
```

### CSS Custom Properties

```css
.custom-element {
  background: var(--color-forfetto-primary);
  color: var(--color-forfetto-text);
  border: 1px solid var(--color-forfetto-accent);
}
```

## 🎨 Accessibility

Tutti i colori rispettano gli standard WCAG 2.1 per il contrasto:

- Rapporto di contrasto minimo 4.5:1 per testo normale
- Rapporto di contrasto minimo 3:1 per testo grande
- I colori funzionali (successo, errore, avviso) sono distinguibili anche per utenti con daltonismo

## ✨ Aggiornamento Novembre 2025

**Cambio del colore Accent**: Evoluzione da Verde Acqua `#009688` → Blu Acciaio `#2563EB` → Viola Moderno `#7C3AED` → Grigio Grafite `#37474F` → **Grigio Freddo `#CFD8DC`** per:

- Modernità e minimalismo
- Colore neutro e delicato che non disturba
- Ottima integrazione con verde e giallo
- Eleganza discreta per interfacce pulite

## 🚀 Best Practices

1. **Consistenza**: Utilizza sempre i colori della palette per mantenere coerenza
2. **Gerarchia**: Il verde primario per azioni principali, l'accent per secondarie
3. **Significato**: Utilizza i colori contestuali (verde per entrate, rosso per spese)
4. **Contrasto**: Verifica sempre la leggibilità del testo sui diversi sfondi
5. **Test**: Verifica l'interfaccia sia in modalità chiara che scura
6. **⚠️ Accent Color**: Il Grigio Freddo richiede sempre testo scuro (`text-accent-foreground`) quando usato come sfondo
