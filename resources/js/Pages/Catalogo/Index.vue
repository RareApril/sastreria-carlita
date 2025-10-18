<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    prendas: Object,
    categorias: Array,
    tallas: Array,
    colores: Array,
    precio_max: Number,
    precio_min: Number,
    filtros: Object
})

const page = usePage();
const user = computed(() => page.props.auth?.user || null);
const isLoggedIn = computed(() => !!user.value);

const filtrosActivos = ref({ ...{
    buscar: '', categoria: '', genero: 'todos', talla: '', color: '',
    precio_min: props.precio_min, precio_max: props.precio_max, sort: 'created_at'
}, ...props.filtros })

const mostrarFiltros = ref(false)
const vistaGrid = ref(true)

const aplicarFiltros = () => {
    router.get('/catalogo', filtrosActivos.value, { preserveState: true, preserveScroll: true })
}

const limpiarFiltros = () => {
    filtrosActivos.value = {
        buscar: '', categoria: '', genero: 'todos', talla: '', color: '',
        precio_min: props.precio_min, precio_max: props.precio_max, sort: 'created_at'
    }
    aplicarFiltros()
}

const formatearPrecio = precio => new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(precio)
const obtenerImagenUrl = imagen => imagen ? `/prendas/${imagen.trim()}` : '/images/placeholder-prenda.jpg'
</script>

<template>
    <Head title="Catálogo - Vestimenta Formal" />
    <div class="min-h-screen bg-white">
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <Link href="/" class="flex items-center space-x-3">
                    <img src="/logo.png" alt="Logo" class="h-12 w-12 rounded-full shadow bg-white ring-2 ring-red-500" />
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Sastrería Carlita</h1>
                        <p class="text-sm text-gray-600">Confecciones a medida</p>
                    </div>
                </Link>
                <div class="md:flex items-center space-x-6 hidden">
                    <Link href="/" class="text-gray-700 hover:text-red-600 font-medium">Inicio</Link>
                    <Link href="/catalogo" class="text-red-600 font-semibold">Catálogo</Link>
                </div>
                <div class="flex items-center space-x-4">
                    <template v-if="isLoggedIn">
                        <Link :href="route('dashboard')" class="text-gray-700 hover:text-red-600 font-medium">
                            {{ user.name }}
                        </Link>
                        <Link :href="route('logout')" method="post" as="button"
                              class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            Cerrar Sesión
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="text-gray-700 hover:text-red-600 font-medium">Iniciar Sesión</Link>
                        <Link :href="route('register')"
                              class="bg-red-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700">Registrarse</Link>
                    </template>
                </div>
            </div>
        </header>

        <div class="bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Catálogo de Ternos</h1>
                        <p class="text-xl text-gray-600">Encuentra el terno perfecto para tu ocasión especial</p>
                    </div>
                    <div class="mt-6 lg:mt-0 flex items-center space-x-4">
                        <input 
                            type="text" 
                            v-model="filtrosActivos.buscar" 
                            @input="aplicarFiltros"
                            placeholder="Buscar ternos..." 
                            class="border border-gray-300 rounded-lg px-4 py-2 w-64 focus:ring-red-500 focus:border-red-500"
                        />
                        <button @click="mostrarFiltros = !mostrarFiltros" 
                                class="lg:hidden bg-white text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 shadow-sm border border-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span>Filtros</span>
                        </button>
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button @click="vistaGrid = true" :class="[vistaGrid ? 'bg-white shadow-sm' : '', 'p-2 rounded-md']">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button @click="vistaGrid = false" :class="[!vistaGrid ? 'bg-white shadow-sm' : '', 'p-2 rounded-md']">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex gap-8">
                <!-- Sidebar de filtros -->
                <aside :class="[mostrarFiltros ? 'block' : 'hidden lg:block', 'w-64 flex-shrink-0']">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
                            <button @click="limpiarFiltros"
                                    class="text-sm text-red-600 hover:text-red-700 font-medium">Limpiar</button>
                        </div>
                        <!-- Categoría -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                            <select v-model="filtrosActivos.categoria" @change="aplicarFiltros"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Todas</option>
                                <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                            </select>
                        </div>
                        <!-- Talla -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Talla</label>
                            <select v-model="filtrosActivos.talla" @change="aplicarFiltros"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Todas</option>
                                <option v-for="t in tallas" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <!-- Color -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                            <select v-model="filtrosActivos.color" @change="aplicarFiltros"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Todos</option>
                                <option v-for="c in colores" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </div>
                        <!-- Precio -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Precio: {{ formatearPrecio(filtrosActivos.precio_min) }} - {{ formatearPrecio(filtrosActivos.precio_max) }}
                            </label>
                            <div class="space-y-3">
                                <input type="range" :min="precio_min" :max="precio_max" 
                                       v-model="filtrosActivos.precio_min" @change="aplicarFiltros" 
                                       class="w-full accent-red-600" />
                                <input type="range" :min="precio_min" :max="precio_max" 
                                       v-model="filtrosActivos.precio_max" @change="aplicarFiltros" 
                                       class="w-full accent-red-600" />
                            </div>
                        </div>
                    </div>
                </aside>

                <main class="flex-1">
                    <div v-if="prendas.data && prendas.data.length > 0" :class="vistaGrid ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-4'">
                        <div v-for="prenda in prendas.data" :key="prenda.id"
                             :class="[
                                vistaGrid 
                                    ? 'flex flex-col overflow-hidden' 
                                    : 'flex flex-row',
                                'bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100'
                             ]">
                            <div :class="[
                                vistaGrid ? 'relative bg-gray-100' : 'relative bg-gray-100 w-48 flex-shrink-0'
                            ]">
                                <img :src="obtenerImagenUrl(prenda.imagen)" 
                                     :alt="prenda.nombre" 
                                     :class="[
                                        vistaGrid ? 'w-full h-80' : 'w-full h-48',
                                        'object-contain transition-transform duration-300 hover:scale-105'
                                     ]" />
                                <div class="absolute top-3 right-3">
                                    <span :class="[
                                        prenda.stock > 3 ? 'bg-green-500' : prenda.stock > 0 ? 'bg-yellow-500' : 'bg-red-500',
                                        'text-white px-2 py-1 rounded text-xs font-medium'
                                    ]">
                                        {{ prenda.stock > 0 ? `${prenda.stock} disponible${prenda.stock !== 1 ? 's' : ''}` : 'Sin stock' }}
                                    </span>
                                </div>
                            </div>
                            <div :class="[
                                vistaGrid ? 'p-4 flex flex-col flex-1' : 'p-4 flex flex-col flex-1 justify-between'
                            ]">
                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ prenda.nombre }}</h3>
                                <p class="text-sm text-gray-500 mb-2">{{ prenda.marca || 'Sin marca' }}</p>
                                <div class="mb-2">
                                    <span class="text-sm text-gray-500">Talla: </span>
                                    <span class="text-sm font-medium text-gray-700">{{ prenda.talla || '-' }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-sm text-gray-500">Color: </span>
                                    <span class="text-sm font-medium text-gray-700">{{ prenda.color || '-' }}</span>
                                </div>
                                <div class="mt-auto">
                                    <span class="text-xl font-bold text-red-600">{{ formatearPrecio(prenda.precio_alquiler) }}</span>
                                    <span class="text-sm text-gray-500 ml-1">/día</span>
                                </div>
                                <Link v-if="isLoggedIn && prenda.stock > 0"
                                      :href="`/reservar/${prenda.id}`"
                                      class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition-colors text-center">
                                    Reservar
                                </Link>
                                <span v-else-if="!isLoggedIn" 
                                      class="mt-4 text-gray-500 text-sm text-center bg-gray-100 py-2 rounded-lg">
                                    Inicia sesión para reservar
                                </span>
                                <span v-else 
                                      class="mt-4 text-red-500 text-sm text-center bg-red-50 py-2 rounded-lg">
                                    Sin stock disponible
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron ternos</h3>
                        <p class="text-gray-500 mb-4">Intenta ajustar los filtros para obtener más resultados</p>
                        <button @click="limpiarFiltros" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Limpiar filtros
                        </button>
                    </div>
                    <!-- Paginación -->
                    <div v-if="prendas.last_page > 1" class="mt-12 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <Link v-if="prendas.prev_page_url" :href="prendas.prev_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                ← Anterior
                            </Link>
                            <template v-for="page in Array.from({length: prendas.last_page}, (_, i) => i + 1)" :key="page">
                                <Link v-if="Math.abs(page - prendas.current_page) <= 2" :href="`/catalogo?page=${page}`"
                                      :class="[
                                        page === prendas.current_page ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                                        'px-3 py-2 border border-gray-300 rounded-lg'
                                      ]">
                                    {{ page }}
                                </Link>
                            </template>
                            <Link v-if="prendas.next_page_url" :href="prendas.next_page_url" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Siguiente →
                            </Link>
                        </nav>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>