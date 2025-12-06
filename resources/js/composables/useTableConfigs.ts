import type { TableActions, TableColumn } from '@/types/table';
import { FileText, Package, Receipt, Users } from 'lucide-vue-next';

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

// Invoice table configuration
export const useInvoiceTableConfig = () => {
  const columns: TableColumn[] = [
    {
      field: 'invoice_number',
      header: 'Numero',
      sortable: true,
      width: 'min-w-32',
    },
    {
      field: 'customer_business_name',
      header: 'Cliente',
      sortable: true,
      width: 'min-w-48',
    },
    {
      field: 'issue_date',
      header: 'Data Emissione',
      sortable: true,
      width: 'min-w-32',
      type: 'date',
    },
    {
      field: 'amount',
      header: 'Importo',
      sortable: true,
      width: 'min-w-32',
      type: 'currency',
    },
    {
      field: 'payment_method',
      header: 'Metodo Pagamento',
      sortable: false,
      width: 'min-w-36',
    },
    {
      field: 'is_paid',
      header: 'Stato',
      sortable: true,
      width: 'min-w-32',
      type: 'boolean',
    },
  ];

  const actions: TableActions = {
    edit: true,
    delete: true,
  };

  return {
    columns,
    actions,
    title: 'Lista Fatture',
    entityName: 'fattura',
    entityPlural: 'fatture',
    searchPlaceholder: 'Cerca fatture...',
    emptyTitle: 'Nessuna fattura trovata',
    emptyDescription:
      'Non ci sono fatture che corrispondono ai criteri di ricerca.',
    emptyIcon: FileText,
    createButtonText: 'Aggiungi la prima fattura',
    deleteConfirmMessage: 'Sei sicuro di voler eliminare questa fattura?',
    routePrefix: 'invoices',
  };
};

// Expense table configuration
export const useExpenseTableConfig = () => {
  const columns: TableColumn[] = [
    {
      field: 'expense_date',
      header: 'Data',
      sortable: true,
      width: 'min-w-32',
      type: 'date',
    },
    {
      field: 'expense_category',
      header: 'Categoria',
      sortable: false,
      width: 'min-w-36',
    },
    {
      field: 'description',
      header: 'Descrizione',
      sortable: true,
      width: 'min-w-48',
    },
    {
      field: 'supplier',
      header: 'Fornitore',
      sortable: true,
      width: 'min-w-36',
    },
    {
      field: 'amount',
      header: 'Importo',
      sortable: true,
      width: 'min-w-32',
      type: 'currency',
    },
  ];

  const actions: TableActions = {
    edit: true,
    delete: true,
  };

  return {
    columns,
    actions,
    title: 'Lista Spese',
    entityName: 'spesa',
    entityPlural: 'spese',
    searchPlaceholder: 'Cerca spese...',
    emptyTitle: 'Nessuna spesa trovata',
    emptyDescription:
      'Non ci sono spese che corrispondono ai criteri di ricerca.',
    emptyIcon: Receipt,
    createButtonText: 'Aggiungi la prima spesa',
    deleteConfirmMessage: 'Sei sicuro di voler eliminare questa spesa?',
    routePrefix: 'expenses',
  };
};

// Product table configuration
export const useProductTableConfig = () => {
  const columns: TableColumn[] = [
    {
      field: 'code',
      header: 'Codice',
      sortable: true,
      width: 'min-w-32',
    },
    {
      field: 'name',
      header: 'Nome',
      sortable: true,
      width: 'min-w-48',
    },
    {
      field: 'description',
      header: 'Descrizione',
      sortable: false,
      width: 'min-w-64',
    },
    {
      field: 'unit_price',
      header: 'Prezzo Unitario',
      sortable: true,
      width: 'min-w-32',
      type: 'currency',
    },
  ];

  const actions: TableActions = {
    edit: true,
    delete: true,
  };

  return {
    columns,
    actions,
    title: 'Lista Prodotti',
    entityName: 'prodotto',
    entityPlural: 'prodotti',
    searchPlaceholder: 'Cerca prodotti...',
    emptyTitle: 'Nessun prodotto trovato',
    emptyDescription:
      'Non ci sono prodotti che corrispondono ai criteri di ricerca.',
    emptyIcon: Package,
    createButtonText: 'Aggiungi il primo prodotto',
    deleteConfirmMessage: 'Sei sicuro di voler eliminare questo prodotto?',
    routePrefix: 'products',
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
