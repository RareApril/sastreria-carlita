<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user
const reservas = page.props.reservas || []

const reservasActivas = reservas.filter(r => r.estado !== 'cancelada')
const reservasAprobadas = reservas.filter(r => r.estado === 'aprobada')
const reservasPendientes = reservas.filter(r => r.estado === 'pendiente')

const getEstadoBadgeClass = (estado) => {
  const classes = {
    'pendiente': 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'aprobada': 'bg-green-100 text-green-800 border-green-200',
    'rechazada': 'bg-red-100 text-red-800 border-red-200'
  }
  return classes[estado] || 'bg-gray-100 text-gray-800 border-gray-200'
}
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">
          Dashboard
        </h2>
        <Link href="/catalogo"
          class="bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold px-6 py-2.5 transition-colors shadow-sm inline-flex items-center space-x-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <span>Explorar Catálogo</span>
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Bienvenida con estadísticas -->
        <div class="mb-8">
          <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-3xl font-bold mb-2">¡Bienvenido, {{ user.name }}!</h1>
                <p class="text-red-100 text-lg">Gestiona tus reservas y encuentra el terno perfecto</p>
              </div>
              <div class="hidden md:flex space-x-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 text-center">
                  <div class="text-3xl font-bold">{{ reservasActivas.length }}</div>
                  <div class="text-sm text-red-100">Reserva{{ reservasActivas.length !== 1 ? 's' : '' }} activa{{ reservasActivas.length !== 1 ? 's' : '' }}</div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 text-center">
                  <div class="text-3xl font-bold">{{ reservasAprobadas.length }}</div>
                  <div class="text-sm text-red-100">Aprobada{{ reservasAprobadas.length !== 1 ? 's' : '' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid de contenido -->
        <div class="grid lg:grid-cols-3 gap-6 mb-8">
          <!-- Estadísticas rápidas móvil -->
          <div class="lg:hidden grid grid-cols-2 gap-4 col-span-full">
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-100">
              <div class="text-2xl font-bold text-gray-900">{{ reservasActivas.length }}</div>
              <div class="text-sm text-gray-600">Reservas activas</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-100">
              <div class="text-2xl font-bold text-green-600">{{ reservasAprobadas.length }}</div>
              <div class="text-sm text-gray-600">Aprobadas</div>
            </div>
          </div>

          <!-- Mi Perfil -->
          <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center space-x-3 mb-4">
              <div class="bg-red-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900">Mi Perfil</h3>
            </div>
            <div class="space-y-3">
              <div>
                <span class="text-xs text-gray-500 uppercase tracking-wide">Nombre</span>
                <p class="text-gray-900 font-medium">{{ user.name }}</p>
              </div>
              <div>
                <span class="text-xs text-gray-500 uppercase tracking-wide">Email</span>
                <p class="text-gray-900 font-medium break-all">{{ user.email }}</p>
              </div>
              <div>
                <span class="text-xs text-gray-500 uppercase tracking-wide">Miembro desde</span>
                <p class="text-gray-900 font-medium">{{ new Date(user.created_at).toLocaleDateString('es-PE', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
              </div>
            </div>
          </div>

          <!-- Accesos rápidos -->
          <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
              <div class="bg-blue-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900">Accesos Rápidos</h3>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
              <Link href="/catalogo" class="group border-2 border-gray-200 hover:border-red-600 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center space-x-3">
                  <div class="bg-red-50 group-hover:bg-red-100 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900">Ver Catálogo</div>
                    <div class="text-sm text-gray-600">Explora ternos disponibles</div>
                  </div>
                </div>
              </Link>

              <a href="https://wa.me/51992073032" target="_blank" class="group border-2 border-gray-200 hover:border-green-600 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center space-x-3">
                  <div class="bg-green-50 group-hover:bg-green-100 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900">WhatsApp</div>
                    <div class="text-sm text-gray-600">Contáctanos directamente</div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>

        <!-- Mis Reservas -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100">
          <div class="p-6 border-b border-gray-100">
            <div class="flex items-center space-x-3">
              <div class="bg-purple-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Mis Reservas</h3>
                <p class="text-sm text-gray-600">Gestiona y revisa el estado de tus reservas</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            <div v-if="reservasActivas.length > 0" class="space-y-4">
              <div v-for="reserva in reservasActivas" :key="reserva.id" 
                   class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                  <!-- Info principal -->
                  <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                      <span class="font-mono font-bold text-gray-900 text-lg">{{ reserva.codigo }}</span>
                      <span :class="['px-3 py-1 rounded-full text-xs font-semibold border', getEstadoBadgeClass(reserva.estado)]">
                        {{ reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1) }}
                      </span>
                    </div>
                    
                    <div class="grid sm:grid-cols-2 gap-3 mb-3">
                      <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span><strong>Inicio:</strong> {{ reserva.fecha_inicio }}</span>
                      </div>
                      <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span><strong>Fin:</strong> {{ reserva.fecha_fin }}</span>
                      </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3">
                      <div class="text-sm font-semibold text-gray-700 mb-2">Prendas reservadas:</div>
                      <ul class="space-y-1">
                        <li v-for="detalle in reserva.detalle_reservas" :key="detalle.id" 
                            class="text-sm text-gray-600 flex items-center space-x-2">
                          <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                          <span>{{ detalle.prenda?.nombre }} - Talla <strong>{{ detalle.talla }}</strong></span>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!-- Acciones -->
                  <div class="flex flex-col space-y-2 lg:w-48">
                    <div v-if="reserva.estado === 'aprobada'" class="bg-green-50 border border-green-200 rounded-lg p-3">
                      <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-xs text-green-800">
                          <div class="font-semibold mb-1">¡Aprobada!</div>
                          <p>Comunícate al WhatsApp para coordinar el recojo.</p>
                        </div>
                      </div>
                      <a href="https://wa.me/51992073032" target="_blank" 
                         class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-3 rounded-lg transition-colors inline-flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>Contactar</span>
                      </a>
                    </div>

                    <form v-if="reserva.estado === 'pendiente'" :action="route('reservas.cancelar', reserva.id)" method="POST">
                      <input type="hidden" name="_token" :value="page.props.csrf_token">
                      <button type="submit" 
                              class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2 px-4 rounded-lg transition-colors border border-red-200">
                        Cancelar Reserva
                      </button>
                    </form>

                    <div v-if="reserva.estado === 'rechazada'" class="bg-red-50 border border-red-200 rounded-lg p-3">
                      <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <div class="text-xs text-red-800">
                          <div class="font-semibold">Rechazada</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-12">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">No tienes reservas activas</h3>
              <p class="text-gray-600 mb-6">Explora nuestro catálogo y reserva el terno perfecto para tu ocasión</p>
              <Link href="/catalogo"
                class="inline-flex items-center space-x-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold px-6 py-3 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Ver Catálogo</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>