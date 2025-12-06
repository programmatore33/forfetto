<template>
  <Head title="Modifica Fattura" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div>
        <Heading
          :title="`Modifica Fattura ${invoice.invoice_number}`"
          description="Aggiorna le informazioni della fattura"
        />
      </div>

      <!-- Form -->
      <Form
        :action="`/invoices/${invoice.id}`"
        method="put"
        class="max-w-4xl"
        v-slot="{ errors, processing }"
      >
        <InvoiceForm
          :invoice="invoiceForForm"
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
import { computed } from 'vue';

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

interface Invoice {
  id: number;
  invoice_number: string;
  issue_date: string;
  customer_id: number | null;
  ateco_code_id: number;
  customer_business_name: string;
  customer_email: string | null;
  customer_vat_number: string | null;
  customer_tax_code: string | null;
  customer_address: string | null;
  customer_city: string | null;
  customer_province: string | null;
  customer_postal_code: string | null;
  customer_phone: string | null;
  customer_pec: string | null;
  customer_sdi_code: string | null;
  description: string;
  amount: number;
  contributo_integrativo_applied: boolean;
  contributo_integrativo_amount: number;
  net_amount: number;
  is_paid: boolean;
  payment_date: string | null;
  payment_method: string | null;
  notes: string | null;
}

interface Props {
  invoice: Invoice;
  customers: Customer[];
  atecoCodes: AtecoCode[];
}

const props = defineProps<Props>();

// Route helper
const routes = useRouteHelper('invoices');

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Fatture',
    href: '/invoices',
  },
  {
    title: props.invoice.invoice_number,
    href: `/invoices/${props.invoice.id}`,
  },
  {
    title: 'Modifica',
    href: `/invoices/${props.invoice.id}/edit`,
  },
];

// Prepare invoice data for form
const invoiceForForm = computed(() => ({
  ...props.invoice,
  issue_date: props.invoice.issue_date.split('T')[0],
  customer_email: props.invoice.customer_email || '',
  customer_vat_number: props.invoice.customer_vat_number || '',
  customer_tax_code: props.invoice.customer_tax_code || '',
  customer_address: props.invoice.customer_address || '',
  customer_city: props.invoice.customer_city || '',
  customer_province: props.invoice.customer_province || '',
  customer_postal_code: props.invoice.customer_postal_code || '',
  customer_phone: props.invoice.customer_phone || '',
  customer_pec: props.invoice.customer_pec || '',
  customer_sdi_code: props.invoice.customer_sdi_code || '',
  payment_date: props.invoice.payment_date?.split('T')[0] || '',
  payment_method: props.invoice.payment_method || '',
  notes: props.invoice.notes || '',
}));

// Event handlers
const handleSubmit = (formData: any) => {
  router.put(`/invoices/${props.invoice.id}`, formData, {
    onSuccess: () => {
      router.visit(routes.show(props.invoice.id));
    },
  });
};

const handleCancel = () => {
  router.visit(routes.show(props.invoice.id));
};
</script>
