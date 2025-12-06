<template>
  <Head title="Dettaglio Spesa" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <Heading
            title="Dettaglio Spesa"
            description="Informazioni sulla spesa"
          />
          <div class="mt-2 flex items-center gap-4 text-sm">
            <Badge :variant="expense.is_deductible ? 'default' : 'secondary'">
              {{ expense.is_deductible ? 'Deducibile' : 'Non deducibile' }}
            </Badge>
            <div class="flex items-center gap-2 text-muted-foreground">
              <Calendar class="h-4 w-4" />
              {{ formatDate(expense.expense_date) }}
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" as-child>
            <Link :href="routes.edit(expense.id)">
              <Edit class="mr-2 h-4 w-4" />
              Modifica
            </Link>
          </Button>
          <Button variant="destructive" @click="handleDelete">
            <Trash2 class="mr-2 h-4 w-4" />
            Elimina
          </Button>
        </div>
      </div>

      <!-- Expense Details -->
      <div class="grid gap-6 md:grid-cols-2">
        <!-- Expense Information -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Receipt class="h-5 w-5" />
              Dati Spesa
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Data
              </span>
              <p class="mt-1 text-sm">{{ formatDate(expense.expense_date) }}</p>
            </div>

            <div v-if="expense.expenseCategory">
              <span class="text-sm font-medium text-muted-foreground">
                Categoria
              </span>
              <p class="mt-1">
                <Badge
                  :style="{
                    backgroundColor: expense.expenseCategory.color || '#6b7280',
                  }"
                >
                  {{ expense.expenseCategory.name }}
                </Badge>
              </p>
            </div>

            <div v-if="expense.supplier">
              <span class="text-sm font-medium text-muted-foreground">
                Fornitore
              </span>
              <p class="mt-1 text-sm">{{ expense.supplier }}</p>
            </div>

            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Descrizione
              </span>
              <p class="mt-1 text-sm whitespace-pre-wrap">
                {{ expense.description }}
              </p>
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
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Importo
              </span>
              <p class="mt-1 text-2xl font-bold">
                <Badge
                  variant="outline"
                  class="border-forfetto-expense text-forfetto-expense"
                >
                  {{ formatCurrency(expense.amount) }}
                </Badge>
              </p>
            </div>

            <div v-if="expense.vat_amount > 0">
              <span class="text-sm font-medium text-muted-foreground">
                IVA (informativa)
              </span>
              <p class="mt-1 text-lg font-semibold">
                {{ formatCurrency(expense.vat_amount) }}
              </p>
              <p class="mt-1 text-xs text-muted-foreground">
                Non deducibile in regime forfettario
              </p>
            </div>

            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Deducibilità
              </span>
              <p class="mt-1">
                <Badge
                  :variant="expense.is_deductible ? 'default' : 'secondary'"
                >
                  {{ expense.is_deductible ? 'Deducibile' : 'Non deducibile' }}
                </Badge>
              </p>
            </div>
          </CardContent>
        </Card>

        <!-- Notes -->
        <Card v-if="expense.notes" class="md:col-span-2">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <StickyNote class="h-5 w-5" />
              Note
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm whitespace-pre-wrap">{{ expense.notes }}</p>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:isOpen="showDeleteDialog"
      :is-deleting="isDeleting"
      title="Elimina Spesa"
      description="Sei sicuro di voler eliminare questa spesa?"
      confirm-text="Elimina Spesa"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    >
      <template #message>
        <p>Sei sicuro di voler eliminare questa spesa?</p>
        <p class="mt-2 text-sm text-muted-foreground">
          Questa azione non può essere annullata.
        </p>
      </template>
    </DeleteConfirmDialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
  Calendar,
  Edit,
  Euro,
  Receipt,
  StickyNote,
  Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useRouteHelper } from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface ExpenseCategory {
  id: number;
  name: string;
  color: string | null;
}

interface Expense {
  id: number;
  expense_date: string;
  expense_category_id: number | null;
  description: string;
  supplier: string | null;
  amount: number;
  vat_amount: number;
  is_deductible: boolean;
  notes: string | null;
  expenseCategory: ExpenseCategory | null;
}

interface Props {
  expense: Expense;
}

const props = defineProps<Props>();

// Route helper
const routes = useRouteHelper('expenses');

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Spese',
    href: '/expenses',
  },
  {
    title: 'Dettaglio',
    href: `/expenses/${props.expense.id}`,
  },
];

// Delete dialog state
const showDeleteDialog = ref(false);
const isDeleting = ref(false);

// Helper functions
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};

// Event handlers
const handleDelete = () => {
  showDeleteDialog.value = true;
};

const confirmDelete = () => {
  isDeleting.value = true;
  router.delete(routes.destroy(props.expense.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(routes.index());
    },
    onFinish: () => {
      isDeleting.value = false;
      showDeleteDialog.value = false;
    },
  });
};

const cancelDelete = () => {
  showDeleteDialog.value = false;
};
</script>
