<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  stats: Object,
  actividadReciente: Array,
  auth: Object
});

const user = props.auth.user;

const getColorClass = (color) => {
  const classes = {
    blue: 'bg-blue-100 text-blue-600',
    green: 'bg-green-100 text-green-600',
    yellow: 'bg-yellow-100 text-yellow-600',
    red: 'bg-red-100 text-red-600'
  };
  return classes[color] || classes.blue;
};
</script>

<template>
  <Head title="Panel Admin" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">
          Panel Administrativo
        </h2>
        <div class="flex items-center space-x-2 bg-green-100 px-4 py-2 rounded-lg">
          <div class="relative">
            <span class="flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
          </div>
          <span class="text-sm font-semibold text-green-800">
            Usuarios activos
          </span>
        </div>
      </div>
    </template>
    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Bienvenida -->
        <div class="mb-8">
          <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-3xl font-bold mb-2">¡Bienvenido, {{ user.name }}!</h1>
                <p class="text-blue-100 text-lg mb-6">
                  Administra el sistema, revisa estadísticas y gestiona reservas desde aquí
                </p>
                <Link
                  :href="route('admin.reservas')"
                  class="inline-flex items-center space-x-2 bg-white text-blue-600 hover:bg-blue-50 rounded-lg font-semibold px-6 py-3 transition-colors shadow-sm"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  <span>Ver Solicitudes de Reserva</span>
                  <span v-if="props.stats.reservasPendientes > 0" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    {{ props.stats.reservasPendientes }}
                  </span>
                </Link>
              </div>
              <div class="hidden lg:block">
                <svg class="w-32 h-32 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Estadísticas principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-purple-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ props.stats.totalUsuarios }}</div>
            <div class="text-sm text-gray-600">Total de Usuarios</div>
          </div>
          <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-yellow-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ props.stats.reservasPendientes }}</div>
            <div class="text-sm text-gray-600">Reservas Pendientes</div>
            <div class="mt-2">
              <span class="text-xs text-yellow-600 font-semibold bg-yellow-50 px-2 py-1 rounded">
                Requieren atención
              </span>
            </div>
          </div>
          <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-blue-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ props.stats.reservasHoy }}</div>
            <div class="text-sm text-gray-600">Reservas Hoy</div>
            <div class="mt-2">
              <span class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded">
                {{ new Date().toLocaleDateString('es-PE', { weekday: 'long', day: 'numeric', month: 'long' }) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Grid de contenido: actividad reciente y accesos rápidos -->
        <div class="grid lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 bg-white rounded-xl shadow-md border border-gray-100">
            <div class="p-6 border-b border-gray-100">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="bg-indigo-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg font-bold text-gray-900">Actividad Reciente</h3>
                </div>
                <span class="text-xs text-gray-500">Tiempo real</span>
              </div>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div v-for="(actividad, index) in props.actividadReciente" :key="index"
                  class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <div :class="['p-2 rounded-lg', getColorClass(actividad.color)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        :d="actividad.icono ? actividad.icono : ''"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ actividad.usuario }}</p>
                    <p class="text-sm text-gray-600">{{ actividad.accion }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ actividad.tiempo }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
              <div class="flex items-center space-x-3 mb-4">
                <div class="bg-red-100 p-2 rounded-lg">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Accesos Rápidos</h3>
              </div>
              <div class="space-y-2">
                <!-- Acceso rápido Reservas -->
                <Link :href="route('admin.reservas')"
                  class="flex items-center justify-between p-3 rounded-lg border-2 border-gray-200 hover:border-blue-600 hover:bg-blue-50 transition-all group">
                  <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="font-semibold text-gray-900">Reservas</span>
                  </div>
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </Link>
                <!-- Acceso rápido Prendas -->
                <Link :href="route('admin.prendas.index')"
                  class="flex items-center justify-between p-3 rounded-lg border-2 border-gray-200 hover:border-purple-600 hover:bg-purple-50 transition-all group">
                  <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="font-semibold text-gray-900">Prendas</span>
                  </div>
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </Link>
                <!-- Nuevo acceso rápido Análisis -->
                <Link :href="route('admin.analisis')"
                  class="flex items-center justify-between p-3 rounded-lg border-2 border-gray-200 hover:border-green-600 hover:bg-green-50 transition-all group">
                  <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 8a6 6 0 00-12 0v1h18v-1a6 6 0 00-6-6zm-7 2a4 4 0 018 0v1h-8zm-1 1h10v6a4 4 0 01-4 4h-2a4 4 0 01-4-4v-6z"></path>
                    </svg>
                    <span class="font-semibold text-gray-900">Análisis</span>
                  </div>
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
