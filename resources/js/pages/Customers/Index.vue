<template>
  <Head title="Clienti" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <Heading
          title="Clienti"
          description="Gestisci i tuoi clienti e le loro informazioni"
        />
        <Button as-child>
          <Link :href="routes.create()">
            <Plus class="mr-2 h-4 w-4" />
            Nuovo Cliente
          </Link>
        </Button>
      </div>

      <!-- Data Table -->
      <DataTableWithPagination
        :data="customers.data"
        :meta="{
          current_page: customers.current_page,
          last_page: customers.last_page,
          per_page: customers.per_page,
          total: customers.total,
          from: customers.from,
          to: customers.to,
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
        <!-- Custom column for business name -->
        <template #column-business_name="{ data }">
          <div class="font-medium">{{ data.business_name }}</div>
        </template>
      </DataTableWithPagination>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

import Heading from '@/components/Heading.vue';
import DataTableWithPagination from '@/components/tables/DataTableWithPagination.vue';
import { Button } from '@/components/ui/button';
import {
  useCustomerTableConfig,
  useRouteHelper,
} from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Customer {
  id: number;
  business_name: string;
  vat_number: string | null;
  tax_code: string | null;
  email: string | null;
  phone: string | null;
  city: string | null;
  created_at: string;
}

interface Props {
  customers: {
    data: Customer[];
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

defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Clienti',
    href: '/customers',
  },
];

// Table configuration
const config = useCustomerTableConfig();
const routes = useRouteHelper('customers');

// Event handlers
const handleEdit = (id: number) => {
  router.visit(routes.edit(id));
};

const handleDelete = (id: number) => {
  if (confirm(config.deleteConfirmMessage)) {
    router.delete(routes.destroy(id), {
      preserveScroll: true,
    });
  }
};

const handleRowClick = (data: any) => {
  router.visit(routes.show(data.id));
};
</script>
