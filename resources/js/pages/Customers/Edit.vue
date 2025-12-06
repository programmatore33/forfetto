<template>
  <Head :title="`Modifica ${customer.business_name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div>
        <Heading
          :title="`Modifica ${customer.business_name}`"
          description="Aggiorna le informazioni del cliente"
        />
      </div>

      <!-- Form -->
      <Form
        :action="`/customers/${customer.id}`"
        method="put"
        reset-on-success
        class="max-w-7xl"
        v-slot="{ errors, processing }"
      >
        <CustomerForm
          :customer="customerForForm"
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

import CustomerForm from '@/components/CustomerForm.vue';
import Heading from '@/components/Heading.vue';
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
  postal_code: string | null;
  province: string | null;
  pec: string | null;
  sdi_code: string | null;
  notes: string | null;
  created_at: string;
  updated_at: string;
}

interface Props {
  customer: Customer;
}

const props = defineProps<Props>();

// Route helper
const routes = useRouteHelper('customers');

// Convert customer data to form format
const customerForForm = computed(() => ({
  id: props.customer.id,
  business_name: props.customer.business_name,
  vat_number: props.customer.vat_number || '',
  tax_code: props.customer.tax_code || '',
  email: props.customer.email || '',
  phone: props.customer.phone || '',
  address: props.customer.address || '',
  city: props.customer.city || '',
  postal_code: props.customer.postal_code || '',
  province: props.customer.province || '',
  pec: props.customer.pec || '',
  sdi_code: props.customer.sdi_code || '',
  notes: props.customer.notes || '',
}));

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Clienti',
    href: '/customers',
  },
  {
    title: props.customer.business_name,
    href: `/customers/${props.customer.id}`,
  },
  {
    title: 'Modifica',
    href: `/customers/${props.customer.id}/edit`,
  },
];

// Event handlers
const handleSubmit = (formData: any) => {
  router.put(`/customers/${props.customer.id}`, formData, {
    onSuccess: () => {
      router.visit(routes.show(props.customer.id));
    },
  });
};

const handleCancel = () => {
  router.visit(routes.show(props.customer.id));
};
</script>
