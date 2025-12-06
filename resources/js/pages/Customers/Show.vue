<template>
  <Head :title="customer.business_name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <Heading
            :title="customer.business_name"
            description="Dettagli del cliente"
          />
          <div
            class="mt-2 flex items-center gap-2 text-sm text-muted-foreground"
          >
            <Calendar class="h-4 w-4" />
            Creato il {{ formatDate(customer.created_at) }}
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" as-child>
            <Link :href="routes.edit(customer.id)">
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

      <!-- Customer Details -->
      <div class="grid gap-6 md:grid-cols-2">
        <!-- Basic Information -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Building class="h-5 w-5" />
              Informazioni Aziendali
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <span class="text-sm font-medium text-muted-foreground">
                Ragione Sociale
              </span>
              <p class="mt-1 text-sm">{{ customer.business_name }}</p>
            </div>

            <div v-if="customer.vat_number" class="grid grid-cols-2 gap-4">
              <div>
                <span class="text-sm font-medium text-muted-foreground">
                  Partita IVA
                </span>
                <p class="mt-1 font-mono text-sm">{{ customer.vat_number }}</p>
              </div>
              <div v-if="customer.tax_code">
                <span class="text-sm font-medium text-muted-foreground">
                  Codice Fiscale
                </span>
                <p class="mt-1 font-mono text-sm">{{ customer.tax_code }}</p>
              </div>
            </div>

            <div v-if="customer.tax_code && !customer.vat_number">
              <span class="text-sm font-medium text-muted-foreground">
                Codice Fiscale
              </span>
              <p class="mt-1 font-mono text-sm">{{ customer.tax_code }}</p>
            </div>
          </CardContent>
        </Card>

        <!-- Contact Information -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Phone class="h-5 w-5" />
              Contatti
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div v-if="customer.email">
              <span class="text-sm font-medium text-muted-foreground">
                Email
              </span>
              <p class="mt-1 text-sm">
                <a
                  :href="`mailto:${customer.email}`"
                  class="text-primary hover:underline"
                >
                  {{ customer.email }}
                </a>
              </p>
            </div>

            <div v-if="customer.phone">
              <span class="text-sm font-medium text-muted-foreground">
                Telefono
              </span>
              <p class="mt-1 text-sm">
                <a
                  :href="`tel:${customer.phone}`"
                  class="text-primary hover:underline"
                >
                  {{ customer.phone }}
                </a>
              </p>
            </div>

            <div v-if="customer.pec">
              <span class="text-sm font-medium text-muted-foreground">
                PEC
              </span>
              <p class="mt-1 text-sm">
                <a
                  :href="`mailto:${customer.pec}`"
                  class="text-primary hover:underline"
                >
                  {{ customer.pec }}
                </a>
              </p>
            </div>

            <div v-if="customer.sdi_code">
              <span class="text-sm font-medium text-muted-foreground">
                Codice SDI
              </span>
              <p class="mt-1 font-mono text-sm">{{ customer.sdi_code }}</p>
            </div>

            <div v-if="!hasContactInfo" class="text-sm text-muted-foreground">
              Nessuna informazione di contatto disponibile
            </div>
          </CardContent>
        </Card>

        <!-- Address Information -->
        <Card v-if="hasAddressInfo">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <MapPin class="h-5 w-5" />
              Indirizzo
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div v-if="customer.address">
              <span class="text-sm font-medium text-muted-foreground">
                Indirizzo
              </span>
              <p class="mt-1 text-sm">{{ customer.address }}</p>
            </div>

            <div
              v-if="customer.city || customer.postal_code || customer.province"
              class="grid grid-cols-3 gap-4"
            >
              <div v-if="customer.city">
                <span class="text-sm font-medium text-muted-foreground">
                  Città
                </span>
                <p class="mt-1 text-sm">{{ customer.city }}</p>
              </div>
              <div v-if="customer.postal_code">
                <span class="text-sm font-medium text-muted-foreground">
                  CAP
                </span>
                <p class="mt-1 text-sm">{{ customer.postal_code }}</p>
              </div>
              <div v-if="customer.province">
                <span class="text-sm font-medium text-muted-foreground">
                  Provincia
                </span>
                <p class="mt-1 text-sm">{{ customer.province }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Notes -->
        <Card v-if="customer.notes">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="h-5 w-5" />
              Note
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-sm whitespace-pre-wrap">{{ customer.notes }}</p>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:isOpen="showDeleteDialog"
      :is-deleting="isDeleting"
      title="Elimina Cliente"
      confirm-text="Elimina Cliente"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    >
      <template #title>
        <span>Elimina {{ customer.business_name }}</span>
      </template>
      <template #message>
        <p>
          Sei sicuro di voler eliminare il cliente
          <span class="font-semibold">{{ customer.business_name }}</span
          >?
        </p>
        <p class="mt-2 text-sm text-muted-foreground">
          Tutti i dati associati a questo cliente verranno eliminati
          definitivamente. Questa azione non può essere annullata.
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
  Edit,
  FileText,
  MapPin,
  Phone,
  Trash2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

import DeleteConfirmDialog from '@/components/DeleteConfirmDialog.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

// Delete dialog state
const showDeleteDialog = ref(false);
const isDeleting = ref(false);

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
];

// Computed properties
const hasContactInfo = computed(() => {
  return (
    props.customer.email ||
    props.customer.phone ||
    props.customer.pec ||
    props.customer.sdi_code
  );
});

const hasAddressInfo = computed(() => {
  return (
    props.customer.address ||
    props.customer.city ||
    props.customer.postal_code ||
    props.customer.province
  );
});

// Utility functions
const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// Event handlers
const handleDelete = () => {
  showDeleteDialog.value = true;
};

const confirmDelete = () => {
  isDeleting.value = true;
  router.delete(routes.destroy(props.customer.id), {
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
  // La dialog si chiude automaticamente
};
</script>
