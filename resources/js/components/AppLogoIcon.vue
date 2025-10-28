<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div class="w-10 h-7">
    <img 
        src="/logo.svg" 
        alt="Forfetto Logo"
        :class="className"
        v-bind="$attrs"
    />
    </div>
</template>
