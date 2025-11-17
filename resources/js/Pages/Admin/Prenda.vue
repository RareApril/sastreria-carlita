<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  prendas: Array,
  categorias: Array,
  auth: Object
})

const form = useForm({
  categoria_vestimenta_id: '',
  codigo: '',
  nombre: '',
  talla: '',
  color: '',
  precio_alquiler: '',
  stock: '',
  descripcion: '',
  marca: '',
  genero: '',
  imagen: null
})

const previewImagen = ref(null)
const imagenOriginal = ref(null) // Nueva: guarda el nombre original de la imagen
const showForm = ref(false)
const editingId = ref(null)
const searchTerm = ref('')
const filterCategoria = ref('')
const filterGenero = ref('')

const prendasFiltradas = computed(() => {
  let resultado = props.prendas
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    resultado = resultado.filter(p =>
      p.nombre?.toLowerCase().includes(term) ||
      p.codigo?.toLowerCase().includes(term) ||
      p.color?.toLowerCase().includes(term) ||
      p.marca?.toLowerCase().includes(term)
    )
  }
  if (filterCategoria.value) {
    resultado = resultado.filter(p => p.categoria_vestimenta_id == filterCategoria.value)
  }
  if (filterGenero.value) {
    resultado = resultado.filter(p => p.genero?.toLowerCase() === filterGenero.value.toLowerCase())
  }
  return resultado
})

const generosDisponibles = computed(() => {
  return [...new Set(props.prendas.map(p => p.genero).filter(Boolean))]
})

const resetForm = () => {
  form.reset()
  form.imagen = null
  previewImagen.value = null
  imagenOriginal.value = null // Resetea también la imagen original
  showForm.value = false
  editingId.value = null
}

const editarPrenda = (prenda) => {
  form.categoria_vestimenta_id = prenda.categoria_vestimenta_id
  form.codigo = prenda.codigo
  form.nombre = prenda.nombre
  form.talla = prenda.talla
  form.color = prenda.color
  form.precio_alquiler = prenda.precio_alquiler
  form.stock = prenda.stock
  form.descripcion = prenda.descripcion
  form.marca = prenda.marca
  form.genero = prenda.genero
  form.imagen = null
  imagenOriginal.value = prenda.imagen ? prenda.imagen : null
  previewImagen.value = prenda.imagen ? `/prendas/${prenda.imagen}` : null
  editingId.value = prenda.id
  showForm.value = true

  setTimeout(() => {
    document.getElementById('formulario-prenda')?.scrollIntoView({ behavior: 'smooth' })
  }, 100)
}

const eliminarPrenda = (id) => {
  if (confirm('¿Estás seguro de eliminar esta prenda? Esta acción no se puede deshacer.')) {
    router.delete(route('admin.prendas.destroy', id), {
      onSuccess: resetForm
    })
  }
}

const limpiarFiltros = () => {
  searchTerm.value = ''
  filterCategoria.value = ''
  filterGenero.value = ''
}

const handleImagenChange = (e) => {
  const file = e.target.files[0]
  form.imagen = file
  if (file) {
    previewImagen.value = URL.createObjectURL(file)
    imagenOriginal.value = null  // Si sube nueva, olvida la anterior
  } else {
    previewImagen.value = null
  }
}

const submit = () => {
  if (editingId.value) {
    form
      .transform(data => ({
        ...data,
        _method: 'PUT'
      }))
      .post(route('admin.prendas.update', editingId.value), {
        onSuccess: resetForm,
        forceFormData: true
      })
  } else {
    form.post(route('admin.prendas.store'), {
      onSuccess: resetForm,
      forceFormData: true
    })
  }
}
</script>

<template>
  <Head title="Gestión de Prendas" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Gestión de Prendas</h2>
        <span class="text-sm text-gray-600">Total: {{ prendasFiltradas.length }} prenda(s)</span>
      </div>
    </template>
    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">
      <div class="flex justify-end">
        <button
          @click="showForm ? resetForm() : showForm = true"
          :class="showForm ? 'bg-gray-500 hover:bg-gray-600' : 'bg-blue-600 hover:bg-blue-700'"
          class="text-white px-6 py-2.5 rounded-lg font-semibold shadow-sm transition-colors duration-200 flex items-center gap-2"
        >
          <span v-if="!showForm">+ Nueva Prenda</span>
          <span v-else>✕ Cancelar</span>
        </button>
      </div>
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 transform scale-95"
        enter-to-class="opacity-100 transform scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 transform scale-100"
        leave-to-class="opacity-0 transform scale-95"
      >
        <div v-if="showForm" id="formulario-prenda" class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <h3 class="text-xl font-bold text-gray-800 mb-4">
            {{ editingId ? 'Editar Prenda' : 'Nueva Prenda' }}
          </h3>
          <!-- Bloque para mostrar errores generales y de validación -->
<div v-if="Object.keys(form.errors).length" class="bg-red-100 text-red-700 border border-red-300 rounded px-4 py-3 mb-4">
  <ul>
    <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
  </ul>
</div>
          <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">
                Imagen
              </label>
              <input
                type="file"
                accept="image/*"
                @change="handleImagenChange"
                class="block w-full border border-gray-300 rounded-lg p-2.5"
              />
              <div v-if="previewImagen || imagenOriginal" class="mt-2">
                <img :src="previewImagen || `/prendas/${imagenOriginal}`" class="h-40 object-contain rounded border" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">
                Categoría <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.categoria_vestimenta_id"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.categoria_vestimenta_id }"
              >
                <option value="">Selecciona una categoría</option>
                <option v-for="cat in props.categorias" :key="cat.id" :value="cat.id">
                  {{ cat.nombre }}
                </option>
              </select>
              <p v-if="form.errors.categoria_vestimenta_id" class="text-red-500 text-xs mt-1">
                {{ form.errors.categoria_vestimenta_id }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Código <span class="text-red-500">*</span></label>
              <input
                type="text"
                v-model="form.codigo"
                placeholder="Ej: PR-001"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.codigo }"
              />
              <p v-if="form.errors.codigo" class="text-red-500 text-xs mt-1">{{ form.errors.codigo }}</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
              <input
                type="text"
                v-model="form.nombre"
                placeholder="Nombre de la prenda"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.nombre }"
              />
              <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Talla <span class="text-red-500">*</span></label>
              <input
                type="text"
                v-model="form.talla"
                placeholder="Ej: M, L, 42"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.talla }"
              />
              <p v-if="form.errors.talla" class="text-red-500 text-xs mt-1">{{ form.errors.talla }}</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Color <span class="text-red-500">*</span></label>
              <input
                type="text"
                v-model="form.color"
                placeholder="Ej: Azul, Negro"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.color }"
              />
              <p v-if="form.errors.color" class="text-red-500 text-xs mt-1">{{ form.errors.color }}</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Precio Alquiler <span class="text-red-500">*</span></label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">S/</span>
                <input
                  type="number"
                  step="0.01"
                  v-model="form.precio_alquiler"
                  placeholder="0.00"
                  class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': form.errors.precio_alquiler }"
                />
              </div>
              <p v-if="form.errors.precio_alquiler" class="text-red-500 text-xs mt-1">{{ form.errors.precio_alquiler }}</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Stock <span class="text-red-500">*</span></label>
              <input
                type="number"
                v-model="form.stock"
                placeholder="0"
                min="0"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.stock }"
              />
              <p v-if="form.errors.stock" class="text-red-500 text-xs mt-1">{{ form.errors.stock }}</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Marca</label>
              <input
                type="text"
                v-model="form.marca"
                placeholder="Marca de la prenda"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Género</label>
              <select
                v-model="form.genero"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Selecciona</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Unisex">Unisex</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
              <textarea
                v-model="form.descripcion"
                rows="3"
                placeholder="Descripción detallada de la prenda..."
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              ></textarea>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3 pt-2">
              <button
                type="button"
                @click="resetForm"
                class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition-colors"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-green-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ form.processing ? 'Guardando...' : (editingId ? 'Actualizar' : 'Guardar') }}
              </button>
            </div>
          </form>
        </div>
      </transition>
      <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Buscar por nombre, código, color o marca..."
              class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
            <select
              v-model="filterCategoria"
              class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Todas</option>
              <option v-for="cat in props.categorias" :key="cat.id" :value="cat.id">
                {{ cat.nombre }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
            <select
              v-model="filterGenero"
              class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Todos</option>
              <option v-for="genero in generosDisponibles" :key="genero" :value="genero">
                {{ genero }}
              </option>
            </select>
          </div>
        </div>
        <div v-if="searchTerm || filterCategoria || filterGenero" class="mt-3 flex justify-end">
          <button
            @click="limpiarFiltros"
            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
          >
            Limpiar filtros
          </button>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Código</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Talla</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Color</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Precio</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="prenda in prendasFiltradas" :key="prenda.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ prenda.codigo }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ prenda.nombre }}</div>
                  <div v-if="prenda.marca" class="text-xs text-gray-500">{{ prenda.marca }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-gray-700">{{ prenda.talla || '-' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-gray-700">{{ prenda.color || '-' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-green-700">S/ {{ prenda.precio_alquiler }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2.5 py-1 rounded-full text-xs font-semibold"
                    :class="prenda.stock > 5 ? 'bg-green-100 text-green-800' : prenda.stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ prenda.stock }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button
                    class="bg-blue-500 text-white px-3 py-1.5 rounded-lg hover:bg-blue-600 transition-colors font-medium"
                    @click="editarPrenda(prenda)"
                  >
                    Editar
                  </button>
                  <button
                    class="bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600 transition-colors font-medium"
                    @click="eliminarPrenda(prenda.id)"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
              <tr v-if="prendasFiltradas.length === 0">
                <td colspan="7" class="text-center py-12">
                  <div class="text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No se encontraron prendas</p>
                    <p class="text-sm text-gray-400 mt-1">
                      {{ searchTerm || filterCategoria || filterGenero ? 'Intenta con otros filtros' : 'Comienza agregando una nueva prenda' }}
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
