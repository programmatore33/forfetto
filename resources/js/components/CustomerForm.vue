<template>
  <form @submit.prevent="onSubmit" class="space-y-6">
    <!-- Basic Information -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <Building class="h-5 w-5" />
          Informazioni Aziendali
        </CardTitle>
        <CardDescription>
          Inserisci i dati principali del cliente
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid gap-2">
          <Label for="business_name">Ragione Sociale *</Label>
          <Input
            id="business_name"
            v-model="form.business_name"
            name="business_name"
            placeholder="Es: Mario Rossi S.r.l."
            required
            :class="{ 'border-red-500': errors.business_name }"
          />
          <InputError :message="errors.business_name" />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="vat_number">Partita IVA</Label>
            <Input
              id="vat_number"
              v-model="form.vat_number"
              name="vat_number"
              placeholder="12345678901"
              maxlength="11"
              :class="{ 'border-red-500': errors.vat_number }"
            />
            <InputError :message="errors.vat_number" />
          </div>

          <div class="grid gap-2">
            <Label for="tax_code">Codice Fiscale</Label>
            <Input
              id="tax_code"
              v-model="form.tax_code"
              name="tax_code"
              placeholder="RSSMRA80A01H501M"
              maxlength="16"
              :class="{ 'border-red-500': errors.tax_code }"
            />
            <InputError :message="errors.tax_code" />
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Contact Information -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <Phone class="h-5 w-5" />
          Informazioni di Contatto
        </CardTitle>
        <CardDescription> Email, telefono e altri contatti </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              placeholder="cliente@esempio.it"
              :class="{ 'border-red-500': errors.email }"
            />
            <InputError :message="errors.email" />
          </div>

          <div class="grid gap-2">
            <Label for="phone">Telefono</Label>
            <Input
              id="phone"
              v-model="form.phone"
              name="phone"
              placeholder="+39 123 456 7890"
              :class="{ 'border-red-500': errors.phone }"
            />
            <InputError :message="errors.phone" />
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="pec">PEC</Label>
            <Input
              id="pec"
              v-model="form.pec"
              name="pec"
              type="email"
              placeholder="cliente@pec.it"
              :class="{ 'border-red-500': errors.pec }"
            />
            <InputError :message="errors.pec" />
          </div>

          <div class="grid gap-2">
            <Label for="sdi_code">Codice SDI</Label>
            <Input
              id="sdi_code"
              v-model="form.sdi_code"
              name="sdi_code"
              placeholder="ABCDEFG"
              maxlength="7"
              :class="{ 'border-red-500': errors.sdi_code }"
            />
            <InputError :message="errors.sdi_code" />
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Address Information -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <MapPin class="h-5 w-5" />
          Indirizzo
        </CardTitle>
        <CardDescription>
          Informazioni sull'indirizzo del cliente
        </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="grid gap-2">
          <Label for="address">Indirizzo</Label>
          <textarea
            id="address"
            v-model="form.address"
            name="address"
            placeholder="Via Roma, 123"
            rows="2"
            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            :class="{ 'border-red-500': errors.address }"
          />
          <InputError :message="errors.address" />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <div class="grid gap-2">
            <Label for="city">Città</Label>
            <Input
              id="city"
              v-model="form.city"
              name="city"
              placeholder="Milano"
              :class="{ 'border-red-500': errors.city }"
            />
            <InputError :message="errors.city" />
          </div>

          <div class="grid gap-2">
            <Label for="postal_code">CAP</Label>
            <Input
              id="postal_code"
              v-model="form.postal_code"
              name="postal_code"
              placeholder="20100"
              maxlength="10"
              :class="{ 'border-red-500': errors.postal_code }"
            />
            <InputError :message="errors.postal_code" />
          </div>

          <div class="grid gap-2">
            <Label for="province">Provincia</Label>
            <Input
              id="province"
              v-model="form.province"
              name="province"
              placeholder="MI"
              maxlength="2"
              :class="{ 'border-red-500': errors.province }"
            />
            <InputError :message="errors.province" />
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Notes -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <FileText class="h-5 w-5" />
          Note
        </CardTitle>
        <CardDescription>
          Aggiungi eventuali note o informazioni aggiuntive
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div class="grid gap-2">
          <Label for="notes">Note</Label>
          <textarea
            id="notes"
            v-model="form.notes"
            name="notes"
            placeholder="Note aggiuntive sul cliente..."
            rows="4"
            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            :class="{ 'border-red-500': errors.notes }"
          />
          <InputError :message="errors.notes" />
        </div>
      </CardContent>
    </Card>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-2">
      <Button type="button" variant="outline" @click="onCancel">
        Annulla
      </Button>
      <Button type="submit" :disabled="processing">
        <Save class="mr-2 h-4 w-4" />
        {{ isEdit ? 'Aggiorna Cliente' : 'Crea Cliente' }}
      </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Building, FileText, MapPin, Phone, Save } from 'lucide-vue-next';
import { computed, reactive, watch } from 'vue';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Customer {
  id?: number;
  business_name: string;
  vat_number: string;
  tax_code: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  postal_code: string;
  province: string;
  pec: string;
  sdi_code: string;
  notes: string;
}

interface Props {
  customer?: Customer | null;
  errors?: Record<string, string>;
  processing?: boolean;
}

interface Emits {
  (e: 'submit', form: Customer): void;
  (e: 'cancel'): void;
}

const props = withDefaults(defineProps<Props>(), {
  customer: null,
  errors: () => ({}),
  processing: false,
});

const emit = defineEmits<Emits>();

// Form data
const form = reactive<Customer>({
  business_name: '',
  vat_number: '',
  tax_code: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  postal_code: '',
  province: '',
  pec: '',
  sdi_code: '',
  notes: '',
});

// Computed
const isEdit = computed(() => !!props.customer?.id);

// Initialize form with customer data if editing
watch(
  () => props.customer,
  (customer) => {
    if (customer) {
      Object.assign(form, {
        business_name: customer.business_name || '',
        vat_number: customer.vat_number || '',
        tax_code: customer.tax_code || '',
        email: customer.email || '',
        phone: customer.phone || '',
        address: customer.address || '',
        city: customer.city || '',
        postal_code: customer.postal_code || '',
        province: customer.province || '',
        pec: customer.pec || '',
        sdi_code: customer.sdi_code || '',
        notes: customer.notes || '',
      });
    }
  },
  { immediate: true },
);

// Event handlers
const onSubmit = () => {
  emit('submit', { ...form });
};

const onCancel = () => {
  emit('cancel');
};
</script>
