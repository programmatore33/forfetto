<template>
  <Dialog :open="isOpen" @update:open="isOpen = $event">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-3">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100"
          >
            <AlertTriangle class="h-6 w-6 text-red-600" />
          </div>
          <span>
            <slot name="title">{{ title }}</slot>
          </span>
        </DialogTitle>
        <DialogDescription class="text-left">
          <slot name="message">
            <slot name="description">{{ description }}</slot>
          </slot>
        </DialogDescription>
      </DialogHeader>

      <DialogFooter class="gap-2 sm:gap-2">
        <DialogClose as-child>
          <Button variant="outline" @click="onCancel">
            <slot name="cancel-text">{{ props.cancelText }}</slot>
          </Button>
        </DialogClose>
        <Button
          variant="destructive"
          @click="onConfirm"
          :disabled="props.isDeleting"
        >
          <Trash2 v-if="!props.isDeleting" class="mr-2 h-4 w-4" />
          <Loader2 v-else class="mr-2 h-4 w-4 animate-spin" />
          <slot name="confirm-text">
            {{ props.isDeleting ? 'Eliminazione...' : props.confirmText }}
          </slot>
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { AlertTriangle, Loader2, Trash2 } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

/**
 * Props for DeleteConfirmDialog component
 */
interface Props {
  /** Whether the deletion operation is in progress */
  isDeleting?: boolean;
  /** Custom title for the dialog */
  title?: string;
  /** Custom description/message for the dialog */
  description?: string;
  /** Custom text for the confirm button */
  confirmText?: string;
  /** Custom text for the cancel button */
  cancelText?: string;
}

/**
 * Events emitted by DeleteConfirmDialog component
 */
interface Emits {
  /** Emitted when user confirms the deletion */
  (e: 'confirm'): void;
  /** Emitted when user cancels the deletion */
  (e: 'cancel'): void;
}

const props = withDefaults(defineProps<Props>(), {
  isDeleting: false,
  title: 'Conferma Eliminazione',
  description:
    'Sei sicuro di voler eliminare questo elemento? Questa azione non può essere annullata.',
  confirmText: 'Elimina',
  cancelText: 'Annulla',
});

const emit = defineEmits<Emits>();

const isOpen = defineModel<boolean>('isOpen');

const onConfirm = () => {
  emit('confirm');
};

const onCancel = () => {
  isOpen.value = false;
  emit('cancel');
};
</script>
