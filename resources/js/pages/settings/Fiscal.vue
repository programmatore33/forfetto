<script setup lang="ts">
import { computed } from 'vue';

import { Head, useForm } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface AtecoCode {
  id: number;
  ateco_code: string;
  description: string;
  profitability_coeff: string;
  is_primary: boolean;
}

interface ProfessionalFundOption {
  value: string;
  label: string;
}

interface SettingsPayload {
  ateco_code_id: number | null;
  invoice_number_format: string;
  professional_fund: string | null;
  reduced_contributions: boolean;
  startup_rate: boolean;
}

interface Props {
  settings: SettingsPayload;
  atecoCodes: AtecoCode[];
  professionalFunds: ProfessionalFundOption[];
  patternPreview: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Fiscal settings',
    href: '/settings/fiscal',
  },
];

const form = useForm<SettingsPayload>({
  ateco_code_id: props.settings?.ateco_code_id ?? null,
  invoice_number_format:
    props.settings?.invoice_number_format ?? '{year}/{seq:3}',
  professional_fund: props.settings?.professional_fund ?? null,
  reduced_contributions: props.settings?.reduced_contributions ?? false,
  startup_rate: props.settings?.startup_rate ?? false,
});

const formatPreview = (pattern: string): string => {
  const year = new Date().getFullYear();
  const paddingMatch = pattern.match(/\{seq:(?<padding>[2-6])\}/);
  const padding = paddingMatch?.groups?.padding
    ? Number(paddingMatch.groups.padding)
    : 3;
  const seq = String(1).padStart(padding, '0');

  return pattern
    .replace(/\{year\}/g, String(year))
    .replace(/\{seq(?::[2-6])?\}/g, seq);
};

const preview = computed(() =>
  formatPreview(form.invoice_number_format || '{year}/{seq:3}'),
);

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      ateco_code_id: data.ateco_code_id || null,
      professional_fund: data.professional_fund || null,
    }))
    .put('/settings/fiscal');
};

const atecoForm = useForm({
  ateco_code: '',
  description: '',
  profitability_coeff: '',
  is_primary: false,
});

const submitAteco = () => {
  atecoForm.post('/settings/fiscal/ateco', {
    onSuccess: () =>
      atecoForm.reset(
        'ateco_code',
        'description',
        'profitability_coeff',
        'is_primary',
      ),
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Impostazioni fiscali" />

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          title="Impostazioni fiscali"
          description="Configura ATECO, formato numerazione e cassa previdenziale"
        />

        <form class="space-y-6" @submit.prevent="submit">
          <div class="space-y-4 rounded-lg border border-muted p-4">
            <HeadingSmall
              title="Nuovo codice ATECO"
              description="Aggiungi un codice ATECO al tuo profilo"
            />

            <div class="grid gap-3 md:grid-cols-2">
              <div class="space-y-2">
                <Label for="ateco_code">Codice</Label>
                <Input
                  id="ateco_code"
                  name="ateco_code"
                  v-model="atecoForm.ateco_code"
                  placeholder="62.01.00"
                />
                <InputError
                  class="mt-2"
                  :message="atecoForm.errors.ateco_code"
                />
              </div>

              <div class="space-y-2">
                <Label for="profitability_coeff"
                  >Coefficiente di redditività</Label
                >
                <Input
                  id="profitability_coeff"
                  name="profitability_coeff"
                  type="number"
                  step="0.01"
                  min="0"
                  max="100"
                  v-model="atecoForm.profitability_coeff"
                  placeholder="78.00"
                />
                <InputError
                  class="mt-2"
                  :message="atecoForm.errors.profitability_coeff"
                />
              </div>
            </div>

            <div class="space-y-2">
              <Label for="description">Descrizione</Label>
              <Input
                id="description"
                name="description"
                v-model="atecoForm.description"
                placeholder="Sviluppo di software"
              />
              <InputError
                class="mt-2"
                :message="atecoForm.errors.description"
              />
            </div>

            <label class="flex items-center gap-3 text-sm text-foreground">
              <Checkbox
                name="is_primary"
                :checked="atecoForm.is_primary"
                @update:checked="
                  (value: boolean) => (atecoForm.is_primary = value)
                "
              />
              Imposta come primario
            </label>
            <InputError class="mt-1" :message="atecoForm.errors.is_primary" />

            <div>
              <Button
                :disabled="atecoForm.processing"
                type="button"
                @click="submitAteco"
              >
                Aggiungi ATECO
              </Button>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="ateco">Codice ATECO predefinito</Label>
            <select
              id="ateco"
              name="ateco_code_id"
              v-model="form.ateco_code_id"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none"
            >
              <option :value="''">Seleziona un ATECO</option>
              <option
                v-for="code in atecoCodes"
                :key="code.id"
                :value="code.id"
              >
                {{ code.ateco_code }} - {{ code.description }}
                <span v-if="code.is_primary">(Primario)</span>
              </option>
            </select>
            <InputError class="mt-2" :message="form.errors.ateco_code_id" />
          </div>

          <div class="space-y-2">
            <Label for="invoice_number_format"
              >Formato numerazione fatture</Label
            >
            <Input
              id="invoice_number_format"
              name="invoice_number_format"
              v-model="form.invoice_number_format"
              placeholder="{year}/{seq:3}"
            />
            <p class="text-sm text-muted-foreground">
              Usa {year} per l'anno corrente e {seq} o {seq:N} (N=2..6) per la
              sequenza. Altri caratteri consentiti: lettere, numeri, -, _, /, .
            </p>
            <p class="text-sm text-muted-foreground">
              Anteprima: {{ preview }}
            </p>
            <InputError
              class="mt-2"
              :message="form.errors.invoice_number_format"
            />
          </div>

          <div class="space-y-2">
            <Label for="professional_fund">Cassa previdenziale</Label>
            <select
              id="professional_fund"
              name="professional_fund"
              v-model="form.professional_fund"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none"
            >
              <option :value="''">Seleziona la tua cassa</option>
              <option
                v-for="fund in professionalFunds"
                :key="fund.value"
                :value="fund.value"
              >
                {{ fund.label }}
              </option>
            </select>
            <InputError class="mt-2" :message="form.errors.professional_fund" />
          </div>

          <div class="flex flex-col gap-2">
            <label class="flex items-center gap-3 text-sm text-foreground">
              <Checkbox
                name="reduced_contributions"
                :checked="form.reduced_contributions"
                @update:checked="
                  (value: boolean) => (form.reduced_contributions = value)
                "
              />
              Contributi ridotti disponibili per la cassa
            </label>
            <InputError
              class="mt-1"
              :message="form.errors.reduced_contributions"
            />
          </div>

          <div class="flex flex-col gap-2">
            <label class="flex items-center gap-3 text-sm text-foreground">
              <Checkbox
                name="startup_rate"
                :checked="form.startup_rate"
                @update:checked="
                  (value: boolean) => (form.startup_rate = value)
                "
              />
              Regime start-up (imposta sostitutiva 5%)
            </label>
            <InputError class="mt-1" :message="form.errors.startup_rate" />
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="form.processing" type="submit">Salva</Button>
            <p v-if="patternPreview" class="text-sm text-muted-foreground">
              Formato attuale: {{ patternPreview }}
            </p>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
