import type { TableActions, TableColumn } from '@/types/table';
import { Users } from 'lucide-vue-next';

// Customer table configuration
export const useCustomerTableConfig = () => {
  const columns: TableColumn[] = [
    {
      field: 'business_name',
      header: 'Ragione Sociale',
      sortable: true,
      width: 'min-w-48',
    },
    {
      field: 'vat_number',
      header: 'P.IVA',
      sortable: true,
      width: 'min-w-32',
    },
    {
      field: 'tax_code',
      header: 'Codice Fiscale',
      sortable: true,
      width: 'min-w-40',
    },
    {
      field: 'email',
      header: 'Email',
      sortable: true,
      width: 'min-w-48',
      type: 'email',
    },
    {
      field: 'phone',
      header: 'Telefono',
      sortable: true,
      width: 'min-w-32',
      type: 'phone',
    },
    {
      field: 'city',
      header: 'Città',
      sortable: true,
      width: 'min-w-32',
    },
    {
      field: 'created_at',
      header: 'Creato il',
      sortable: true,
      width: 'min-w-32',
      type: 'date',
    },
  ];

  const actions: TableActions = {
    edit: true,
    delete: true,
  };

  return {
    columns,
    actions,
    title: 'Lista Clienti',
    entityName: 'cliente',
    entityPlural: 'clienti',
    searchPlaceholder: 'Cerca clienti...',
    emptyTitle: 'Nessun cliente trovato',
    emptyDescription:
      'Non ci sono clienti che corrispondono ai criteri di ricerca.',
    emptyIcon: Users,
    createButtonText: 'Aggiungi il primo cliente',
    deleteConfirmMessage: 'Sei sicuro di voler eliminare questo cliente?',
    routePrefix: 'customers',
  };
};

// Helper for route generation
export const useRouteHelper = (prefix: string) => {
  return {
    index: () => `/${prefix}`,
    create: () => `/${prefix}/create`,
    show: (id: number) => `/${prefix}/${id}`,
    edit: (id: number) => `/${prefix}/${id}/edit`,
    destroy: (id: number) => `/${prefix}/${id}`,
  };
};
