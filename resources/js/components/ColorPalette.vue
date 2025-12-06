<template>
    <div class="space-y-6 p-6">
        <h1 class="text-2xl font-bold text-foreground">
            Palette Colori Forfetto
        </h1>

        <!-- Colori Principali -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">
                Colori Principali
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-primary p-4 text-primary-foreground">
                    <h3 class="font-semibold">Primario</h3>
                    <p class="text-sm opacity-90">#4CAF50 - Verde Menta</p>
                </div>
                <div
                    class="rounded-lg bg-secondary p-4 text-secondary-foreground"
                >
                    <h3 class="font-semibold">Secondario</h3>
                    <p class="text-sm opacity-90">#FFB300 - Giallo Caldo</p>
                </div>
                <div class="rounded-lg bg-accent p-4 text-accent-foreground">
                    <h3 class="font-semibold">Accent</h3>
                    <p class="text-sm opacity-90">#009688 - Verde Acqua</p>
                </div>
            </div>
        </div>

        <!-- Pulsanti di Esempio -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Pulsanti</h2>
            <div class="flex flex-wrap gap-4">
                <button
                    class="rounded-md bg-primary px-4 py-2 text-primary-foreground transition-opacity hover:opacity-90"
                >
                    Azione Primaria
                </button>
                <button
                    class="rounded-md bg-secondary px-4 py-2 text-secondary-foreground transition-opacity hover:opacity-90"
                >
                    Azione Secondaria
                </button>
                <button
                    class="rounded-md bg-accent px-4 py-2 text-accent-foreground transition-opacity hover:opacity-90"
                >
                    Azione Accent
                </button>
                <button
                    class="rounded-md border border-primary px-4 py-2 text-primary transition-colors hover:bg-primary hover:text-primary-foreground"
                >
                    Outline
                </button>
            </div>
        </div>

        <!-- Indicatori Finanziari -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">
                Indicatori Finanziari
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-border bg-card p-4">
                    <h3 class="text-sm text-muted-foreground">
                        Entrate Mensili
                    </h3>
                    <p
                        class="text-2xl font-bold text-[var(--color-forfetto-income)]"
                    >
                        +€4,250.00
                    </p>
                </div>
                <div class="rounded-lg border border-border bg-card p-4">
                    <h3 class="text-sm text-muted-foreground">Spese Mensili</h3>
                    <p
                        class="text-2xl font-bold text-[var(--color-forfetto-expense)]"
                    >
                        -€1,180.00
                    </p>
                </div>
            </div>
        </div>

        <!-- Cards e Contenuti -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Cards</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border border-border bg-card p-4">
                    <h3 class="font-semibold text-card-foreground">
                        Fatture del Mese
                    </h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        15 fatture emesse
                    </p>
                    <div class="mt-3">
                        <span class="text-2xl font-bold text-primary"
                            >€3,250</span
                        >
                    </div>
                </div>
                <div class="rounded-lg border border-accent bg-card p-4">
                    <h3 class="font-semibold text-card-foreground">
                        Clienti Attivi
                    </h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Questo trimestre
                    </p>
                    <div class="mt-3">
                        <span class="text-2xl font-bold text-accent">12</span>
                    </div>
                </div>
                <div class="rounded-lg border border-border bg-card p-4">
                    <h3 class="font-semibold text-card-foreground">Margine</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Profitto netto
                    </p>
                    <div class="mt-3">
                        <span class="text-2xl font-bold text-secondary"
                            >72%</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert / Notifiche -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-foreground">Notifiche</h2>
            <div class="space-y-3">
                <div
                    class="rounded-lg border border-primary/20 bg-primary/10 p-3 text-primary"
                >
                    <strong>Successo:</strong> La fattura è stata salvata
                    correttamente.
                </div>
                <div
                    class="rounded-lg border border-secondary/20 bg-secondary/10 p-3 text-secondary-foreground"
                >
                    <strong>Attenzione:</strong> Il limite forfettario è al 80%.
                </div>
                <div
                    class="rounded-lg border border-accent/20 bg-accent/10 p-3 text-accent"
                >
                    <strong>Info:</strong> Nuova funzionalità disponibile.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
// Componente di test per la palette colori
</script>
