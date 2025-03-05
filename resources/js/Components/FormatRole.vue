<template>
    <span>{{ formattedRole }}</span>
  </template>

  <script>
  export default {
    name: 'FormatRole',
    props: {
      role: {
        type: [String],
        required: true,
      }
    },
    computed: {
      formattedRole() {
        if (this.role === 'admin') {
            return 'Administrador';
        } else if (this.role === 'nurse') {
            return 'Enfermero';
        } else if (this.role === 'doctor') {
            return 'Doctor';
        } else if (this.role === 'receptionist') {
            return 'Recepcionista';
        } else {
            return this.role;
        };
      }
    }
  }
  </script>
