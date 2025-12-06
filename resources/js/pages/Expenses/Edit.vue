<template>
  <Head title="Modifica Spesa" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div>
        <Heading
          title="Modifica Spesa"
          description="Aggiorna le informazioni della spesa"
        />
      </div>

      <!-- Form -->
      <Form
        :action="`/expenses/${expense.id}`"
        method="put"
        class="max-w-7xl"
        v-slot="{ errors, processing }"
      >
        <ExpenseForm
          :expense="expenseForForm"
          :expense-categories="expenseCategories"
          :errors="errors"
          :processing="processing"
          @submit="handleSubmit"
          @cancel="handleCancel"
        />
      </Form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

import ExpenseForm from '@/components/ExpenseForm.vue';
import Heading from '@/components/Heading.vue';
import { useRouteHelper } from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface ExpenseCategory {
  id: number;
  name: string;
  description: string | null;
  color: string | null;
}

interface Expense {
  id: number;
  expense_date: string;
  expense_category_id: number | null;
  description: string;
  supplier: string | null;
  amount: number;
  notes: string | null;
}

interface Props {
  expense: Expense;
  expenseCategories: ExpenseCategory[];
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
    title: 'Spesa',
    href: `/expenses/${props.expense.id}`,
  },
  {
    title: 'Modifica',
    href: `/expenses/${props.expense.id}/edit`,
  },
];

// Prepare expense data for form
const expenseForForm = computed(() => ({
  ...props.expense,
  expense_date: props.expense.expense_date.split('T')[0],
  supplier: props.expense.supplier || '',
  notes: props.expense.notes || '',
}));

// Event handlers
const handleSubmit = (formData: any) => {
  router.put(`/expenses/${props.expense.id}`, formData, {
    onSuccess: () => {
      router.visit(routes.show(props.expense.id));
    },
  });
};

const handleCancel = () => {
  router.visit(routes.show(props.expense.id));
};
</script>
