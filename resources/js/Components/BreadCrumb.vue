<template>
    <div class="inline-flex items-center">
      <template v-for="(item, index) in items" :key="index">
        <!-- Elemento del breadcrumb -->
        <div class="inline-flex items-center">
          <!-- Si tiene ruta, crear un Link -->
          <Link v-if="item.route"
                :href="item.route"
                class="inline-flex items-center hover:text-blue-600 dark:hover:text-white">
            <template v-if="item.formattedId">
              <FormatId :id="item.formattedId.id" :prefix="item.formattedId.prefix"></FormatId>
            </template>
            <template v-else>
              {{ item.text }}
            </template>
          </Link>

          <!-- Si no tiene ruta, mostrar como texto plano -->
          <template v-else>
            <template v-if="item.formattedId">
              <FormatId :id="item.formattedId.id" :prefix="item.formattedId.prefix"></FormatId>
            </template>
            <template v-else>
              {{ item.text }}
            </template>
          </template>
        </div>

        <!-- Agregar icono separador si no es el último elemento -->
        <ChevronRightIcon v-if="index < items.length - 1" class="size-5  mx-1" />
      </template>
    </div>
  </template>

  <script setup>
  import { Link } from '@inertiajs/vue3';
  import ChevronRightIcon from './Icons/ChevronRightIcon.vue';
  import FormatId from './FormatId.vue';

  defineProps({
    items: {
      type: Array,
      required: true,
      // Cada item puede tener:
      // - text: texto a mostrar (opcional si tiene formattedId)
      // - route: ruta para el Link (opcional)
      // - formattedId: { id, prefix } para usar con FormatId (opcional)
    }
  });
  </script>
