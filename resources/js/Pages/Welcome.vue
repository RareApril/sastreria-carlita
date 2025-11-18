<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
});

const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user)

// Carrusel de imágenes
const currentSlide = ref(0)
const slides = [
  { image: '/prendas/3.png', title: 'Ternos Elegantes' },
  { image: '/prendas/4.png', title: 'Ternos Clásicos' },
  { image: '/prendas/5.png', title: 'Ternos Modernos' },
  { image: '/prendas/6.png', title: 'Ternos de Gala' },
  { image: '/prendas/7.png', title: 'Ternos Slim Fit' },
  { image: '/prendas/8.png', title: 'Ternos Formales' },
  { image: '/prendas/9.png', title: 'Ternos Premium' }
]



onMounted(() => {
  setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % slides.length
  }, 4000)
})

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length
}

const whatsappNumbers = [
  { number: '992073032', label: '992 073 032' },
  { number: '985155212', label: '985 155 212' },
  { number: '989342193', label: '989 342 193' },
]

const openWhatsApp = (number) => {
  window.open(`https://wa.me/51${number}?text=Hola,%20quisiera%20información%20sobre%20el%20alquiler%20de%20ternos`, '_blank')
}
</script>

<template>
  <Head title="Alquiler de Ternos Carlita" />

  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <!-- Logo y Título -->
          <div class="flex items-center space-x-3">
            <img src="/logo.png" alt="Logo" class="h-12 w-12 object-contain" />
            <div>
              <h1 class="text-xl font-bold text-gray-900">Alquiler de Ternos Carlita</h1>
              <p class="text-xs text-gray-600">Desde 1998 - 25+ años de experiencia</p>
            </div>
          </div>

          <!-- Navegación -->
          <nav class="flex items-center space-x-4">
            <template v-if="isAuthenticated">
              <Link
                :href="route('dashboard')"
                class="text-gray-700 hover:text-red-600 font-medium transition"
              >
                Dashboard
              </Link>
            </template>
            <template v-else>
              <Link
                v-if="canLogin"
                :href="route('login')"
                class="text-gray-700 hover:text-red-600 font-medium transition"
              >
                Iniciar Sesión
              </Link>
              <Link
                v-if="canRegister"
                :href="route('register')"
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-5 py-2 rounded-lg transition shadow-sm"
              >
                Registrarse
              </Link>
            </template>
          </nav>
        </div>
      </div>
    </header>

    <!-- Hero Section con Carrusel -->
    <section class="relative bg-gray-900 h-[600px] overflow-hidden">
      <!-- Carrusel -->
      <div class="relative h-full">
        <div 
          v-for="(slide, index) in slides" 
          :key="index"
          class="absolute inset-0 transition-opacity duration-1000"
          :class="currentSlide === index ? 'opacity-100' : 'opacity-0'"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/80 z-10"></div>
          <img 
            :src="slide.image" 
            :alt="slide.title"
            class="w-full h-full object-contain"
          />
        </div>

        <!-- Contenido sobre el carrusel -->
        <div class="absolute inset-0 z-20 flex items-center">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-2xl">
              <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Sastrería & Confecciones Carlita
              </h2>
              <p class="text-xl md:text-2xl text-gray-200 mb-8">
                Ternos a medida y alquiler de prendas elegantes para toda ocasión
              </p>
              <div class="flex flex-wrap gap-4">
                <Link
                  :href="route('catalogo.index')"
                  class="bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-4 rounded-lg transition shadow-lg inline-block"
                >
                  Ver Catálogo de Ternos
                </Link>
                <a
                  href="#contacto"
                  class="bg-white hover:bg-gray-100 text-gray-900 font-bold px-8 py-4 rounded-lg transition shadow-lg inline-block"
                >
                  Contactar Ahora
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Controles del carrusel -->
        <button
          @click="prevSlide"
          class="absolute left-4 top-1/2 -translate-y-1/2 z-30 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-3 rounded-full transition"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          @click="nextSlide"
          class="absolute right-4 top-1/2 -translate-y-1/2 z-30 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-3 rounded-full transition"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <!-- Indicadores -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex space-x-2">
          <button
            v-for="(slide, index) in slides"
            :key="index"
            @click="currentSlide = index"
            class="w-3 h-3 rounded-full transition"
            :class="currentSlide === index ? 'bg-white' : 'bg-white/50'"
          ></button>
        </div>
      </div>
    </section>

    <!-- Información del Negocio -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
          <!-- Ubicación -->
          <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <div class="flex items-start space-x-4">
              <div class="bg-red-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-2">Ubicación</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                  C.C. Plaza Vitarte<br>
                  Block "B" 253-233-238<br>
                  Block "E" 290<br>
                  Ate Vitarte, Lima
                </p>
              </div>
            </div>
          </div>

          <!-- Horarios -->
          <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <div class="flex items-start space-x-4">
              <div class="bg-red-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-2">Horarios</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                  <strong>Lun - Sáb:</strong> 10:00 AM - 10:00 PM<br>
                  <strong>Domingos:</strong> 10:00 AM - 8:00 PM
                </p>
              </div>
            </div>
          </div>

          <!-- Servicios -->
          <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <div class="flex items-start space-x-4">
              <div class="bg-red-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 mb-2">Servicios</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                  <li>• Confección a medida</li>
                  <li>• Alquiler de ternos</li>
                  <li>• Incluye accesorios</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contacto WhatsApp -->
    <section id="contacto" class="py-16 bg-gradient-to-br from-red-50 to-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
          ¿Necesitas Ayuda? Contáctanos por WhatsApp
        </h2>
        <p class="text-gray-600 mb-8">
          Nuestro equipo está listo para atenderte y ayudarte a encontrar el terno perfecto
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <button
            v-for="contact in whatsappNumbers"
            :key="contact.number"
            @click="openWhatsApp(contact.number)"
            class="group bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-4 rounded-lg transition shadow-lg inline-flex items-center space-x-3"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span>{{ contact.label }}</span>
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="mb-4 md:mb-0">
            <p class="text-sm text-gray-400">
              © 2025 Sastrería & Confecciones Carlita. Todos los derechos reservados.
            </p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
html {
  scroll-behavior: smooth;
}
</style>