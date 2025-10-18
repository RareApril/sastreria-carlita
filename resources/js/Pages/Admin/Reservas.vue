<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const user = page.props.auth.user;
const reservas = page.props.reservas || [];
const csrf = page.props.csrf_token;

const filtroEstado = ref('todas');

const reservasFiltradas = computed(() => {
  if (filtroEstado.value === 'todas') return reservas;
  return reservas.filter(r => r.estado === filtroEstado.value);
});

const estadisticas = computed(() => ({
  total: reservas.length,
  pendientes: reservas.filter(r => r.estado === 'pendiente').length,
  aprobadas: reservas.filter(r => r.estado === 'aprobada').length,
  rechazadas: reservas.filter(r => r.estado === 'rechazada').length,
  canceladas: reservas.filter(r => r.estado === 'cancelada').length
}));

const getEstadoBadgeClass = (estado) => {
  const classes = {
    'pendiente': 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'aprobada': 'bg-green-100 text-green-800 border-green-200',
    'rechazada': 'bg-red-100 text-red-800 border-red-200',
    'cancelada': 'bg-gray-100 text-gray-800 border-gray-200'
  };
  return classes[estado] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const formatearFecha = (fecha) => {
  return new Date(fecha).toLocaleDateString('es-PE', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};
</script>

<template>
  <Head title="Gestión de Reservas" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Gestión de Reservas</h2>
          <p class="text-sm text-gray-600 mt-1">Administra y controla todas las solicitudes de reserva</p>
        </div>
        <Link :href="route('admin.dashboard')"
              class="text-gray-600 hover:text-gray-900 font-medium inline-flex items-center space-x-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          <span>Volver al Dashboard</span>
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
          <button @click="filtroEstado = 'todas'"
                  :class="[
                    filtroEstado === 'todas' ? 'ring-2 ring-blue-500 bg-blue-50' : 'bg-white hover:shadow-md',
                    'p-4 rounded-xl shadow border border-gray-200 transition-all text-left'
                  ]">
            <div class="text-2xl font-bold text-gray-900">{{ estadisticas.total }}</div>
            <div class="text-sm text-gray-600">Total</div>
          </button>

          <button @click="filtroEstado = 'pendiente'"
                  :class="[
                    filtroEstado === 'pendiente' ? 'ring-2 ring-yellow-500 bg-yellow-50' : 'bg-white hover:shadow-md',
                    'p-4 rounded-xl shadow border border-gray-200 transition-all text-left'
                  ]">
            <div class="text-2xl font-bold text-yellow-600">{{ estadisticas.pendientes }}</div>
            <div class="text-sm text-gray-600">Pendientes</div>
          </button>

          <button @click="filtroEstado = 'aprobada'"
                  :class="[
                    filtroEstado === 'aprobada' ? 'ring-2 ring-green-500 bg-green-50' : 'bg-white hover:shadow-md',
                    'p-4 rounded-xl shadow border border-gray-200 transition-all text-left'
                  ]">
            <div class="text-2xl font-bold text-green-600">{{ estadisticas.aprobadas }}</div>
            <div class="text-sm text-gray-600">Aprobadas</div>
          </button>

          <button @click="filtroEstado = 'rechazada'"
                  :class="[
                    filtroEstado === 'rechazada' ? 'ring-2 ring-red-500 bg-red-50' : 'bg-white hover:shadow-md',
                    'p-4 rounded-xl shadow border border-gray-200 transition-all text-left'
                  ]">
            <div class="text-2xl font-bold text-red-600">{{ estadisticas.rechazadas }}</div>
            <div class="text-sm text-gray-600">Rechazadas</div>
          </button>

          <button @click="filtroEstado = 'cancelada'"
                  :class="[
                    filtroEstado === 'cancelada' ? 'ring-2 ring-gray-500 bg-gray-50' : 'bg-white hover:shadow-md',
                    'p-4 rounded-xl shadow border border-gray-200 transition-all text-left'
                  ]">
            <div class="text-2xl font-bold text-gray-600">{{ estadisticas.canceladas }}</div>
            <div class="text-sm text-gray-600">Canceladas</div>
          </button>
        </div>

        <!-- Tabla de Reservas -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900">
                    {{ filtroEstado === 'todas' ? 'Todas las Reservas' : `Reservas ${filtroEstado.charAt(0).toUpperCase() + filtroEstado.slice(1)}s` }}
                  </h3>
                  <p class="text-sm text-gray-600">{{ reservasFiltradas.length }} reserva(s)</p>
                </div>
              </div>
            </div>
          </div>

          <div v-if="reservasFiltradas.length > 0" class="overflow-x-auto">
            <div class="p-6 space-y-4">
              <div v-for="reserva in reservasFiltradas" 
                   :key="reserva.id" 
                   class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all">
                
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                  <!-- Información principal -->
                  <div class="flex-1 space-y-3">
                    <!-- Usuario y código -->
                    <div class="flex items-start justify-between">
                      <div>
                        <div class="flex items-center space-x-3 mb-2">
                          <div class="bg-gray-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                          </div>
                          <div>
                            <div class="font-semibold text-gray-900">{{ reserva.user?.name }}</div>
                            <div class="text-sm text-gray-600">{{ reserva.user?.email }}</div>
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span class="font-mono font-bold text-gray-900 text-sm bg-gray-100 px-3 py-1 rounded-lg">
                          {{ reserva.codigo }}
                        </span>
                        <span :class="['px-3 py-1 rounded-full text-xs font-semibold border', getEstadoBadgeClass(reserva.estado)]">
                          {{ reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1) }}
                        </span>
                      </div>
                    </div>

                    <!-- Fechas -->
                    <div class="grid sm:grid-cols-2 gap-3 bg-gray-50 rounded-lg p-3">
                      <div class="flex items-center space-x-2 text-sm">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600">Inicio:</span>
                        <span class="font-semibold text-gray-900">{{ formatearFecha(reserva.fecha_inicio) }}</span>
                      </div>
                      <div class="flex items-center space-x-2 text-sm">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600">Fin:</span>
                        <span class="font-semibold text-gray-900">{{ formatearFecha(reserva.fecha_fin) }}</span>
                      </div>
                    </div>

                    <!-- Prendas -->
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                      <div class="text-sm font-semibold text-blue-900 mb-2 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Prendas reservadas:</span>
                      </div>
                      <ul class="space-y-1">
                        <li v-for="detalle in reserva.detalle_reservas" 
                            :key="detalle.id" 
                            class="text-sm text-blue-800 flex items-center space-x-2">
                          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                          <span>{{ detalle.prenda?.nombre }} - Talla <strong>{{ detalle.talla }}</strong></span>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!-- Acciones -->
                  <div v-if="reserva.estado === 'pendiente'" class="flex flex-col space-y-2 lg:w-48">
                    <form :action="route('admin.reservas.aprobar', reserva.id)" method="POST">
                      <input type="hidden" name="_token" :value="csrf">
                      <button type="submit" 
                              class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors inline-flex items-center justify-center space-x-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Aprobar</span>
                      </button>
                    </form>

                    <form :action="route('admin.reservas.rechazar', reserva.id)" method="POST">
                      <input type="hidden" name="_token" :value="csrf">
                      <button type="submit" 
                              class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 px-4 rounded-lg transition-colors inline-flex items-center justify-center space-x-2 border-2 border-red-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Rechazar</span>
                      </button>
                    </form>
                  </div>

                  <div v-else-if="reserva.estado === 'aprobada'" class="lg:w-48">
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-3 text-center">
                      <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <div class="text-sm font-semibold text-green-800">Aprobada</div>
                    </div>
                  </div>

                  <div v-else-if="reserva.estado === 'rechazada'" class="lg:w-48">
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-3 text-center">
                      <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <div class="text-sm font-semibold text-red-800">Rechazada</div>
                    </div>
                  </div>

                  <div v-else-if="reserva.estado === 'cancelada'" class="lg:w-48">
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-3 text-center">
                      <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                      <div class="text-sm font-semibold text-gray-800">Cancelada</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              No hay reservas {{ filtroEstado !== 'todas' ? filtroEstado + 's' : '' }}
            </h3>
            <p class="text-gray-600 mb-4">
              {{ filtroEstado === 'todas' ? 'Aún no se han registrado reservas en el sistema' : `No hay reservas con el estado "${filtroEstado}"` }}
            </p>
            <button v-if="filtroEstado !== 'todas'" 
                    @click="filtroEstado = 'todas'"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
              Ver todas las reservas
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>