<template>
    <Head title="Palette Colori" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <ColorPalette />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import ColorPalette from '@/components/ColorPalette.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Palette Colori',
        href: '/colors',
    },
];
</script>
