<script setup>
import { ref, computed, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
  Chart as ChartJS,
  Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler)

const props = defineProps({
  usuariosChart: Object,
  reservasChart: Object,
  filtro: String
})

// Estado activo (usuarios o reservas)
const vistaActiva = ref('usuarios')

// Filtro
const filtro = ref(props.filtro || '30days')
const fechaInicio = ref(null)
const fechaFin = ref(null)

const opcionesFiltro = [
  { label: 'Últimas 24 horas', value: '24hours' },
  { label: 'Últimos 7 días', value: '7days' },
  { label: 'Últimos 30 días', value: '30days' },
  { label: 'Últimos 3 meses', value: '3months' },
  { label: 'Últimos 12 meses', value: '12months' },
  { label: 'Mes actual', value: 'month' },
  { label: 'Trimestre actual', value: 'quarter' },
  { label: 'Año actual', value: 'year' },
  { label: 'Todo el tiempo', value: 'all' },
  { label: 'Personalizado', value: 'custom' }
]

// Calcular totales
const totalUsuarios = computed(() => {
  if (!props.usuariosChart?.datasets?.[0]?.data) return 0
  return props.usuariosChart.datasets[0].data.reduce((a, b) => a + b, 0)
})
const totalReservas = computed(() => {
  if (!props.reservasChart?.datasets?.[0]?.data) return 0
  return props.reservasChart.datasets[0].data.reduce((a, b) => a + b, 0)
})

// Opciones del gráfico
const opcionesGrafico = {
  responsive: true,
  maintainAspectRatio: false,
  animation: {
    duration: 2000,
    easing: 'easeInOutQuart'
  },
  plugins: {
    legend: { display: false },
    tooltip: {
      mode: 'index',
      intersect: false,
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 12,
      cornerRadius: 8,
      titleFont: { size: 14, weight: 'bold' },
      bodyFont: { size: 13 }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
      ticks: { font: { size: 11 }, color: '#6B7280' }
    },
    x: {
      grid: { display: false, drawBorder: false },
      ticks: { font: { size: 11 }, color: '#6B7280' }
    }
  },
  interaction: { mode: 'index', intersect: false },
  layout: {
    padding: { top: 40, right: 20, bottom: 20, left: 20 }
  }
}

// Graficos mejorados
const datosUsuarios = computed(() => {
  if (!props.usuariosChart) return null
  return {
    ...props.usuariosChart,
    datasets: props.usuariosChart.datasets.map(ds => ({
      ...ds,
      borderColor: '#8B5CF6',
      backgroundColor: 'rgba(139, 92, 246, 0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 6,
      pointBackgroundColor: '#8B5CF6',
      pointBorderColor: '#fff',
      pointBorderWidth: 2
    }))
  }
})
const datosReservas = computed(() => {
  if (!props.reservasChart) return null
  return {
    ...props.reservasChart,
    datasets: props.reservasChart.datasets.map(ds => ({
      ...ds,
      borderColor: '#3B82F6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointRadius: 3,
      pointHoverRadius: 6,
      pointBackgroundColor: '#3B82F6',
      pointBorderColor: '#fff',
      pointBorderWidth: 2
    }))
  }
})

function cambiarVista(vista) {
  vistaActiva.value = vista
}
function seleccionarFiltro(valor) {
  filtro.value = valor
  let params = { filtro: valor }
  if (valor === 'custom' && fechaInicio.value && fechaFin.value) {
    params.fecha_inicio = fechaInicio.value
    params.fecha_fin = fechaFin.value
  }
  router.get(route('admin.analisis'), params, { preserveState: true, preserveScroll: true })
}

// Si selecciona fechas personalizadas, recarga
watch([fechaInicio, fechaFin], ([ini, fin]) => {
  if (filtro.value === 'custom' && ini && fin) {
    seleccionarFiltro('custom')
  }
})

const tituloGrafico = computed(() => {
  return vistaActiva.value === 'usuarios' ? 'Usuarios Registrados' : 'Reservas Realizadas'
})
</script>

<template>
  <Head title="Análisis" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 py-6">
      <h1 class="text-2xl font-semibold mb-6 text-gray-900">Análisis</h1>
      
      <!-- Barra filtros -->
      <div class="mb-6">
        <div>
          <label class="font-semibold mb-2 block text-sm text-gray-700">Filtrar por</label>
          <select 
            v-model="filtro" 
            @change="seleccionarFiltro(filtro)"
            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option v-for="r in opcionesFiltro" :key="r.value" :value="r.value">{{ r.label }}</option>
          </select>
        </div>
        <div v-if="filtro === 'custom'" class="flex gap-2 mt-3 items-center">
          <input 
            type="date" 
            v-model="fechaInicio" 
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
          />
          <span class="text-gray-500">-</span>
          <input 
            type="date" 
            v-model="fechaFin" 
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
          />
        </div>
      </div>

      <!-- Tarjetas de métricas -->
      <div class="flex gap-0 mb-8">
        <button
          @click="cambiarVista('usuarios')"
          :class="[
            'bg-white border-r border-gray-200 p-5 text-left transition-all hover:bg-gray-50 flex-1',
            vistaActiva === 'usuarios' ? 'border-b-2 border-b-purple-500' : 'border-b-2 border-b-transparent'
          ]"
        >
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs text-gray-600 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-purple-500"></span>
              Usuarios Registrados
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
          <div class="text-2xl font-bold text-gray-900">{{ totalUsuarios }}</div>
        </button>
        <button
          @click="cambiarVista('reservas')"
          :class="[
            'bg-white p-5 text-left transition-all hover:bg-gray-50 flex-1',
            vistaActiva === 'reservas' ? 'border-b-2 border-b-blue-500' : 'border-b-2 border-b-transparent'
          ]"
        >
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs text-gray-600 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-blue-500"></span>
              Reservas Realizadas
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
          <div class="text-2xl font-bold text-gray-900">{{ totalReservas }}</div>
        </button>
      </div>

      <!-- Gráfico principal -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="relative" style="height: 400px;">
          <div class="absolute top-2 left-2 z-10">
            <h3 class="text-sm font-medium text-gray-600">{{ tituloGrafico }}</h3>
          </div>
          <Line
            v-if="vistaActiva === 'usuarios' && datosUsuarios"
            :data="datosUsuarios"
            :options="opcionesGrafico"
          />
          <Line
            v-else-if="vistaActiva === 'reservas' && datosReservas"
            :data="datosReservas"
            :options="opcionesGrafico"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
button:focus {
  outline: none;
}
</style>
