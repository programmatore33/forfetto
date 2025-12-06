<template>
  <Head title="Nuovo Cliente" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div>
        <Heading
          title="Nuovo Cliente"
          description="Crea un nuovo cliente inserendo le informazioni necessarie"
        />
      </div>

      <!-- Form -->
      <Form
        action="/customers"
        method="post"
        reset-on-success
        class="max-w-7xl"
        v-slot="{ errors, processing }"
      >
        <CustomerForm
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

import CustomerForm from '@/components/CustomerForm.vue';
import Heading from '@/components/Heading.vue';
import { useRouteHelper } from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Route helper
const routes = useRouteHelper('customers');

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Clienti',
    href: '/customers',
  },
  {
    title: 'Nuovo Cliente',
    href: '/customers/create',
  },
];

// Event handlers
const handleSubmit = (formData: any) => {
  router.post('/customers', formData, {
    onSuccess: () => {
      router.visit(routes.index());
    },
  });
};

const handleCancel = () => {
  router.visit(routes.index());
};
</script>
