<template>
  <div class="relative">
    <router-link to="/checkout" class="relative text-slate-300 hover:text-white" aria-label="Cart">
      🛒
      <span
        v-if="totalItems > 0"
        class="absolute -top-2 -right-2 bg-purple-600 text-white text-xs font-bold px-2 py-0.5 rounded-full"
      >
        {{ totalItems }}
      </span>
    </router-link>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useCart } from '@/utils/CartService'

// Primarno koristimo reaktivni store
const cartStore = useCart()

// Broj iz store-a (ako ga expose-uje), fallback na sumu
const storeCount = computed(() => {
  const c = cartStore?.totalItems
  if (typeof c === 'number') return c
  const list = cartStore?.cart
  if (Array.isArray(list)) return list.reduce((s, i) => s + (i.quantity ?? i.qty ?? 1), 0)
  return 0
})

// Legacy fallback (ako neke komponente i dalje emituju `cart-updated`)
const legacyCount = ref(0)
function recalcLegacy() {
  try {
    const list = Array.isArray(cartStore?.cart)
      ? cartStore.cart
      : JSON.parse(localStorage.getItem('cart') || '[]')
    legacyCount.value = list.reduce((s, i) => s + (i.quantity ?? i.qty ?? 1), 0)
  } catch { legacyCount.value = 0 }
}

// Konačan broj za bedž: prvo store, ako je 0 koristimo legacy
const totalItems = computed(() => storeCount.value || legacyCount.value)

onMounted(() => {
  recalcLegacy()
  window.addEventListener('cart-updated', recalcLegacy)
})
onUnmounted(() => {
  window.removeEventListener('cart-updated', recalcLegacy)
})
</script>
