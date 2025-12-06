<template>
  <Head title="Spese" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <Heading title="Spese" description="Gestisci le tue spese" />
        <Button as-child>
          <Link :href="routes.create()">
            <Plus class="mr-2 h-4 w-4" />
            Nuova Spesa
          </Link>
        </Button>
      </div>

      <!-- Data Table -->
      <DataTableWithPagination
        :data="expenses.data"
        :meta="{
          current_page: expenses.current_page,
          last_page: expenses.last_page,
          per_page: expenses.per_page,
          total: expenses.total,
          from: expenses.from,
          to: expenses.to,
        }"
        :filters="filters"
        :columns="config.columns"
        :title="config.title"
        :entity-name="config.entityName"
        :entity-plural="config.entityPlural"
        :search-placeholder="config.searchPlaceholder"
        :empty-title="config.emptyTitle"
        :empty-description="config.emptyDescription"
        :empty-icon="config.emptyIcon"
        :create-button-text="config.createButtonText"
        :delete-confirm-message="config.deleteConfirmMessage"
        :route-prefix="config.routePrefix"
        :create-route="routes.create()"
        :actions="config.actions"
        @edit="handleEdit"
        @delete="handleDelete"
        @row-click="handleRowClick"
      >
        <!-- Custom column for category -->
        <template #column-expense_category="{ data }">
          <Badge
            v-if="data.expenseCategory"
            :style="{
              backgroundColor: data.expenseCategory.color || '#6b7280',
            }"
          >
            {{ data.expenseCategory.name }}
          </Badge>
          <span v-else class="text-sm text-muted-foreground"
            >Senza categoria</span
          >
        </template>

        <!-- Custom column for description -->
        <template #column-description="{ data }">
          <div class="max-w-xs truncate" :title="data.description">
            {{ data.description }}
          </div>
        </template>

        <!-- Custom column for amount -->
        <template #column-amount="{ data }">
          <Badge
            variant="outline"
            class="border-forfetto-expense font-mono text-forfetto-expense"
          >
            {{ formatCurrency(data.amount) }}
          </Badge>
        </template>
      </DataTableWithPagination>
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
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';
import Heading from '@/components/Heading.vue';
import DataTableWithPagination from '@/components/tables/DataTableWithPagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  useExpenseTableConfig,
  useRouteHelper,
} from '@/composables/useTableConfigs';
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
  description: string;
  supplier: string | null;
  amount: number;
  expenseCategory: ExpenseCategory | null;
}

interface Props {
  expenses: {
    data: Expense[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
  };
  filters: {
    search: string | null;
    per_page: number;
    sort_field: string;
    sort_direction: 'asc' | 'desc';
  };
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Spese',
    href: '/expenses',
  },
];

// Table configuration
const config = useExpenseTableConfig();
const routes = useRouteHelper('expenses');

// Delete dialog state
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const expenseToDelete = ref<Expense | null>(null);

// Helper functions
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};

// Event handlers
const handleEdit = (id: number) => {
  router.visit(routes.edit(id));
};

const handleDelete = (id: number) => {
  const expense = props.expenses.data.find((e: Expense) => e.id === id);
  if (expense) {
    expenseToDelete.value = expense;
    showDeleteDialog.value = true;
  }
};

const confirmDelete = () => {
  if (expenseToDelete.value) {
    isDeleting.value = true;
    router.delete(routes.destroy(expenseToDelete.value.id), {
      preserveScroll: true,
      onFinish: () => {
        isDeleting.value = false;
        showDeleteDialog.value = false;
        expenseToDelete.value = null;
      },
    });
  }
};

const cancelDelete = () => {
  expenseToDelete.value = null;
};

const handleRowClick = (data: any) => {
  router.visit(routes.show(data.id));
};
</script>
