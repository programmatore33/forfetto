<template>
  <Head :title="`Fattura ${invoice.invoice_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <Heading
            :title="`Fattura ${invoice.invoice_number}`"
            description="Dettagli della fattura"
          />
          <div class="mt-2 flex items-center gap-4 text-sm">
            <Badge :variant="invoice.is_paid ? 'default' : 'secondary'">
              {{ invoice.is_paid ? 'Pagata' : 'Non pagata' }}
            </Badge>
            <div class="flex items-center gap-2 text-muted-foreground">
              <Calendar class="h-4 w-4" />
              Emessa il {{ formatDate(invoice.issue_date) }}
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" as-child>
            <Link :href="routes.edit(invoice.id)">
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

      <!-- Invoice Details -->
      <div class="grid gap-6 md:grid-cols-2">
        <!-- Invoice Information -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="h-5 w-5" />
              Dati Fattura
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Numero Fattura
              </span>
              <p class="mt-1 font-mono text-sm font-bold">
                {{ invoice.invoice_number }}
              </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <span class="text-sm font-medium text-muted-foreground">
                  Data Emissione
                </span>
                <p class="mt-1 text-sm">{{ formatDate(invoice.issue_date) }}</p>
              </div>
              <div v-if="invoice.payment_date">
                <span class="text-sm font-medium text-muted-foreground">
                  Data Pagamento
                </span>
                <p class="mt-1 text-sm">
                  {{ formatDate(invoice.payment_date) }}
                </p>
              </div>
            </div>

            <div v-if="atecoCode">
              <span class="text-sm font-medium text-muted-foreground">
                Codice ATECO
              </span>
              <p class="mt-1 text-sm">
                {{ atecoCode.code }} - {{ atecoCode.description }}
              </p>
            </div>

            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Descrizione
              </span>
              <p class="mt-1 text-sm whitespace-pre-wrap">
                {{ invoice.description }}
              </p>
            </div>
          </CardContent>
        </Card>

        <!-- Customer Data -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Building class="h-5 w-5" />
              Dati Cliente
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Ragione Sociale
              </span>
              <p class="mt-1 text-sm font-medium">
                {{ invoice.customer_business_name }}
              </p>
              <p
                v-if="invoice.customer_id"
                class="mt-1 text-xs text-muted-foreground"
              >
                <Link
                  :href="customerRoutes.show(invoice.customer_id)"
                  class="hover:underline"
                >
                  Vedi scheda cliente
                </Link>
              </p>
            </div>

            <div
              v-if="invoice.customer_vat_number || invoice.customer_tax_code"
              class="grid grid-cols-2 gap-4"
            >
              <div v-if="invoice.customer_vat_number">
                <span class="text-sm font-medium text-muted-foreground">
                  P.IVA
                </span>
                <p class="mt-1 font-mono text-sm">
                  {{ invoice.customer_vat_number }}
                </p>
              </div>
              <div v-if="invoice.customer_tax_code">
                <span class="text-sm font-medium text-muted-foreground">
                  Codice Fiscale
                </span>
                <p class="mt-1 font-mono text-sm">
                  {{ invoice.customer_tax_code }}
                </p>
              </div>
            </div>

            <div v-if="invoice.customer_email || invoice.customer_phone">
              <div v-if="invoice.customer_email" class="mb-2">
                <span class="text-sm font-medium text-muted-foreground">
                  Email
                </span>
                <p class="mt-1 text-sm">{{ invoice.customer_email }}</p>
              </div>
              <div v-if="invoice.customer_phone">
                <span class="text-sm font-medium text-muted-foreground">
                  Telefono
                </span>
                <p class="mt-1 text-sm">{{ invoice.customer_phone }}</p>
              </div>
            </div>

            <div v-if="hasCustomerAddress">
              <span class="text-sm font-medium text-muted-foreground">
                Indirizzo
              </span>
              <p class="mt-1 text-sm">
                {{ invoice.customer_address }}<br />
                {{ invoice.customer_postal_code }} {{ invoice.customer_city }}
                {{
                  invoice.customer_province
                    ? `(${invoice.customer_province})`
                    : ''
                }}
              </p>
            </div>

            <div v-if="invoice.customer_pec || invoice.customer_sdi_code">
              <div v-if="invoice.customer_pec" class="mb-2">
                <span class="text-sm font-medium text-muted-foreground">
                  PEC
                </span>
                <p class="mt-1 text-sm">{{ invoice.customer_pec }}</p>
              </div>
              <div v-if="invoice.customer_sdi_code">
                <span class="text-sm font-medium text-muted-foreground">
                  Codice SDI
                </span>
                <p class="mt-1 font-mono text-sm">
                  {{ invoice.customer_sdi_code }}
                </p>
              </div>
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
              <p class="mt-1 text-lg font-semibold">
                <Badge
                  variant="outline"
                  class="border-forfetto-income text-forfetto-income"
                >
                  {{ formatCurrency(invoice.amount) }}
                </Badge>
              </p>
            </div>

            <div v-if="invoice.withholding_tax > 0">
              <span class="text-sm font-medium text-muted-foreground">
                Ritenuta d'Acconto (20%)
              </span>
              <p class="mt-1 text-lg font-semibold text-red-600">
                - {{ formatCurrency(invoice.withholding_tax) }}
              </p>
            </div>

            <div class="border-t pt-4">
              <span class="text-sm font-medium text-muted-foreground">
                Importo Netto
              </span>
              <p class="mt-1 text-2xl font-bold">
                {{ formatCurrency(invoice.net_amount) }}
              </p>
            </div>
          </CardContent>
        </Card>

        <!-- Payment -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <CreditCard class="h-5 w-5" />
              Pagamento
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Stato
              </span>
              <p class="mt-1">
                <Badge :variant="invoice.is_paid ? 'default' : 'secondary'">
                  {{ invoice.is_paid ? 'Pagata' : 'Non pagata' }}
                </Badge>
              </p>
            </div>

            <div v-if="invoice.is_paid && invoice.payment_date">
              <span class="text-sm font-medium text-muted-foreground">
                Data Pagamento
              </span>
              <p class="mt-1 text-sm">{{ formatDate(invoice.payment_date) }}</p>
            </div>

            <div v-if="invoice.payment_method">
              <span class="text-sm font-medium text-muted-foreground">
                Metodo
              </span>
              <p class="mt-1">
                <Badge
                  :variant="getPaymentMethodVariant(invoice.payment_method)"
                >
                  {{ getPaymentMethodLabel(invoice.payment_method) }}
                </Badge>
              </p>
            </div>
          </CardContent>
        </Card>

        <!-- Notes -->
        <Card v-if="invoice.notes" class="md:col-span-2">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <StickyNote class="h-5 w-5" />
              Note
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm whitespace-pre-wrap">{{ invoice.notes }}</p>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:isOpen="showDeleteDialog"
      :is-deleting="isDeleting"
      title="Elimina Fattura"
      :description="`Sei sicuro di voler eliminare la fattura ${invoice.invoice_number}?`"
      confirm-text="Elimina Fattura"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    >
      <template #message>
        <p>
          Sei sicuro di voler eliminare la fattura
          <span class="font-semibold">{{ invoice.invoice_number }}</span
          >?
        </p>
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
  Building,
  Calendar,
  CreditCard,
  Edit,
  Euro,
  FileText,
  StickyNote,
  Trash2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useRouteHelper } from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Invoice {
  id: number;
  invoice_number: string;
  issue_date: string;
  payment_date: string | null;
  customer_id: number | null;
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
  withholding_tax: number;
  net_amount: number;
  is_paid: boolean;
  payment_method: string | null;
  notes: string | null;
}

interface AtecoCode {
  id: number;
  code: string;
  description: string;
}

interface Props {
  invoice: Invoice;
  atecoCode?: AtecoCode;
}

const props = defineProps<Props>();

// Route helpers
const routes = useRouteHelper('invoices');
const customerRoutes = useRouteHelper('customers');

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
];

// Delete dialog state
const showDeleteDialog = ref(false);
const isDeleting = ref(false);

// Computed
const hasCustomerAddress = computed(() => {
  return (
    props.invoice.customer_address ||
    props.invoice.customer_city ||
    props.invoice.customer_postal_code
  );
});

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

const getPaymentMethodLabel = (method: string) => {
  const labels: Record<string, string> = {
    bank_transfer: 'Bonifico Bancario',
    cash: 'Contanti',
    check: 'Assegno',
    paypal: 'PayPal',
    other: 'Altro',
  };
  return labels[method] || method;
};

const getPaymentMethodVariant = (
  method: string,
): 'default' | 'secondary' | 'outline' => {
  const variants: Record<string, 'default' | 'secondary' | 'outline'> = {
    bank_transfer: 'default',
    cash: 'secondary',
    check: 'outline',
    paypal: 'default',
    other: 'secondary',
  };
  return variants[method] || 'outline';
};

// Event handlers
const handleDelete = () => {
  showDeleteDialog.value = true;
};

const confirmDelete = () => {
  isDeleting.value = true;
  router.delete(routes.destroy(props.invoice.id), {
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
