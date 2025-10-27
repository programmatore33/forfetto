<template>
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold text-foreground">Palette Colori Forfetto</h1>
        
        <!-- Colori Principali -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Colori Principali</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-primary text-primary-foreground p-4 rounded-lg">
                    <h3 class="font-semibold">Primario</h3>
                    <p class="text-sm opacity-90">#4CAF50 - Verde Menta</p>
                </div>
                <div class="bg-secondary text-secondary-foreground p-4 rounded-lg">
                    <h3 class="font-semibold">Secondario</h3>
                    <p class="text-sm opacity-90">#FFB300 - Giallo Caldo</p>
                </div>
                <div class="bg-accent text-accent-foreground p-4 rounded-lg">
                    <h3 class="font-semibold">Accent</h3>
                    <p class="text-sm opacity-90">#009688 - Verde Acqua</p>
                </div>
            </div>
        </div>

        <!-- Pulsanti di Esempio -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Pulsanti</h2>
            <div class="flex flex-wrap gap-4">
                <button class="bg-primary text-primary-foreground px-4 py-2 rounded-md hover:opacity-90 transition-opacity">
                    Azione Primaria
                </button>
                <button class="bg-secondary text-secondary-foreground px-4 py-2 rounded-md hover:opacity-90 transition-opacity">
                    Azione Secondaria
                </button>
                <button class="bg-accent text-accent-foreground px-4 py-2 rounded-md hover:opacity-90 transition-opacity">
                    Azione Accent
                </button>
                <button class="border border-primary text-primary px-4 py-2 rounded-md hover:bg-primary hover:text-primary-foreground transition-colors">
                    Outline
                </button>
            </div>
        </div>

        <!-- Indicatori Finanziari -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Indicatori Finanziari</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-card border border-border p-4 rounded-lg">
                    <h3 class="text-sm text-muted-foreground">Entrate Mensili</h3>
                    <p class="text-2xl font-bold text-[var(--color-forfetto-income)]">+€4,250.00</p>
                </div>
                <div class="bg-card border border-border p-4 rounded-lg">
                    <h3 class="text-sm text-muted-foreground">Spese Mensili</h3>
                    <p class="text-2xl font-bold text-[var(--color-forfetto-expense)]">-€1,180.00</p>
                </div>
            </div>
        </div>

        <!-- Cards e Contenuti -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Cards</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-card border border-border p-4 rounded-lg">
                    <h3 class="font-semibold text-card-foreground">Fatture del Mese</h3>
                    <p class="text-sm text-muted-foreground mt-1">15 fatture emesse</p>
                    <div class="mt-3">
                        <span class="text-2xl font-bold text-primary">€3,250</span>
                    </div>
                </div>
                <div class="bg-card border border-accent p-4 rounded-lg">
                    <h3 class="font-semibold text-card-foreground">Clienti Attivi</h3>
                    <p class="text-sm text-muted-foreground mt-1">Questo trimestre</p>
                    <div class="mt-3">
                        <span class="text-2xl font-bold text-accent">12</span>
                    </div>
                </div>
                <div class="bg-card border border-border p-4 rounded-lg">
                    <h3 class="font-semibold text-card-foreground">Margine</h3>
                    <p class="text-sm text-muted-foreground mt-1">Profitto netto</p>
                    <div class="mt-3">
                        <span class="text-2xl font-bold text-secondary">72%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert / Notifiche -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Notifiche</h2>
            <div class="space-y-3">
                <div class="bg-primary/10 border border-primary/20 text-primary p-3 rounded-lg">
                    <strong>Successo:</strong> La fattura è stata salvata correttamente.
                </div>
                <div class="bg-secondary/10 border border-secondary/20 text-secondary-foreground p-3 rounded-lg">
                    <strong>Attenzione:</strong> Il limite forfettario è al 80%.
                </div>
                <div class="bg-accent/10 border border-accent/20 text-accent p-3 rounded-lg">
                    <strong>Info:</strong> Nuova funzionalità disponibile.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
// Componente di test per la palette colori
</script>