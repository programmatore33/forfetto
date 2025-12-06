<template>
  <Head title="Nuova Fattura" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div>
        <Heading
          title="Nuova Fattura"
          description="Crea una nuova fattura inserendo le informazioni necessarie"
        />
      </div>

      <!-- Form -->
      <Form
        action="/invoices"
        method="post"
        reset-on-success
        class="max-w-4xl"
        v-slot="{ errors, processing }"
      >
        <InvoiceForm
          :customers="customers"
          :ateco-codes="atecoCodes"
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

import Heading from '@/components/Heading.vue';
import InvoiceForm from '@/components/InvoiceForm.vue';
import { useRouteHelper } from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Customer {
  id: number;
  business_name: string;
  vat_number: string | null;
  tax_code: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  city: string | null;
  province: string | null;
  postal_code: string | null;
  pec: string | null;
  sdi_code: string | null;
}

interface AtecoCode {
  id: number;
  code: string;
  description: string;
  is_primary: boolean;
}

interface Props {
  customers: Customer[];
  atecoCodes: AtecoCode[];
}

defineProps<Props>();

// Route helper
const routes = useRouteHelper('invoices');

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fatture',
    href: '/invoices',
  },
  {
    title: 'Nuova Fattura',
    href: '/invoices/create',
  },
];

// Event handlers
const handleSubmit = (formData: any) => {
  router.post('/invoices', formData, {
    onSuccess: () => {
      router.visit(routes.index());
    },
  });
};

const handleCancel = () => {
  router.visit(routes.index());
};
</script>
