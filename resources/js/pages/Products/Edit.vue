<script setup lang="ts">
import ProductForm from '@/components/ProductForm.vue';
import {
  Card,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Product {
  id: number;
  code: string | null;
  name: string;
  description: string | null;
  unit_price: number;
}

interface Props {
  product: Product;
}

defineProps<Props>();
</script>

<template>
  <Head title="Modifica Prodotto" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <Card class="mb-6">
          <CardHeader>
            <CardTitle>Modifica Prodotto/Servizio</CardTitle>
            <CardDescription>
              Aggiorna le informazioni del prodotto "{{ product.name }}"
            </CardDescription>
          </CardHeader>
        </Card>

        <ProductForm
          :product="product"
          :submit-route="`/products/${product.id}`"
          submit-method="put"
        />
      </div>
    </div>
  </AppLayout>
</template>
