<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';

interface Product {
  id?: number;
  code: string | null;
  name: string;
  description: string | null;
  unit_price: number;
}

interface Props {
  product?: Product;
  submitRoute: string;
  submitMethod?: 'post' | 'put';
}

const props = withDefaults(defineProps<Props>(), {
  submitMethod: 'post',
});

const form = useForm({
  code: props.product?.code || '',
  name: props.product?.name || '',
  description: props.product?.description || '',
  unit_price: props.product?.unit_price || 0,
});

const submit = () => {
  if (props.submitMethod === 'put') {
    form.put(props.submitRoute);
  } else {
    form.post(props.submitRoute);
  }
};
</script>

<template>
  <form @submit.prevent="submit">
    <Card>
      <CardContent class="pt-6">
        <div class="space-y-4">
          <!-- Code -->
          <div>
            <Label for="code">Codice (opzionale)</Label>
            <Input
              id="code"
              v-model="form.code"
              type="text"
              placeholder="es. PROD-001"
            />
            <InputError :message="form.errors.code" />
          </div>

          <!-- Name -->
          <div>
            <Label for="name"
              >Nome <span class="text-destructive">*</span></Label
            >
            <Input
              id="name"
              v-model="form.name"
              type="text"
              required
              placeholder="es. Consulenza Web Development"
            />
            <InputError :message="form.errors.name" />
          </div>

          <!-- Description -->
          <div>
            <Label for="description">Descrizione</Label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              placeholder="Descrizione dettagliata del prodotto o servizio"
              class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            />
            <InputError :message="form.errors.description" />
          </div>

          <!-- Unit Price -->
          <div>
            <Label for="unit_price"
              >Prezzo Unitario <span class="text-destructive">*</span></Label
            >
            <Input
              id="unit_price"
              v-model.number="form.unit_price"
              type="number"
              step="0.01"
              min="0"
              required
              placeholder="0.00"
            />
            <InputError :message="form.errors.unit_price" />
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-2 pt-4">
            <Button
              type="button"
              variant="outline"
              @click="$inertia.visit('/products')"
            >
              Annulla
            </Button>
            <Button type="submit" :disabled="form.processing">
              {{ form.processing ? 'Salvataggio...' : 'Salva' }}
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>
  </form>
</template>
