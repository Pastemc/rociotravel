<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Acerca de Agraria</h3>
        </div>
        <div class="card-body">
          <h5>Bienvenido al sistema Agraria</h5>
          <p>Este es un sistema administrativo desarrollado con Laravel y Vue.js.</p>
          <p>Características:</p>
          <ul>
            <li>Autenticación persistente</li>
            <li>Navegación SPA con Vue Router</li>
            <li>Diseño responsive con AdminLTE 3</li>
            <li>Gestión de estado con localStorage</li>
          </ul>
          
          <RouterLink to="/" class="btn btn-primary">
            <i class="fas fa-home mr-2"></i> Volver al Inicio
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AboutView"
}
</script>

<style scoped>

</style>