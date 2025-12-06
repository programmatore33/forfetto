<template>
  <Head title="Prodotti e Servizi" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <Heading
          title="Prodotti e Servizi"
          description="Gestisci il catalogo dei tuoi prodotti e servizi"
        />
        <Button as-child>
          <Link :href="routes.create()">
            <Plus class="mr-2 h-4 w-4" />
            Nuovo Prodotto
          </Link>
        </Button>
      </div>

      <!-- Data Table -->
      <DataTableWithPagination
        :data="products.data"
        :meta="{
          current_page: products.current_page,
          last_page: products.last_page,
          per_page: products.per_page,
          total: products.total,
          from: products.from,
          to: products.to,
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
        <!-- Custom column for product name -->
        <template #column-name="{ data }">
          <div class="font-medium">{{ data.name }}</div>
        </template>
      </DataTableWithPagination>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:isOpen="showDeleteDialog"
      :is-deleting="isDeleting"
      title="Elimina Prodotto"
      :description="`Sei sicuro di voler eliminare il prodotto ${productToDelete?.name}?`"
      confirm-text="Elimina Prodotto"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    >
      <template #message>
        <p>
          Sei sicuro di voler eliminare il prodotto
          <span class="font-semibold">{{ productToDelete?.name }}</span
          >?
        </p>
        <p class="mt-2 text-sm text-muted-foreground">
          Questo prodotto verrà eliminato definitivamente. Questa azione non può
          essere annullata.
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
import { Button } from '@/components/ui/button';
import {
  useProductTableConfig,
  useRouteHelper,
} from '@/composables/useTableConfigs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Product {
  id: number;
  code: string | null;
  name: string;
  description: string | null;
  unit_price: number;
}

interface Props {
  products: {
    data: Product[];
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
    title: 'Prodotti e Servizi',
    href: '/products',
  },
];

// Table configuration
const config = useProductTableConfig();
const routes = useRouteHelper('products');

// Delete dialog state
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const productToDelete = ref<Product | null>(null);

// Event handlers
const handleEdit = (id: number) => {
  router.visit(routes.edit(id));
};

const handleDelete = (id: number) => {
  // Find the product to delete to show the name in the dialog
  const product = props.products.data.find((p: Product) => p.id === id);
  if (product) {
    productToDelete.value = product;
    showDeleteDialog.value = true;
  }
};

const confirmDelete = () => {
  if (productToDelete.value) {
    isDeleting.value = true;
    router.delete(routes.destroy(productToDelete.value.id), {
      preserveScroll: true,
      onFinish: () => {
        isDeleting.value = false;
        showDeleteDialog.value = false;
        productToDelete.value = null;
      },
    });
  }
};

const cancelDelete = () => {
  productToDelete.value = null;
};

const handleRowClick = (data: any) => {
  router.visit(routes.edit(data.id));
};
</script>
