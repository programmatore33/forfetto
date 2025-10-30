export interface TableColumn {
  field: string;
  header: string;
  sortable?: boolean;
  width?: string;
  align?: 'left' | 'center' | 'right';
  type?: 'text' | 'email' | 'phone' | 'date' | 'custom';
}

export interface TableMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

export interface TableFilters {
  search: string | null;
  per_page: number;
  sort_field: string;
  sort_direction: 'asc' | 'desc';
  [key: string]: any;
}

export interface TableEmitEvents {
  'update:filters': [filters: Partial<TableFilters>];
  'row-click': [data: any];
  edit: [id: number];
  delete: [id: number];
}

export interface TableActions {
  edit?: boolean;
  delete?: boolean;
  custom?: Array<{
    icon: any;
    label: string;
    action: string;
    variant?: 'default' | 'destructive' | 'ghost';
  }>;
}
