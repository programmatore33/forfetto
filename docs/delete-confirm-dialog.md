# DeleteConfirmDialog Component

Componente riutilizzabile per confermare operazioni di eliminazione in modo consistente e professionale.

## Caratteristiche

- 🎨 Design consistente con shadcn/ui
- 🔧 Completamente personalizzabile tramite props e slot
- ⚡ Gestione stato loading automatica
- ♿ Accessibile per screen readers
- 🌍 Testi personalizzabili per internazionalizzazione

## Props

| Prop          | Tipo      | Default                              | Descrizione                               |
| ------------- | --------- | ------------------------------------ | ----------------------------------------- |
| `isDeleting`  | `boolean` | `false`                              | Mostra stato loading durante eliminazione |
| `title`       | `string`  | `"Conferma Eliminazione"`            | Titolo della dialog                       |
| `description` | `string`  | `"Sei sicuro di voler eliminare..."` | Messaggio di default                      |
| `confirmText` | `string`  | `"Elimina"`                          | Testo bottone conferma                    |
| `cancelText`  | `string`  | `"Annulla"`                          | Testo bottone annulla                     |

## Slot

| Slot           | Descrizione                               |
| -------------- | ----------------------------------------- |
| `title`        | Personalizza completamente il titolo      |
| `message`      | Personalizza completamente il contenuto   |
| `description`  | Alternativa a `message` per compatibilità |
| `confirm-text` | Personalizza testo bottone conferma       |
| `cancel-text`  | Personalizza testo bottone annulla        |

## Eventi

| Evento    | Descrizione                                    |
| --------- | ---------------------------------------------- |
| `confirm` | Emesso quando l'utente conferma l'eliminazione |
| `cancel`  | Emesso quando l'utente annulla                 |

## Esempi di Utilizzo

### Uso Base

```vue
<DeleteConfirmDialog
  v-model:isOpen="showDialog"
  @confirm="handleDelete"
  @cancel="handleCancel"
/>
```

### Personalizzazione con Props

```vue
<DeleteConfirmDialog
  v-model:isOpen="showDialog"
  :is-deleting="isDeleting"
  title="Elimina Cliente"
  confirm-text="Elimina Cliente"
  @confirm="handleDelete"
/>
```

### Personalizzazione con Slot

```vue
<DeleteConfirmDialog v-model:isOpen="showDialog" @confirm="handleDelete">
  <template #title>
    <span>Elimina {{ customer.name }}</span>
  </template>
  
  <template #message>
    <p>Stai per eliminare <strong>{{ customer.name }}</strong></p>
    <p class="text-sm text-muted-foreground mt-2">
      Questa azione eliminerà anche:
    </p>
    <ul class="text-sm text-muted-foreground list-disc ml-4">
      <li>{{ customer.invoicesCount }} fatture</li>
      <li>Tutti i dati associati</li>
    </ul>
  </template>
</DeleteConfirmDialog>
```

### Per Altre Entità

```vue
<!-- Elimina Fattura -->
<DeleteConfirmDialog
  v-model:isOpen="showDeleteInvoice"
  title="Elimina Fattura"
  :description="`Elimina fattura #${invoice.number}?`"
  confirm-text="Elimina Fattura"
  @confirm="deleteInvoice"
/>

<!-- Elimina Spesa -->
<DeleteConfirmDialog
  v-model:isOpen="showDeleteExpense"
  title="Elimina Spesa"
  confirm-text="Elimina Spesa"
  @confirm="deleteExpense"
>
  <template #message>
    <p>Elimina la spesa di <strong>€{{ expense.amount }}</strong>?</p>
  </template>
</DeleteConfirmDialog>
```

## Pattern di Implementazione

```vue
<script setup>
// State
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const itemToDelete = ref(null);

// Handlers
const handleDelete = (item) => {
  itemToDelete.value = item;
  showDeleteDialog.value = true;
};

const confirmDelete = async () => {
  isDeleting.value = true;
  try {
    await router.delete(`/api/items/${itemToDelete.value.id}`);
    // Success handling
  } finally {
    isDeleting.value = false;
    showDeleteDialog.value = false;
    itemToDelete.value = null;
  }
};
</script>
```
