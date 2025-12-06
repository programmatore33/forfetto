<template>
  <form @submit.prevent="onSubmit" class="space-y-6">
    <!-- Two Column Layout for Invoice and Customer Data -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Invoice Data -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <FileText class="h-5 w-5" />
            Dati Fattura
          </CardTitle>
          <CardDescription>
            Numero fattura, data e codice ATECO
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="invoice_number">Numero Fattura *</Label>
              <div class="flex gap-2">
                <Input
                  id="invoice_number"
                  v-model="form.invoice_number"
                  name="invoice_number"
                  placeholder="2025/001"
                  required
                  :class="{ 'border-red-500': errors.invoice_number }"
                />
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="suggestInvoiceNumber"
                  :disabled="loadingNumber"
                >
                  {{ loadingNumber ? 'Caricamento...' : 'Suggerisci' }}
                </Button>
              </div>
              <InputError :message="errors.invoice_number" />
            </div>

            <div class="grid gap-2">
              <Label for="issue_date">Data Emissione *</Label>
              <Input
                id="issue_date"
                v-model="form.issue_date"
                name="issue_date"
                type="date"
                required
                :class="{ 'border-red-500': errors.issue_date }"
              />
              <InputError :message="errors.issue_date" />
            </div>
          </div>

          <div class="grid gap-2">
            <Label for="customer_id">Cliente</Label>
            <select
              id="customer_id"
              v-model="form.customer_id"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
              :class="{ 'border-red-500': errors.customer_id }"
            >
              <option :value="null">Nessuno - inserimento manuale</option>
              <option
                v-for="customer in customers"
                :key="customer.id"
                :value="customer.id"
              >
                {{ customer.business_name }}
                <template v-if="customer.vat_number">
                  - P.IVA: {{ customer.vat_number }}
                </template>
              </option>
            </select>
            <InputError :message="errors.customer_id" />
          </div>

          <div class="grid gap-2">
            <Label for="ateco_code_id">Codice ATECO *</Label>
            <select
              id="ateco_code_id"
              v-model="form.ateco_code_id"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
              :class="{ 'border-red-500': errors.ateco_code_id }"
              required
            >
              <option value="">Seleziona codice ATECO</option>
              <option
                v-for="ateco in atecoCodes"
                :key="ateco.id"
                :value="ateco.id"
              >
                {{ ateco.code }} - {{ ateco.description }}
                <template v-if="ateco.is_primary"> (Primario)</template>
              </option>
            </select>
            <InputError :message="errors.ateco_code_id" />
          </div>

          <div class="grid gap-2">
            <Label for="description">Descrizione *</Label>
            <textarea
              id="description"
              v-model="form.description"
              name="description"
              placeholder="Descrizione del servizio o prodotto..."
              rows="3"
              required
              class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
              :class="{ 'border-red-500': errors.description }"
            />
            <InputError :message="errors.description" />
          </div>
        </CardContent>
      </Card>

      <!-- Customer Data Snapshot -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Building class="h-5 w-5" />
            Dati Cliente
          </CardTitle>
          <CardDescription>
            Dati del cliente al momento della fattura
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid gap-2">
            <Label for="customer_business_name">Ragione Sociale *</Label>
            <Input
              id="customer_business_name"
              v-model="form.customer_business_name"
              name="customer_business_name"
              placeholder="Nome cliente"
              required
              :class="{ 'border-red-500': errors.customer_business_name }"
            />
            <InputError :message="errors.customer_business_name" />
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="customer_vat_number">Partita IVA</Label>
              <Input
                id="customer_vat_number"
                v-model="form.customer_vat_number"
                name="customer_vat_number"
                placeholder="12345678901"
                maxlength="20"
                :class="{ 'border-red-500': errors.customer_vat_number }"
              />
              <InputError :message="errors.customer_vat_number" />
            </div>

            <div class="grid gap-2">
              <Label for="customer_tax_code">Codice Fiscale</Label>
              <Input
                id="customer_tax_code"
                v-model="form.customer_tax_code"
                name="customer_tax_code"
                placeholder="RSSMRA80A01H501M"
                maxlength="20"
                :class="{ 'border-red-500': errors.customer_tax_code }"
              />
              <InputError :message="errors.customer_tax_code" />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="customer_email">Email</Label>
              <Input
                id="customer_email"
                v-model="form.customer_email"
                name="customer_email"
                type="email"
                placeholder="cliente@esempio.it"
                :class="{ 'border-red-500': errors.customer_email }"
              />
              <InputError :message="errors.customer_email" />
            </div>

            <div class="grid gap-2">
              <Label for="customer_phone">Telefono</Label>
              <Input
                id="customer_phone"
                v-model="form.customer_phone"
                name="customer_phone"
                placeholder="+39 123 456 7890"
                :class="{ 'border-red-500': errors.customer_phone }"
              />
              <InputError :message="errors.customer_phone" />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="customer_pec">PEC</Label>
              <Input
                id="customer_pec"
                v-model="form.customer_pec"
                name="customer_pec"
                type="email"
                placeholder="cliente@pec.it"
                :class="{ 'border-red-500': errors.customer_pec }"
              />
              <InputError :message="errors.customer_pec" />
            </div>

            <div class="grid gap-2">
              <Label for="customer_sdi_code">Codice SDI</Label>
              <Input
                id="customer_sdi_code"
                v-model="form.customer_sdi_code"
                name="customer_sdi_code"
                placeholder="ABCDEFG"
                maxlength="7"
                :class="{ 'border-red-500': errors.customer_sdi_code }"
              />
              <InputError :message="errors.customer_sdi_code" />
            </div>
          </div>

          <div class="grid gap-2">
            <Label for="customer_address">Indirizzo</Label>
            <Input
              id="customer_address"
              v-model="form.customer_address"
              name="customer_address"
              placeholder="Via Roma, 123"
              :class="{ 'border-red-500': errors.customer_address }"
            />
            <InputError :message="errors.customer_address" />
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="grid gap-2 md:col-span-2">
              <Label for="customer_city">Città</Label>
              <Input
                id="customer_city"
                v-model="form.customer_city"
                name="customer_city"
                placeholder="Milano"
                :class="{ 'border-red-500': errors.customer_city }"
              />
              <InputError :message="errors.customer_city" />
            </div>

            <div class="grid gap-2">
              <Label for="customer_postal_code">CAP</Label>
              <Input
                id="customer_postal_code"
                v-model="form.customer_postal_code"
                name="customer_postal_code"
                placeholder="20100"
                maxlength="10"
                :class="{ 'border-red-500': errors.customer_postal_code }"
              />
              <InputError :message="errors.customer_postal_code" />
            </div>

            <div class="grid gap-2">
              <Label for="customer_province">Provincia</Label>
              <Input
                id="customer_province"
                v-model="form.customer_province"
                name="customer_province"
                placeholder="MI"
                maxlength="2"
                :class="{ 'border-red-500': errors.customer_province }"
              />
              <InputError :message="errors.customer_province" />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Items -->
    <Card>
      <CardHeader>
        <div class="flex items-center justify-between">
          <div>
            <CardTitle class="flex items-center gap-2">
              <Package class="h-5 w-5" />
              Voci di Fattura
            </CardTitle>
            <CardDescription> Prodotti e servizi fatturati </CardDescription>
          </div>
          <Button type="button" size="sm" @click="addItem">
            <Plus class="mr-2 h-4 w-4" />
            Aggiungi Voce
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        <div class="space-y-4">
          <!-- Items Table -->
          <div class="rounded-md border">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-muted/50">
                  <tr>
                    <th class="px-3 py-2 text-left text-sm font-medium">
                      Codice
                    </th>
                    <th class="px-3 py-2 text-left text-sm font-medium">
                      Descrizione *
                    </th>
                    <th class="px-3 py-2 text-right text-sm font-medium">
                      Prezzo
                    </th>
                    <th class="px-3 py-2 text-right text-sm font-medium">
                      Qtà
                    </th>
                    <th class="px-3 py-2 text-right text-sm font-medium">
                      Totale
                    </th>
                    <th
                      class="w-12 px-3 py-2 text-center text-sm font-medium"
                    ></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="form.items.length === 0">
                    <td
                      colspan="6"
                      class="px-3 py-8 text-center text-sm text-muted-foreground"
                    >
                      Nessuna voce. Clicca "Aggiungi Voce" per iniziare.
                    </td>
                  </tr>
                  <tr
                    v-for="(item, index) in form.items"
                    :key="index"
                    class="border-t"
                  >
                    <td class="px-3 py-2">
                      <Input
                        v-model="item.code"
                        type="text"
                        placeholder="Cod."
                        class="h-9 text-sm"
                      />
                    </td>
                    <td class="px-3 py-2">
                      <Input
                        v-model="item.description"
                        type="text"
                        placeholder="Descrizione prodotto/servizio"
                        required
                        class="h-9 text-sm"
                      />
                    </td>
                    <td class="px-3 py-2">
                      <Input
                        v-model.number="item.unit_price"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        required
                        class="h-9 text-right text-sm"
                        @input="updateItemTotal(index)"
                      />
                    </td>
                    <td class="px-3 py-2">
                      <Input
                        v-model.number="item.quantity"
                        type="number"
                        min="1"
                        required
                        class="h-9 w-20 text-right text-sm"
                        @input="updateItemTotal(index)"
                      />
                    </td>
                    <td class="px-3 py-2 text-right text-sm font-semibold">
                      {{ item.total.toFixed(2) }} €
                    </td>
                    <td class="px-3 py-2 text-center">
                      <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8"
                        @click="removeItem(index)"
                      >
                        <Trash2 class="h-4 w-4 text-destructive" />
                      </Button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Summary -->
          <div class="flex flex-col items-end gap-2 border-t pt-4">
            <div class="flex w-full max-w-sm justify-between text-sm">
              <span class="font-medium">Subtotale:</span>
              <span class="font-semibold"
                >{{ calculatedAmount.toFixed(2) }} €</span
              >
            </div>

            <div class="flex w-full max-w-sm items-center justify-between">
              <Label for="apply_contributo" class="cursor-pointer text-sm">
                Contributo integrativo 4%
              </Label>
              <input
                id="apply_contributo"
                v-model="applyContributo"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300"
              />
            </div>

            <div
              v-if="applyContributo"
              class="flex w-full max-w-sm justify-between text-sm"
            >
              <span class="font-medium">Contributo (4%):</span>
              <span class="font-semibold"
                >{{ calculatedContributo.toFixed(2) }} €</span
              >
            </div>

            <div
              class="flex w-full max-w-sm justify-between border-t pt-2 text-base"
            >
              <span class="font-bold">Totale Fattura:</span>
              <span class="text-lg font-bold"
                >{{ calculatedNetAmount.toFixed(2) }} €</span
              >
            </div>
          </div>
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
        <CardDescription> Stato e dettagli del pagamento </CardDescription>
      </CardHeader>
      <CardContent class="space-y-4">
        <div class="flex items-center space-x-2">
          <input
            id="is_paid"
            v-model="form.is_paid"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300"
          />
          <Label for="is_paid" class="cursor-pointer"> Fattura pagata </Label>
        </div>

        <div v-if="form.is_paid" class="space-y-4">
          <div class="grid gap-2">
            <Label for="payment_date">Data Pagamento</Label>
            <Input
              id="payment_date"
              v-model="form.payment_date"
              name="payment_date"
              type="date"
              :class="{ 'border-red-500': errors.payment_date }"
            />
            <InputError :message="errors.payment_date" />
          </div>

          <div class="grid gap-2">
            <Label for="payment_method">Metodo di Pagamento</Label>
            <select
              id="payment_method"
              v-model="form.payment_method"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
              :class="{ 'border-red-500': errors.payment_method }"
            >
              <option value="">Seleziona metodo</option>
              <option value="bank_transfer">Bonifico Bancario</option>
              <option value="cash">Contanti</option>
              <option value="check">Assegno</option>
              <option value="paypal">PayPal</option>
              <option value="other">Altro</option>
            </select>
            <InputError :message="errors.payment_method" />
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Notes -->
    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2">
          <StickyNote class="h-5 w-5" />
          Note
        </CardTitle>
        <CardDescription> Note aggiuntive sulla fattura </CardDescription>
      </CardHeader>
      <CardContent>
        <div class="grid gap-2">
          <Label for="notes">Note</Label>
          <textarea
            id="notes"
            v-model="form.notes"
            name="notes"
            placeholder="Note aggiuntive..."
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
        {{ isEdit ? 'Aggiorna Fattura' : 'Crea Fattura' }}
      </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import {
  Building,
  CreditCard,
  FileText,
  Package,
  Plus,
  Save,
  StickyNote,
  Trash2,
} from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

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
  id: number;
  business_name: string;
  vat_number: string | null;
  tax_code: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  city: string | null;
  province: string | null;
  postal_code: string | null;
  pec: string | null;
  sdi_code: string | null;
}

interface AtecoCode {
  id: number;
  code: string;
  description: string;
  is_primary: boolean;
}

interface InvoiceItem {
  id?: number;
  product_id: number | null;
  code: string;
  description: string;
  unit_price: number;
  quantity: number;
  total: number;
}

interface Invoice {
  id?: number;
  invoice_number: string;
  issue_date: string;
  customer_id: number | null;
  ateco_code_id: number;
  customer_business_name: string;
  customer_email: string;
  customer_vat_number: string;
  customer_tax_code: string;
  customer_address: string;
  customer_city: string;
  customer_province: string;
  customer_postal_code: string;
  customer_phone: string;
  customer_pec: string;
  customer_sdi_code: string;
  description: string;
  amount: number;
  contributo_integrativo_applied?: boolean;
  contributo_integrativo_amount?: number;
  net_amount: number;
  is_paid: boolean;
  payment_date: string;
  payment_method: string;
  notes: string;
  items: InvoiceItem[];
}

interface Props {
  invoice?: Invoice | null;
  customers: Customer[];
  atecoCodes: AtecoCode[];
  errors?: Record<string, string>;
  processing?: boolean;
}

interface Emits {
  (e: 'submit', form: Invoice): void;
  (e: 'cancel'): void;
}

const props = withDefaults(defineProps<Props>(), {
  invoice: null,
  errors: () => ({}),
  processing: false,
});

const emit = defineEmits<Emits>();

// Form data
const form = reactive<Invoice>({
  invoice_number: '',
  issue_date: new Date().toISOString().split('T')[0],
  customer_id: null,
  ateco_code_id: 0,
  customer_business_name: '',
  customer_email: '',
  customer_vat_number: '',
  customer_tax_code: '',
  customer_address: '',
  customer_city: '',
  customer_province: '',
  customer_postal_code: '',
  customer_phone: '',
  customer_pec: '',
  customer_sdi_code: '',
  description: '',
  amount: 0,
  contributo_integrativo_applied: false,
  contributo_integrativo_amount: 0,
  net_amount: 0,
  is_paid: false,
  payment_date: '',
  payment_method: '',
  notes: '',
  items: [],
});

const loadingNumber = ref(false);
const applyContributo = ref(false);

// Calculated amount from items
const calculatedAmount = computed(() => {
  return parseFloat(
    form.items
      .reduce((sum, item) => sum + item.unit_price * item.quantity, 0)
      .toFixed(2),
  );
});

const calculatedContributo = computed(() => {
  return applyContributo.value
    ? parseFloat((calculatedAmount.value * 0.04).toFixed(2))
    : 0;
});

const calculatedNetAmount = computed(() => {
  return parseFloat(
    (calculatedAmount.value + calculatedContributo.value).toFixed(2),
  );
});

// Computed
const isEdit = computed(() => !!props.invoice?.id);

// Net amount equals full amount for regime forfettario (no withholding)

// Watch for form initialization
watch(
  () => props.invoice,
  (newInvoice) => {
    if (newInvoice) {
      Object.assign(form, {
        ...newInvoice,
        customer_email: newInvoice.customer_email || '',
        customer_vat_number: newInvoice.customer_vat_number || '',
        customer_tax_code: newInvoice.customer_tax_code || '',
        customer_address: newInvoice.customer_address || '',
        customer_city: newInvoice.customer_city || '',
        customer_province: newInvoice.customer_province || '',
        customer_postal_code: newInvoice.customer_postal_code || '',
        customer_phone: newInvoice.customer_phone || '',
        customer_pec: newInvoice.customer_pec || '',
        customer_sdi_code: newInvoice.customer_sdi_code || '',
        payment_date: newInvoice.payment_date || '',
        payment_method: newInvoice.payment_method || '',
        notes: newInvoice.notes || '',
        items:
          newInvoice.items && newInvoice.items.length > 0
            ? newInvoice.items
            : [],
      });
      // initialize contributo flag from invoice if present
      applyContributo.value = !!newInvoice.contributo_integrativo_applied;
      form.contributo_integrativo_amount =
        newInvoice.contributo_integrativo_amount ?? 0;
      // Ensure net_amount is a number (two decimals)
      const netValue = newInvoice.net_amount ?? newInvoice.amount ?? 0;
      form.net_amount = parseFloat(Number(netValue).toFixed(2));
    }
  },
  { immediate: true },
);

// Pre-select primary ATECO code
watch(
  () => props.atecoCodes,
  (codes) => {
    if (!form.ateco_code_id && codes.length > 0) {
      const primary = codes.find((c) => c.is_primary);
      form.ateco_code_id = primary?.id || codes[0].id;
    }
  },
  { immediate: true },
);

// No withholding logic for forfettario; net_amount mirrors amount

// Update calculations when items or contributo changes
watch(
  [() => form.items, applyContributo],
  () => {
    form.amount = calculatedAmount.value;
    form.contributo_integrativo_applied = applyContributo.value;
    form.contributo_integrativo_amount = calculatedContributo.value;
    form.net_amount = calculatedNetAmount.value;
  },
  { deep: true },
);

// Watch for customer selection
watch(
  () => form.customer_id,
  (customerId) => {
    handleCustomerSelect(customerId);
  },
);

// Handlers
const handleCustomerSelect = (customerId: number | null) => {
  if (customerId) {
    const customer = props.customers.find((c) => c.id === customerId);
    if (customer) {
      form.customer_business_name = customer.business_name;
      form.customer_email = customer.email || '';
      form.customer_vat_number = customer.vat_number || '';
      form.customer_tax_code = customer.tax_code || '';
      form.customer_address = customer.address || '';
      form.customer_city = customer.city || '';
      form.customer_province = customer.province || '';
      form.customer_postal_code = customer.postal_code || '';
      form.customer_phone = customer.phone || '';
      form.customer_pec = customer.pec || '';
      form.customer_sdi_code = customer.sdi_code || '';
    }
  }
};

const suggestInvoiceNumber = async () => {
  loadingNumber.value = true;
  try {
    const year = new Date(form.issue_date).getFullYear();
    const response = await fetch(`/invoices/next-number?year=${year}`);
    const data = await response.json();
    form.invoice_number = data.invoice_number;
  } catch (error) {
    console.error('Error fetching next invoice number:', error);
  } finally {
    loadingNumber.value = false;
  }
};

const addItem = () => {
  form.items.push({
    product_id: null,
    code: '',
    description: '',
    unit_price: 0,
    quantity: 1,
    total: 0,
  });
};

const removeItem = (index: number) => {
  form.items.splice(index, 1);
};

const updateItemTotal = (index: number) => {
  const item = form.items[index];
  item.total = parseFloat((item.unit_price * item.quantity).toFixed(2));
};

const onSubmit = () => {
  emit('submit', form);
};

const onCancel = () => {
  emit('cancel');
};

// Initialize with one empty item if creating new invoice
if (!props.invoice?.id && form.items.length === 0) {
  addItem();
}
</script>
