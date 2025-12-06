<template>
  <Head title="Fatture" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <Heading
          title="Fatture"
          description="Gestisci le tue fatture attive e passive"
        />
        <Button as-child>
          <Link :href="routes.create()">
            <Plus class="mr-2 h-4 w-4" />
            Nuova Fattura
          </Link>
        </Button>
      </div>

      <!-- Data Table -->
      <DataTableWithPagination
        :data="invoices.data"
        :meta="{
          current_page: invoices.current_page,
          last_page: invoices.last_page,
          per_page: invoices.per_page,
          total: invoices.total,
          from: invoices.from,
          to: invoices.to,
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
        <!-- Custom column for invoice number -->
        <template #column-invoice_number="{ data }">
          <div class="font-mono font-medium">{{ data.invoice_number }}</div>
        </template>

        <!-- Custom column for customer -->
        <template #column-customer_business_name="{ data }">
          <div>
            <div class="font-medium">{{ data.customer_business_name }}</div>
            <div v-if="data.customer_id" class="text-xs text-muted-foreground">
              <Link
                :href="customerRoutes.show(data.customer_id)"
                class="hover:underline"
                @click.stop
              >
                Vedi scheda cliente
              </Link>
            </div>
          </div>
        </template>

        <!-- Custom column for amount -->
        <template #column-amount="{ data }">
          <Badge
            variant="outline"
            class="border-forfetto-income font-mono text-forfetto-income"
          >
            {{ formatCurrency(data.amount) }}
          </Badge>
        </template>

        <!-- Custom column for payment method -->
        <template #column-payment_method="{ data }">
          <Badge
            v-if="data.payment_method"
            :variant="getPaymentMethodVariant(data.payment_method)"
          >
            {{ getPaymentMethodLabel(data.payment_method) }}
          </Badge>
          <span v-else class="text-sm text-muted-foreground">-</span>
        </template>

        <!-- Custom column for payment status -->
        <template #column-is_paid="{ data }">
          <Badge :variant="data.is_paid ? 'default' : 'secondary'">
            {{ data.is_paid ? 'Pagata' : 'Non pagata' }}
          </Badge>
        </template>
      </DataTableWithPagination>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:isOpen="showDeleteDialog"
      :is-deleting="isDeleting"
      title="Elimina Fattura"
      :description="`Sei sicuro di voler eliminare la fattura ${invoiceToDelete?.invoice_number}?`"
      confirm-text="Elimina Fattura"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    >
      <template #message>
        <p>
          Sei sicuro di voler eliminare la fattura
          <span class="font-semibold">{{
            invoiceToDelete?.invoice_number
          }}</span
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
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';
import Heading from '@/components/Heading.vue';
import DataTableWithPagination from '@/components/tables/DataTableWithPagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  useInvoiceTableConfig,
  useRouteHelper,
} from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Invoice {
  id: number;
  invoice_number: string;
  customer_id: number | null;
  customer_business_name: string;
  issue_date: string;
  amount: number;
  payment_method: string | null;
  is_paid: boolean;
}

interface Props {
  invoices: {
    data: Invoice[];
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
    title: 'Fatture',
    href: '/invoices',
  },
];

// Table configuration
const config = useInvoiceTableConfig();
const routes = useRouteHelper('invoices');
const customerRoutes = useRouteHelper('customers');

// Delete dialog state
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const invoiceToDelete = ref<Invoice | null>(null);

// Helper functions
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};

const getPaymentMethodLabel = (method: string) => {
  const labels: Record<string, string> = {
    bank_transfer: 'Bonifico',
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
const handleEdit = (id: number) => {
  router.visit(routes.edit(id));
};

const handleDelete = (id: number) => {
  const invoice = props.invoices.data.find((i: Invoice) => i.id === id);
  if (invoice) {
    invoiceToDelete.value = invoice;
    showDeleteDialog.value = true;
  }
};

const confirmDelete = () => {
  if (invoiceToDelete.value) {
    isDeleting.value = true;
    router.delete(routes.destroy(invoiceToDelete.value.id), {
      preserveScroll: true,
      onFinish: () => {
        isDeleting.value = false;
        showDeleteDialog.value = false;
        invoiceToDelete.value = null;
      },
    });
  }
};

const cancelDelete = () => {
  invoiceToDelete.value = null;
};

const handleRowClick = (data: any) => {
  router.visit(routes.show(data.id));
};
</script>
