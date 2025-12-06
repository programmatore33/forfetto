<template>
  <form @submit.prevent="onSubmit" class="space-y-6">
    <!-- Expense Data -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <Receipt class="h-5 w-5" />
          Dati Spesa
        </CardTitle>
        <CardDescription> Data, categoria e fornitore </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="expense_date">Data Spesa *</Label>
            <Input
              id="expense_date"
              v-model="form.expense_date"
              name="expense_date"
              type="date"
              required
              :class="{ 'border-red-500': errors.expense_date }"
            />
            <InputError :message="errors.expense_date" />
          </div>

          <div class="grid gap-2">
            <Label for="expense_category_id">Categoria</Label>
            <select
              id="expense_category_id"
              v-model="form.expense_category_id"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
              :class="{ 'border-red-500': errors.expense_category_id }"
            >
              <option :value="null">Seleziona categoria (opzionale)</option>
              <option
                v-for="category in expenseCategories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <InputError :message="errors.expense_category_id" />
          </div>
        </div>

        <div class="grid gap-2">
          <Label for="supplier">Fornitore</Label>
          <Input
            id="supplier"
            v-model="form.supplier"
            name="supplier"
            type="text"
            placeholder="Nome del fornitore"
            :class="{ 'border-red-500': errors.supplier }"
          />
          <InputError :message="errors.supplier" />
        </div>
      </CardContent>
    </Card>

    <!-- Amounts -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <Euro class="h-5 w-5" />
          Importi
        </CardTitle>
        <CardDescription> Importo spesa e IVA </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="amount">Importo *</Label>
            <Input
              id="amount"
              v-model="form.amount"
              name="amount"
              type="number"
              step="0.01"
              min="0.01"
              placeholder="0.00"
              required
              :class="{ 'border-red-500': errors.amount }"
            />
            <InputError :message="errors.amount" />
          </div>

          <div class="grid gap-2">
            <Label for="vat_amount">IVA</Label>
            <Input
              id="vat_amount"
              v-model="form.vat_amount"
              name="vat_amount"
              type="number"
              step="0.01"
              min="0"
              placeholder="0.00"
              :class="{ 'border-red-500': errors.vat_amount }"
            />
            <p class="text-xs text-muted-foreground">
              IVA a scopo informativo, non deducibile in forfettario
            </p>
            <InputError :message="errors.vat_amount" />
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <input
            id="is_deductible"
            v-model="form.is_deductible"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300"
          />
          <Label for="is_deductible" class="cursor-pointer">
            Spesa deducibile
          </Label>
        </div>
      </CardContent>
    </Card>

    <!-- Description -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <FileText class="h-5 w-5" />
          Descrizione
        </CardTitle>
        <CardDescription> Descrizione dettagliata della spesa </CardDescription>
      </CardHeader>
      <CardContent>
        <div class="grid gap-2">
          <Label for="description">Descrizione *</Label>
          <textarea
            id="description"
            v-model="form.description"
            name="description"
            placeholder="Descrizione della spesa..."
            rows="4"
            required
            maxlength="500"
            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            :class="{ 'border-red-500': errors.description }"
          />
          <p class="text-right text-xs text-muted-foreground">
            {{ form.description.length }}/500 caratteri
          </p>
          <InputError :message="errors.description" />
        </div>
      </CardContent>
    </Card>

    <!-- Notes -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <StickyNote class="h-5 w-5" />
          Note
        </CardTitle>
        <CardDescription> Note aggiuntive sulla spesa </CardDescription>
      </CardHeader>
      <CardContent>
        <div class="grid gap-2">
          <Label for="notes">Note</Label>
          <textarea
            id="notes"
            v-model="form.notes"
            name="notes"
            placeholder="Note aggiuntive..."
            rows="4"
            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            :class="{ 'border-red-500': errors.notes }"
          />
          <InputError :message="errors.notes" />
        </div>
      </CardContent>
    </Card>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
      <Button type="button" variant="outline" @click="onCancel">
        Annulla
      </Button>
      <Button type="submit" :disabled="processing">
        <Save class="mr-2 h-4 w-4" />
        {{ isEdit ? 'Aggiorna Spesa' : 'Crea Spesa' }}
      </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Euro, FileText, Receipt, Save, StickyNote } from 'lucide-vue-next';
import { computed, reactive, watch } from 'vue';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface ExpenseCategory {
  id: number;
  name: string;
  description: string | null;
  color: string | null;
}

interface Expense {
  id?: number;
  expense_date: string;
  expense_category_id: number | null;
  description: string;
  supplier: string;
  amount: number;
  vat_amount: number;
  is_deductible: boolean;
  notes: string;
}

interface Props {
  expense?: Expense | null;
  expenseCategories: ExpenseCategory[];
  errors?: Record<string, string>;
  processing?: boolean;
}

interface Emits {
  (e: 'submit', form: Expense): void;
  (e: 'cancel'): void;
}

const props = withDefaults(defineProps<Props>(), {
  expense: null,
  errors: () => ({}),
  processing: false,
});

const emit = defineEmits<Emits>();

// Form data
const form = reactive<Expense>({
  expense_date: new Date().toISOString().split('T')[0],
  expense_category_id: null,
  description: '',
  supplier: '',
  amount: 0,
  vat_amount: 0,
  is_deductible: true,
  notes: '',
});

// Computed
const isEdit = computed(() => !!props.expense?.id);

// Watch for form initialization
watch(
  () => props.expense,
  (newExpense) => {
    if (newExpense) {
      Object.assign(form, {
        ...newExpense,
        expense_date: newExpense.expense_date.split('T')[0],
        supplier: newExpense.supplier || '',
        notes: newExpense.notes || '',
      });
    }
  },
  { immediate: true },
);

// Handlers
const onSubmit = () => {
  emit('submit', form);
};

const onCancel = () => {
  emit('cancel');
};
</script>
