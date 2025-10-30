<template>
  <Card>
    <CardContent class="p-0">
      <DataTable
        :value="data"
        :paginator="true"
        :rows="Number(filters.per_page)"
        :rowsPerPageOptions="rowsPerPageOptions"
        :totalRecords="meta.total"
        :lazy="true"
        :paginator-template="paginatorTemplate"
        :current-page-report-template="currentPageReportTemplate"
        :sortable="true"
        class="border-0"
        @row-click="onRowClick"
        @page="onPage"
        @sort="onSort"
      >
        <!-- Header with search -->
        <template #header>
          <div class="flex items-center justify-between border-b p-4">
            <h3 class="text-lg font-medium">{{ title }}</h3>
            <div class="flex items-center space-x-4">
              <IconField v-if="searchable" icon-position="left">
                <InputIcon>
                  <Search class="h-4 w-4" />
                </InputIcon>
                <InputText
                  v-model="searchValue"
                  :placeholder="searchPlaceholder"
                  class="w-80"
                  @input="onSearch"
                />
              </IconField>
              <slot name="header-actions" />
            </div>
          </div>
        </template>

        <!-- Dynamic columns -->
        <Column
          v-for="column in columns"
          :key="column.field"
          :field="column.field"
          :header="column.header"
          :sortable="column.sortable"
          :class="getColumnClass(column)"
        >
          <template #body="{ data: rowData }">
            <slot
              :name="`column-${column.field}`"
              :data="rowData"
              :value="getFieldValue(rowData, column.field)"
            >
              <!-- Default column rendering based on type -->
              <component
                :is="getColumnComponent(column)"
                :data="rowData"
                :field="column.field"
                :type="column.type"
              />
            </slot>
          </template>
        </Column>

        <!-- Actions column -->
        <Column v-if="hasActions" header="Azioni" class="w-20">
          <template #body="{ data: rowData }">
            <div class="flex space-x-2">
              <Button
                v-if="actions.edit"
                variant="ghost"
                size="sm"
                @click.stop="emit('edit', rowData.id)"
              >
                <Pencil class="h-4 w-4" />
              </Button>
              <Button
                v-if="actions.delete"
                variant="ghost"
                size="sm"
                @click.stop="confirmDelete(rowData.id)"
              >
                <Trash2 class="h-4 w-4" />
              </Button>
              <Button
                v-for="customAction in actions.custom"
                :key="customAction.action"
                :variant="customAction.variant || 'ghost'"
                size="sm"
                @click.stop="
                  handleCustomAction(customAction.action, rowData.id)
                "
              >
                <component :is="customAction.icon" class="h-4 w-4" />
              </Button>
            </div>
          </template>
        </Column>

        <!-- Empty state -->
        <template #empty>
          <div class="py-12 text-center">
            <component
              :is="emptyIcon"
              class="mx-auto mb-4 h-12 w-12 text-gray-400"
            />
            <h3 class="mb-2 text-lg font-medium text-gray-900">
              {{ emptyTitle }}
            </h3>
            <p class="mb-4 text-gray-500">
              {{ emptyDescription }}
            </p>
            <slot name="empty-actions">
              <Button v-if="createRoute" as-child>
                <Link :href="createRoute">
                  <Plus class="mr-2 h-4 w-4" />
                  {{ createButtonText }}
                </Link>
              </Button>
            </slot>
          </div>
        </template>
      </DataTable>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { Pencil, Plus, Search, Trash2, Users } from 'lucide-vue-next';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { computed, defineComponent, h, ref, watch } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
  type TableActions,
  type TableColumn,
  type TableEmitEvents,
  type TableFilters,
  type TableMeta,
} from '@/types/table';

// Default column component for rendering values
const DefaultColumnRenderer = defineComponent({
  props: {
    data: { type: Object, required: true },
    field: { type: String, required: true },
    type: { type: String, default: 'text' },
  },
  setup(props) {
    const getFieldValue = (obj: any, path: string) => {
      return path.split('.').reduce((current, key) => current?.[key], obj);
    };

    const value = computed(() => getFieldValue(props.data, props.field));

    const formatValue = () => {
      if (!value.value) return '-';

      switch (props.type) {
        case 'email':
          return h(
            'a',
            {
              href: `mailto:${value.value}`,
              class: 'text-blue-600 hover:text-blue-800 hover:underline',
              onClick: (e: Event) => e.stopPropagation(),
            },
            value.value,
          );
        case 'phone':
          return h(
            'a',
            {
              href: `tel:${value.value}`,
              class: 'text-blue-600 hover:text-blue-800 hover:underline',
              onClick: (e: Event) => e.stopPropagation(),
            },
            value.value,
          );
        case 'date':
          return new Date(value.value).toLocaleDateString('it-IT');
        default:
          return value.value;
      }
    };

    return () => formatValue();
  },
});

// Props
interface Props {
  data: any[];
  meta: TableMeta;
  filters: TableFilters;
  columns: TableColumn[];
  title?: string;
  searchable?: boolean;
  searchPlaceholder?: string;
  entityName?: string;
  entityPlural?: string;
  createRoute?: string;
  createButtonText?: string;
  actions?: TableActions;
  emptyIcon?: any;
  emptyTitle?: string;
  emptyDescription?: string;
  rowsPerPageOptions?: number[];
  deleteConfirmMessage?: string;
  routePrefix?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Lista',
  searchable: true,
  searchPlaceholder: 'Cerca...',
  entityName: 'elemento',
  entityPlural: 'elementi',
  createButtonText: 'Aggiungi nuovo',
  actions: () => ({ edit: true, delete: true }),
  emptyIcon: Users,
  emptyTitle: 'Nessun elemento trovato',
  emptyDescription:
    'Non ci sono elementi che corrispondono ai criteri di ricerca.',
  rowsPerPageOptions: () => [10, 15, 25, 50],
  deleteConfirmMessage: 'Sei sicuro di voler eliminare questo elemento?',
});

// Emits
const emit = defineEmits<TableEmitEvents>();

// Computed
const searchValue = ref(props.filters.search || '');
const hasActions = computed(
  () =>
    props.actions.edit ||
    props.actions.delete ||
    (props.actions.custom && props.actions.custom.length > 0),
);

const paginatorTemplate = computed(
  () =>
    'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown',
);

const currentPageReportTemplate = computed(
  () => `Mostrando {first} a {last} di {totalRecords} ${props.entityPlural}`,
);

// Helper functions
const getFieldValue = (obj: any, path: string) => {
  return path.split('.').reduce((current, key) => current?.[key], obj);
};

const getColumnClass = (column: TableColumn) => {
  const classes = [];
  if (column.width) classes.push(column.width);
  if (column.align === 'center') classes.push('text-center');
  if (column.align === 'right') classes.push('text-right');
  return classes.join(' ');
};

const getColumnComponent = (column: TableColumn) => {
  return column.type === 'custom' ? 'div' : DefaultColumnRenderer;
};

const handleCustomAction = (action: string, id: number) => {
  // Custom actions are handled by parent component
  emit('row-click', { id, action });
};

// Route helper
const buildRoute = (action: string, id?: number) => {
  if (!props.routePrefix) return '#';

  switch (action) {
    case 'index':
      return `/${props.routePrefix}`;
    case 'show':
      return `/${props.routePrefix}/${id}`;
    case 'edit':
      return `/${props.routePrefix}/${id}/edit`;
    case 'destroy':
      return `/${props.routePrefix}/${id}`;
    default:
      return '#';
  }
};

// Event handlers
const debouncedSearch = useDebounceFn(() => {
  updateFilters({ search: searchValue.value || undefined, page: 1 });
}, 500);

const updateFilters = (newFilters: Partial<TableFilters>) => {
  router.get(
    buildRoute('index'),
    { ...props.filters, ...newFilters },
    { preserveState: true, replace: true },
  );
};

const onRowClick = (event: any) => {
  router.visit(buildRoute('show', event.data.id));
};

const onSearch = () => {
  debouncedSearch();
};

const onPage = (event: any) => {
  updateFilters({
    per_page: event.rows,
    page: event.page + 1,
  });
};

const onSort = (event: any) => {
  updateFilters({
    sort_field: event.sortField,
    sort_direction: event.sortOrder === 1 ? 'asc' : 'desc',
    page: 1,
  });
};

const confirmDelete = (id: number) => {
  if (confirm(props.deleteConfirmMessage)) {
    router.delete(buildRoute('destroy', id), {
      preserveScroll: true,
    });
  }
};

// Watch for search changes
watch(searchValue, () => {
  debouncedSearch();
});
</script>

<style>
/* Import the same PrimeVue styles from the original component */
.p-datatable {
  border-radius: 0.5rem;
  border: 1px solid hsl(var(--border));
}

.p-datatable .p-datatable-header {
  background-color: hsl(var(--background));
  border-bottom: 1px solid hsl(var(--border));
}

.p-datatable .p-datatable-thead > tr > th {
  background-color: hsl(var(--muted) / 0.5);
  border-bottom: 1px solid hsl(var(--border));
  color: hsl(var(--muted-foreground));
  font-weight: 500;
}

.p-datatable .p-datatable-tbody > tr {
  border-bottom: 1px solid hsl(var(--border));
  cursor: pointer;
}

.p-datatable .p-datatable-tbody > tr:hover {
  background-color: hsl(var(--muted) / 0.5);
}

.p-datatable .p-datatable-tbody > tr > td {
  border: 0;
}

.p-paginator {
  background-color: hsl(var(--background));
  border-top: 1px solid hsl(var(--border));
}

.p-paginator .p-paginator-pages .p-paginator-page {
  color: hsl(var(--foreground));
}

.p-paginator .p-paginator-pages .p-paginator-page:hover {
  background-color: hsl(var(--muted));
}

.p-paginator .p-paginator-pages .p-paginator-page.p-highlight {
  background-color: hsl(var(--primary));
  color: hsl(var(--primary-foreground));
}

.p-inputtext {
  border-color: hsl(var(--input));
  background-color: hsl(var(--background));
  color: hsl(var(--foreground));
}

.p-inputtext:focus {
  outline: 2px solid hsl(var(--ring));
  outline-offset: 2px;
}
</style>
