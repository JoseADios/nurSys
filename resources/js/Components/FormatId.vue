<template>
    <span>{{ formattedId }}</span>
  </template>

  <script>
  export default {
    name: 'FormatoId',
    props: {
      id: {
        type: [Number, String],
        required: true
      },
      prefix: {
        type: String,
        required: true
      }
    },
    computed: {
      formattedId() {
        return `${this.prefix}-${this.id}`;
      }
    }
  }
  </script>
