<template>
  <div class="row">
    <div class="col-12">
      <!-- Card de Usuarios -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-users mr-2"></i>
            Gestión de Usuarios
          </h3>
          <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" @click="showCreateModal">
              <i class="fas fa-plus"></i> Nuevo Usuario
            </button>
          </div>
        </div>
        
        <div class="card-body">
          <!-- Loading -->
          <div v-if="loading" class="text-center p-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando usuarios...</p>
          </div>
          
          <!-- Tabla de Usuarios -->
          <div v-else class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th>Fecha Creación</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span class="badge" :class="getRoleBadgeClass(user.role)">
                      {{ user.role_text }}
                    </span>
                  </td>
                  <td>
                    <span class="badge" :class="getStatusBadgeClass(user.status)">
                      {{ user.status_text }}
                    </span>
                  </td>
                  <td>{{ user.created_at }}</td>
                  <td>
                    <button class="btn btn-sm btn-info mr-1" @click="editUser(user)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" @click="deleteUser(user)" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="users.length === 0">
                  <td colspan="7" class="text-center">No hay usuarios registrados</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Crear/Editar Usuario -->
  <div class="modal" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            <i class="fas fa-user-plus mr-2"></i>
            {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
          </h4>
          <button type="button" class="close" @click="closeModal">
            <span>&times;</span>
          </button>
        </div>
        
        <form @submit.prevent="saveUser">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Nombre Completo *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="name"
                    v-model="form.name" 
                    :class="{ 'is-invalid': errors.name }"
                    required
                  />
                  <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Correo Electrónico *</label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email"
                    v-model="form.email" 
                    :class="{ 'is-invalid': errors.email }"
                    required
                  />
                  <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="role">Rol *</label>
                  <select 
                    class="form-control" 
                    id="role"
                    v-model="form.role" 
                    :class="{ 'is-invalid': errors.role }"
                    required
                  >
                    <option value="">Seleccionar rol</option>
                    <option value="admin">Administrador</option>
                    <option value="moderator">Moderador</option>
                    <option value="user">Usuario</option>
                  </select>
                  <div v-if="errors.role" class="invalid-feedback">{{ errors.role[0] }}</div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="status">Estado *</label>
                  <select 
                    class="form-control" 
                    id="status"
                    v-model="form.status" 
                    :class="{ 'is-invalid': errors.status }"
                    required
                  >
                    <option value="">Seleccionar estado</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                  </select>
                  <div v-if="errors.status" class="invalid-feedback">{{ errors.status[0] }}</div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="password">{{ isEditing ? 'Nueva Contraseña' : 'Contraseña' }} {{ !isEditing ? '*' : '' }}</label>
                  <input 
                    type="password" 
                    class="form-control" 
                    id="password"
                    v-model="form.password" 
                    :class="{ 'is-invalid': errors.password }"
                    :required="!isEditing"
                  />
                  <div v-if="errors.password" class="invalid-feedback">{{ errors.password[0] }}</div>
                  <small class="text-muted" v-if="isEditing">Dejar vacío si no desea cambiar la contraseña</small>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="password_confirmation">Confirmar Contraseña {{ !isEditing ? '*' : '' }}</label>
                  <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation"
                    v-model="form.password_confirmation" 
                    :required="!isEditing"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
              {{ saving ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Crear') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "UsuariosView",
  data() {
    return {
      users: [],
      loading: false,
      saving: false,
      isEditing: false,
      form: {
        id: null,
        name: '',
        email: '',
        role: '',
        status: '',
        password: '',
        password_confirmation: ''
      },
      errors: {}
    }
  },
  methods: {
    async loadUsers() {
      this.loading = true
      try {
        const response = await axios.get('/api/admin-users')
        this.users = response.data.data
      } catch (error) {
        console.error('Error loading users:', error)
        this.$toast.error('Error al cargar usuarios')
      } finally {
        this.loading = false
      }
    },
    
    showCreateModal() {
      this.resetForm()
      this.isEditing = false
      this.showModal()
    },
    
    editUser(user) {
      this.form.id = user.id
      this.form.name = user.name
      this.form.email = user.email
      this.form.role = user.role
      this.form.status = user.status
      this.form.password = ''
      this.form.password_confirmation = ''
      this.isEditing = true
      this.showModal()
    },
    
    async saveUser() {
      this.saving = true
      this.errors = {}
      
      try {
        const url = this.isEditing ? `/api/admin-users/${this.form.id}` : '/api/admin-users'
        const method = this.isEditing ? 'put' : 'post'
        
        const response = await axios[method](url, this.form)
        
        if (response.data.success) {
          this.closeModal()
          this.loadUsers()
          this.showToast(response.data.message, 'success')
        }
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors
        } else {
          this.showToast('Error al guardar usuario', 'error')
        }
      } finally {
        this.saving = false
      }
    },
    
    async deleteUser(user) {
      if (confirm(`¿Está seguro de eliminar al usuario "${user.name}"?`)) {
        try {
          const response = await axios.delete(`/api/admin-users/${user.id}`)
          if (response.data.success) {
            this.loadUsers()
            this.showToast(response.data.message, 'success')
          }
        } catch (error) {
          this.showToast('Error al eliminar usuario', 'error')
        }
      }
    },
    
    resetForm() {
      this.form = {
        id: null,
        name: '',
        email: '',
        role: '',
        status: '',
        password: '',
        password_confirmation: ''
      }
      this.errors = {}
    },
    
    showModal() {
      document.getElementById('userModal').style.display = 'block'
      document.body.classList.add('modal-open')
    },
    
    closeModal() {
      document.getElementById('userModal').style.display = 'none'
      document.body.classList.remove('modal-open')
      this.resetForm()
    },
    
    getRoleBadgeClass(role) {
      const classes = {
        'admin': 'badge-danger',
        'moderator': 'badge-warning',
        'user': 'badge-info'
      }
      return classes[role] || 'badge-secondary'
    },
    
    getStatusBadgeClass(status) {
      return status === 'active' ? 'badge-success' : 'badge-secondary'
    },
    
    showToast(message, type = 'info') {
      // Simple toast notification
      const toastClass = type === 'success' ? 'alert-success' : 'alert-danger'
      const toast = document.createElement('div')
      toast.className = `alert ${toastClass} position-fixed`
      toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;'
      toast.innerHTML = `
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">
          <span>&times;</span>
        </button>
      `
      document.body.appendChild(toast)
      setTimeout(() => toast.remove(), 5000)
    }
  },
  
  mounted() {
    this.loadUsers()
  }
}
</script>

<style scoped>
.modal {
  display: none;
  position: fixed;
  z-index: 1050;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.modal-dialog {
  margin: 30px auto;
  max-width: 800px;
}

.modal-content {
  background-color: #fff;
  border-radius: 5px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

.modal-header {
  padding: 15px 20px;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  padding: 15px 20px;
  border-top: 1px solid #e9ecef;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.table th {
  background-color: #f8f9fa;
}

.badge {
  font-size: 0.75em;
}
</style>