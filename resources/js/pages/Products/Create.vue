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
</script>

<template>
  <Head title="Nuovo Prodotto" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <Card class="mb-6">
          <CardHeader>
            <CardTitle>Nuovo Prodotto/Servizio</CardTitle>
            <CardDescription>
              Aggiungi un nuovo prodotto o servizio al tuo catalogo
            </CardDescription>
          </CardHeader>
        </Card>

        <ProductForm submit-route="/products" submit-method="post" />
      </div>
    </div>
  </AppLayout>
</template>
