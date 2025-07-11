<template>
  <div class="merk-barang">
    <div class="header-bar">
      <h2>Daftar Merk Barang</h2>
      <button v-if="!isKetuaYayasan" class="btn-tambah" @click="tambahMerk">+ Tambah Merk</button>
    </div>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Cari Merk..." />
    </div>

    <div v-if="!isKetuaYayasan && showForm" class="form-merk">
      <h3>{{ formMode === 'tambah' ? 'Tambah' : 'Edit' }} Merk</h3>

      <input
        type="text"
        v-model="formData.kode_merk"
        placeholder="Kode Merk"
        :disabled="formMode === 'edit'"
      />

      <input
        type="text"
        v-model="formData.nama_merk"
        placeholder="Nama Merk"
        required
      />

      <button @click="submitForm" class="btn-simpan">Simpan</button>
      <button @click="batalForm" class="btn-batal">Batal</button>
    </div>

    <table class="merk-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Merk</th>
          <th>Nama Merk</th>
          <th v-if="!isKetuaYayasan">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(merk, index) in filteredMerkList" :key="merk.id">
          <td>{{ index + 1 }}</td>
          <td>{{ merk.kode_merk }}</td>
          <td>{{ merk.nama_merk }}</td>
          <td v-if="!isKetuaYayasan">
            <button @click="editMerk(merk.id)" class="btn-edit">Edit</button>
            <button @click="deleteMerk(merk.id)" class="btn-delete">Delete</button>
          </td>
        </tr>
        <tr v-if="filteredMerkList.length === 0">
          <td :colspan="isKetuaYayasan ? 3 : 4" style="text-align: center;">Data Tidak Ditemukan</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: '',
      merkList: [],
      formMode: 'tambah',
      showForm: false,
      formData: {
        id: null,
        kode_merk: '',
        nama_merk: ''
      }
    };
  },
  computed: {
    isKetuaYayasan() {
      const user = JSON.parse(localStorage.getItem('user'));
      return user?.role === 'ketua_yayasan';
    },
    filteredMerkList() {
      const q = this.searchQuery.toLowerCase();
      return this.merkList.filter(m =>
        m.nama_merk.toLowerCase().includes(q) || m.kode_merk.toLowerCase().includes(q)
      );
    }
  },
  methods: {
    async fetchMerk() {
      try {
        const res = await this.$api.get('/api/merk');
        this.merkList = res.data;
      } catch (err) {
        console.error('Gagal ambil merk:', err);
      }
    },
    generateKodeBaru() {
      const prefix = 'MK';
      const angkaList = this.merkList
        .map(m => parseInt(m.kode_merk?.replace(prefix, '')) || 0)
        .filter(n => !isNaN(n));
      const maxAngka = angkaList.length > 0 ? Math.max(...angkaList) : 0;
      const nextAngka = maxAngka + 1;
      return prefix + nextAngka.toString().padStart(3, '0');
    },
    tambahMerk() {
      this.formMode = 'tambah';
      this.formData = {
        id: null,
        kode_merk: this.generateKodeBaru(),
        nama_merk: ''
      };
      this.showForm = true;
    },
    editMerk(id) {
      const merk = this.merkList.find(m => m.id === id);
      if (merk) {
        this.formMode = 'edit';
        this.formData = { ...merk };
        this.showForm = true;
      }
    },
    async deleteMerk(id) {
      if (!confirm('Yakin ingin menghapus merk ini?')) return;
      try {
        await this.$api.delete(`/api/merk/${id}`);
        this.fetchMerk();
      } catch (err) {
        alert('Gagal menghapus merk');
      }
    },
    submitForm() {
      if (this.formMode === 'tambah') {
        this.simpanTambahMerk();
      } else {
        this.simpanEditMerk();
      }
    },
    async simpanTambahMerk() {
      try {
        await this.$api.post('/api/merk', {
          kode_merk: this.formData.kode_merk,
          nama_merk: this.formData.nama_merk
        });
        this.fetchMerk();
        this.showForm = false;
      } catch (err) {
        alert('Gagal menambahkan merk');
      }
    },
    async simpanEditMerk() {
      try {
        await this.$api.put(`/api/merk/${this.formData.id}`, {
          kode_merk: this.formData.kode_merk,
          nama_merk: this.formData.nama_merk
        });
        this.fetchMerk();
        this.showForm = false;
      } catch (err) {
        alert('Gagal mengedit merk');
      }
    },
    batalForm() {
      this.showForm = false;
    }
  },
  mounted() {
    this.fetchMerk();
  }
};
</script>

<style scoped>
.merk-barang {
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.header-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
.btn-tambah {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}
.search-bar {
  margin-bottom: 20px;
}
.search-bar input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.merk-table {
  width: 100%;
  border-collapse: collapse;
}
.merk-table th,
.merk-table td {
  padding: 12px;
  border-bottom: 1px solid #ccc;
}
.merk-table th {
  background-color: #2c3e50;
  color: white;
}
.btn-edit {
  background-color: #3498db;
  color: white;
  padding: 6px 10px;
  margin-right: 5px;
  border: none;
  border-radius: 4px;
}
.btn-delete {
  background-color: #e74c3c;
  color: white;
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
}
.form-merk {
  background: #f5f5f5;
  padding: 15px;
  margin-top: 20px;
  border-radius: 8px;
}
.form-merk input {
  display: block;
  width: 100%;
  margin-bottom: 10px;
  padding: 8px;
}
.btn-simpan {
  background-color: #2ecc71;
  color: white;
  padding: 8px 16px;
  margin-right: 8px;
  border: none;
  border-radius: 4px;
}
.btn-batal {
  background-color: #95a5a6;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
}
</style>
