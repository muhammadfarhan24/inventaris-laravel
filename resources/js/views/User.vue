<template>
  <div class="user-page">
    <h2 v-if="isAdmin">Manajemen User</h2>
    <h2 v-else>Profil Saya</h2>

    <!-- ADMIN VIEW -->
    <div v-if="isAdmin">
      <div class="header-bar">
        <input v-model="searchQuery" placeholder="Cari user..." class="search-input" />
        <button class="btn-tambah" @click="tambahUser">+ Tambah User</button>
      </div>

      <!-- Form Tambah User -->
      <div v-if="showFormTambah" class="form-tambah">
        <input v-model="formUser.name" placeholder="Nama" />
        <input v-model="formUser.username" placeholder="Username" />
        <input v-model="formUser.password" placeholder="Password" type="password" />
        <select v-model="formUser.role">
          <option disabled value="">Pilih Role</option>
          <option value="admin">Admin</option>
          <option value="ketua_masjid">Ketua Masjid</option>
          <option value="ketua_yayasan">Ketua Yayasan</option>
        </select>
        <button @click="simpanUserBaru">Simpan</button>
        <button @click="showFormTambah = false">Batal</button>
      </div>

      <!-- Form Edit User -->
      <div v-if="showFormEdit" class="form-tambah">
        <input v-model="editUserData.name" placeholder="Nama" />
        <input v-model="editUserData.username" placeholder="Username" />
        <input v-model="editUserData.password" type="password" placeholder="Password baru (opsional)" />
        <select v-model="editUserData.role">
          <option disabled value="">Pilih Role</option>
          <option value="admin">Admin</option>
          <option value="ketua_masjid">Ketua Masjid</option>
          <option value="ketua_yayasan">Ketua Yayasan</option>
        </select>
        <button @click="simpanEditUser">Simpan Perubahan</button>
        <button @click="showFormEdit = false">Batal</button>
      </div>

      <!-- Tabel User -->
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in filteredUsers" :key="user.id">
              <td>{{ index + 1 }}</td>
              <td>{{ user.name }}</td> <!-- Ganti 'name' dengan 'nama' jika API menggunakan nama -->
              <td>{{ user.username }}</td>
              <td>{{ user.role }}</td>
              <td>{{ user.status }}</td>
              <td>
                <button class="btn-edit" @click="editUser(user.id)">Edit</button>
                <button class="btn-delete" @click="hapusUser(user.id)">Hapus</button>
              </td>
            </tr>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="6" class="text-center">Tidak ada user ditemukan</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- NON-ADMIN VIEW -->
    <div v-else class="user-profile">
      <div class="profile-box">
        <p><strong>Nama:</strong> {{ currentUser.name }}</p>
        <p><strong>Username:</strong> {{ currentUser.username }}</p>
        <p><strong>Role:</strong> {{ currentUser.role }}</p>
        <p><strong>Status:</strong> {{ currentUser.status }}</p>
        <button class="btn-edit" @click="editProfil">Edit Profil</button>
        <button class="btn-reset" @click="resetPassword">Ubah Password</button>
      </div>

      <!-- Form Edit Profil -->
      <div v-if="showEditProfil" class="form-tambah">
        <input v-model="currentUser.name" placeholder="Nama" />
        <input v-model="currentUser.username" placeholder="Username" />
        <button @click="simpanProfil">Simpan Perubahan</button>
        <button @click="showEditProfil = false">Batal</button>
      </div>

      <!-- Form Ubah Password -->
      <div v-if="showGantiPassword" class="form-tambah">
        <input v-model="newPassword" type="password" placeholder="Password baru" />
        <button @click="simpanPasswordBaru">Simpan Password</button>
        <button @click="showGantiPassword = false">Batal</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'UserPage',
  data() {
    return {
      currentUser: JSON.parse(localStorage.getItem('user')) || {},
      searchQuery: '',
      users: [],
      showFormTambah: false,
      showFormEdit: false,
      showEditProfil: false,
      showGantiPassword: false,
      newPassword: '',
      formUser: {
        name: '',
        username: '',
        password: '',
        role: '',
        status: 'Aktif' 
      },
      editUserData: {
        id: '',
        name: '',
        username: '',
        password: '',
        role: ''
      }
    }
  },
  computed: {
    isAdmin() {
      return this.currentUser.role === "admin";
    },
    filteredUsers() {
      const q = this.searchQuery.toLowerCase();
      return this.users.filter(user =>
        user.name?.toLowerCase().includes(q) ||
        user.username?.toLowerCase().includes(q) ||
        user.role?.toLowerCase().includes(q)
      );
    }
  },
  methods: {
    async fetchUsers() {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/user');
        this.users = res.data;
      } catch (err) {
        console.error('Gagal memuat user:', err);
      }
    },
    async hapusUser(id) {
      if (confirm("Yakin ingin menghapus user ini?")) {
        try {
          await axios.delete(`http://127.0.0.1:8000/api/user/${id}`);
          this.fetchUsers();
        } catch (err) {
          alert("Gagal menghapus user");
          console.error(err);
        }
      }
    },
    tambahUser() {
      this.showFormTambah = true;
      this.showFormEdit = false;
      this.formUser = {
        name: '',
        username: '',
        password: '',
        role: '',
        status: 'Aktif' 
      };
    },
    async simpanUserBaru() {
      try {
        if (!this.formUser.name || !this.formUser.username || !this.formUser.password || !this.formUser.role) {
          alert("Semua kolom wajib diisi");
          return;
        }

        await axios.post('http://127.0.0.1:8000/api/user', {
          name: this.formUser.name,
          username: this.formUser.username,
          password: this.formUser.password,
          role: this.formUser.role,
          status: this.formUser.status 
        });

        this.showFormTambah = false;
        this.fetchUsers();
      } catch (err) {
        console.log("DETAIL ERROR:", err.response?.data);
        alert("Gagal update user");
      }
    },
    editUser(id) {
      const user = this.users.find(u => u.id === id);
      if (user) {
        this.editUserData = { ...user, password: '' };
        this.showFormTambah = false;
        this.showFormEdit = true;
      }
    },
    async simpanEditUser() {
      try {
        await axios.put(`http://127.0.0.1:8000/api/user/${this.editUserData.id}`, {
          name: this.editUserData.name,
          username: this.editUserData.username,
          password: this.editUserData.password || '',
          role: this.editUserData.role
        });
        this.showFormEdit = false;
        this.fetchUsers();
      } catch (err) {
        alert("Gagal update user");
        console.error(err);
      }
    },
    editProfil() {
      this.showEditProfil = true;
    },
    async simpanProfil() {
      try {
        await axios.put(`http://127.0.0.1:8000/api/user/${this.currentUser.id}`, {
          name: this.currentUser.name,
          username: this.currentUser.username,
          password: ''
        });
        alert("Profil berhasil diperbarui");
        this.showEditProfil = false;
        localStorage.setItem('user', JSON.stringify(this.currentUser));
      } catch (err) {
        alert("Gagal memperbarui profil");
        console.error(err);
      }
    },
    resetPassword() {
      this.showGantiPassword = true;
    },
    async simpanPasswordBaru() {
      try {
        await axios.put(`http://127.0.0.1:8000/api/user/${this.currentUser.id}/password`, {
          password: this.newPassword
        });
        alert("Password berhasil diperbarui");
        this.showGantiPassword = false;
      } catch (err) {
        alert("Gagal memperbarui password");
        console.error(err);
      }
    }
  },
  mounted() {
    this.fetchUsers();
  }
};
</script>


<style scoped>
.user-page {
  padding: 20px;
}
.header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.search-input {
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
.btn-tambah {
  background-color: #27ae60;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
}
.table-container {
  overflow-x: auto;
}
.user-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}
.user-table th,
.user-table td {
  padding: 10px;
  border: 1px solid #ddd;
}
.user-table th {
  background-color: #2c3e50;
  color: white;
}
.text-center {
  text-align: center;
}
.btn-edit {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 4px;
  margin-right: 5px;
  cursor: pointer;
}
.btn-delete {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 4px;
  cursor: pointer;
}
.btn-reset {
  background-color: #f39c12;
  color: white;
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
  margin-top: 10px;
  cursor: pointer;
}
.profile-box {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  max-width: 500px;
}
.form-tambah {
  margin: 15px 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.form-tambah input,
.form-tambah select {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
