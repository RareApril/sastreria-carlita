<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
    prenda: Object,
    fechasOcupadas: Array
})

const form = useForm({
    prenda_id: props.prenda.id,
    talla: props.prenda.talla,
    fecha_inicio: '',
    fecha_fin: ''
})

const today = new Date()
today.setHours(0,0,0,0)

const calendarMonths = ref([])
const rangeSelecting = ref('inicio')

// ------- Colores para indicadores -------
const COLORS = {
    disponible: '#fff',
    reservado: '#e85d55',
    mantenimiento: '#fbbf24' // Ambar (amarillo)
}

// Inicializar doble mes
const initCalendars = () => {
    const now = new Date()
    const currentMonth = now.getMonth()
    const currentYear = now.getFullYear()
    calendarMonths.value = [
        { year: currentYear, month: currentMonth },
        {
            year: (currentMonth === 11 ? currentYear + 1 : currentYear),
            month: (currentMonth + 1) % 12
        }
    ]
}

const getMonthInfo = (year, month) => {
    const date = new Date(year, month)
    const firstWeekday = (date.getDay() + 6) % 7
    const daysInMonth = new Date(year, month + 1, 0).getDate()
    return { firstWeekday, daysInMonth }
}

// ================ Indicadores visuales ================

// Saber si el día está dentro de una reserva
const isReservado = (y, m, d) => {
    const dStr = toStr(y, m, d)
    return props.fechasOcupadas.some(rango =>
        dStr >= rango.inicio && dStr <= rango.fin
    )
}

// Saber si está en mantenimiento (2 días después de cada fin de reserva)
const isMantenimiento = (y, m, d) => {
    const dStr = toStr(y, m, d)
    return props.fechasOcupadas.some(rango => {
        const finDate = new Date(rango.fin)
        for (let i = 1; i <= 2; i++) {
            const mantDate = new Date(finDate)
            mantDate.setDate(finDate.getDate() + i)
            if (dStr === toStr(mantDate.getFullYear(), mantDate.getMonth(), mantDate.getDate())) {
                return true
            }
        }
        return false
    })
}

// Bloquea para click
const isDisabledByReserva = (y, m, d) => {
    return isReservado(y, m, d) || isMantenimiento(y, m, d)
}

// Utilidad
const isPast = (y, m, d) => {
    const dt = new Date(y, m, d)
    dt.setHours(0,0,0,0)
    return dt < today
}

const toStr = (y, m, d) => `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`

const isStart = (y, m, d) => form.fecha_inicio === toStr(y, m, d)
const isEnd = (y, m, d) => form.fecha_fin === toStr(y, m, d)
const isInRange = (y, m, d) => {
    const t = toStr(y, m, d)
    return form.fecha_inicio && form.fecha_fin && (t > form.fecha_inicio && t < form.fecha_fin)
}
const isSelected = (y, m, d) => isStart(y, m, d) || isEnd(y, m, d) || isInRange(y, m, d)

// Día recibe fondo y color diferente por tipo
const dayStyle = (y, m, d) => {
    if (isReservado(y, m, d)) return { background: COLORS.reservado, color: "#fff", border: '2px solid #e85d55' }
    if (isMantenimiento(y, m, d)) return { background: COLORS.mantenimiento, color: "#fff", border: '2px solid #fbbf24' }
    return { background: COLORS.disponible }
}

const handleDayClick = (y, m, d, disabled) => {
    if (disabled) return
    const val = toStr(y, m, d)
    if (!form.fecha_inicio || (form.fecha_inicio && form.fecha_fin)) {
        form.fecha_inicio = val
        form.fecha_fin = ''
        rangeSelecting.value = 'fin'
    } else if (rangeSelecting.value === 'fin') {
        if (val > form.fecha_inicio) {
            form.fecha_fin = val
        } else {
            form.fecha_inicio = val
            form.fecha_fin = ''
        }
    }
}

const resetAll = () => {
    form.fecha_inicio = ''
    form.fecha_fin = ''
    rangeSelecting.value = 'inicio'
}

const submit = () => {
    form.post(route('reservas.store'))
}

onMounted(() => {
    initCalendars()
})
</script>

<template>
  <Head :title="`Reservar ${prenda.nombre}`" />
  <div class="min-h-screen py-10 flex flex-col items-center justify-center bg-gray-50">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold mb-6 text-center text-rose-500">Reservar {{ prenda.nombre }}</h2>
      <div class="mb-5 flex items-center justify-center">
        <span class="inline-block bg-rose-100 text-rose-600 px-8 py-1 rounded-full font-bold text-lg border border-rose-200 shadow">Talla: {{ prenda.talla }}</span>
      </div>

      <!-- Indicadores visuales (leyenda calendario) -->
      <div class="mb-6 flex items-center gap-7 justify-center">
        <span class="indicador ejemplo-disponible"><span class="color-circle disponible"></span> Día disponible</span>
        <span class="indicador ejemplo-reservado"><span class="color-circle reservado"></span> Día reservado</span>
        <span class="indicador ejemplo-mantenimiento"><span class="color-circle mantenimiento"></span> Día de mantenimiento</span>
      </div>

      <!-- Calendario Doble Mes -->
      <div id="calendar-container" style="display: flex; gap: 40px; justify-content: center; width: 100%; max-width: 700px; margin-bottom: 20px;">
        <div v-for="({ year, month }, idx) in calendarMonths" :key="idx" class="calendar-month">
          <div class="calendar-header">
            {{ new Date(year, month).toLocaleString('es-ES', { month: 'long', year: 'numeric' }) }}
          </div>
          <div class="calendar-grid">
            <div v-for="w in 7" :key="'wd'+w" class="weekday">
              {{ ['L','M','X','J','V','S','D'][w-1] }}
            </div>
            <template v-for="blank in getMonthInfo(year, month).firstWeekday">
              <div>&nbsp;</div>
            </template>
            <template v-for="d in getMonthInfo(year, month).daysInMonth">
              <div
                class="day"
                :class="{
                  disabled: isPast(year, month, d) || isDisabledByReserva(year, month, d),
                  selected: isSelected(year, month, d),
                  range: isInRange(year, month, d),
                  start: isStart(year, month, d),
                  end: isEnd(year, month, d)
                }"
                @click="handleDayClick(year, month, d, isPast(year, month, d) || isDisabledByReserva(year, month, d))"
                :data-date="toStr(year, month, d)"
                :style="dayStyle(year, month, d)"
                :title="isMantenimiento(year, month, d) ? 'Día de mantenimiento, no disponible para reservas.' : (isReservado(year, month, d) ? 'Día reservado por otro cliente.' : (isPast(year, month, d) ? 'No disponible: fecha pasada.' : 'Día disponible para reserva.'))"
              >{{ d }}</div>
            </template>
          </div>
        </div>
      </div>
      <form class="mt-8" @submit.prevent="submit">
        <input type="hidden" v-model="form.fecha_inicio" />
        <input type="hidden" v-model="form.fecha_fin" />
        <div class="mb-4 text-center min-h-[28px]">
          <span v-if="!form.fecha_inicio" class="text-gray-400">Selecciona la fecha de inicio</span>
          <span v-else-if="!form.fecha_fin" class="text-rose-600">Selecciona la fecha de fin</span>
          <span v-else>
            Fechas:
            <span class="font-bold text-rose-500">{{ form.fecha_inicio }}</span>
            <span class="text-gray-600">→</span>
            <span class="font-bold text-rose-500">{{ form.fecha_fin }}</span>
          </span>
        </div>
        <div class="flex justify-end mb-2">
          <button type="button" class="text-gray-400 hover:text-rose-500 text-sm font-semibold" @click="resetAll">
            Limpiar selección
          </button>
        </div>
        <button
          type="submit"
          :disabled="form.processing || !form.fecha_inicio || !form.fecha_fin"
          class="w-full py-3 rounded-lg font-bold text-lg bg-rose-600 hover:bg-rose-700 text-white shadow mt-2 transition disabled:bg-rose-200 disabled:text-white"
        >
          Reservar
        </button>
      </form>
    </div>
  </div>
</template>

<style>
.indicador {
    display: flex;
    align-items: center;
    font-size: 1rem;
    gap: 7px;
    font-weight: 500;
}
.color-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #c3c3c3;
    display: inline-block;
}
.color-circle.disponible { background: #fff; border-color: #bababa; }
.color-circle.reservado { background: #e85d55; border-color: #e85d55; }
.color-circle.mantenimiento { background: #fbbf24; border-color: #fbbf24; }

#calendar-container {
    display: flex;
    gap: 40px;
    justify-content: center;
    width: 100%;
    max-width: 700px;
    margin-bottom: 20px;
}
.calendar-month {
    width: 300px;
}
.calendar-header {
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 15px;
    text-transform: capitalize;
    color: #d94673;
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}
.calendar-grid div {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 40px;
    border-radius: 50%;
    color: #e85d55;
    background: #fff;
}
.weekday {
    font-weight: bold;
    font-size: 0.8rem;
    color: #e85d55;
    background: none;
}
.day {
    cursor: pointer;
    transition: background-color 0.2s, color 0.15s;
}
.day:not(.disabled):hover {
    background-color: #fee2e2;
    color: #d94673 !important;
}
.day.disabled {
    color: #d2d2d2 !important;
    background: #fff !important;
    cursor: not-allowed;
    border: none;
}
.day.selected, .day.start, .day.end {
    background-color: #e85d55 !important;
    color: #fff !important;
    border: 2px solid #e85d55;
}
.day.range {
    background: rgba(236, 72, 153, 0.07);
    color: #e85d55;
}
@media (max-width: 768px) {
    #calendar-container {
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }
    .calendar-month { width: 100%; min-width: 270px; }
}
</style>
