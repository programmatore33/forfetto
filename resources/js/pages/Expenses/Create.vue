<template>
  <Head title="Nuova Spesa" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div>
        <Heading
          title="Nuova Spesa"
          description="Crea una nuova spesa inserendo le informazioni necessarie"
        />
      </div>

      <!-- Form -->
      <Form
        action="/expenses"
        method="post"
        reset-on-success
        class="max-w-7xl"
        v-slot="{ errors, processing }"
      >
        <ExpenseForm
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

interface Props {
  expenseCategories: ExpenseCategory[];
}

defineProps<Props>();

// Route helper
const routes = useRouteHelper('expenses');

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Spese',
    href: '/expenses',
  },
  {
    title: 'Nuova Spesa',
    href: '/expenses/create',
  },
];

// Event handlers
const handleSubmit = (formData: any) => {
  router.post('/expenses', formData, {
    onSuccess: () => {
      router.visit(routes.index());
    },
  });
};

const handleCancel = () => {
  router.visit(routes.index());
};
</script>
