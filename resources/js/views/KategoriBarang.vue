<template>
  <div class="kategori-barang">
    <div class="header-bar">
      <h2>Daftar Kategori Barang</h2>
      <button v-if="!isKetuaYayasan" class="btn-tambah" @click="tambahKategori">+ Tambah Kategori</button>
    </div>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Cari Kategori..." />
    </div>

    <!-- Form Tambah / Edit -->
    <div v-if="!isKetuaYayasan && showForm" class="form-kategori">
      <h3>{{ formMode === 'tambah' ? 'Tambah' : 'Edit' }} Kategori</h3>
      <input type="text" v-model="formData.kode_kategori" placeholder="Kode Kategori" :disabled="formMode === 'edit'" />
      <input type="text" v-model="formData.nama_kategori" placeholder="Nama Kategori" />
      <button @click="submitForm" class="btn-simpan">Simpan</button>
      <button @click="batalForm" class="btn-batal">Batal</button>
    </div>

    <table class="kategori-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Kategori</th>
          <th>Kategori</th>
          <th v-if="!isKetuaYayasan">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(kategori, index) in filteredKategoriList" :key="kategori.id">
          <td>{{ index + 1 }}</td>
          <td>{{ kategori.kode_kategori }}</td>
          <td>{{ kategori.nama_kategori }}</td>
          <td v-if="!isKetuaYayasan">
            <button @click="editKategori(kategori.id)" class="btn-edit">Edit</button>
            <button @click="deleteKategori(kategori.id)" class="btn-delete">Delete</button>
          </td>
        </tr>
        <tr v-if="filteredKategoriList.length === 0">
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
      kategoriList: [],
      formMode: 'tambah',
      showForm: false,
      formData: {
        id: null,
        kode_kategori: '',
        nama_kategori: ''
      }
    };
  },
  computed: {
    isKetuaYayasan() {
      const user = JSON.parse(localStorage.getItem('user'));
      return user?.role === 'ketua_yayasan';
    },
    filteredKategoriList() {
      const q = this.searchQuery.toLowerCase();
      return this.kategoriList.filter(k =>
        k.nama_kategori.toLowerCase().includes(q) || k.kode_kategori.toLowerCase().includes(q)
      );
    }
  },
  methods: {
    async fetchKategori() {
      try {
        const res = await this.$axios.get('http://127.0.0.1:8000/api/kategori');
        this.kategoriList = res.data;
      } catch (err) {
        console.error('Gagal mengambil data kategori:', err);
      }
    },
    generateKodeBaru() {
      const prefix = 'KT';
      const angkaList = this.kategoriList
        .map(k => parseInt(k.kode_kategori?.replace(prefix, '')))
        .filter(n => !isNaN(n));

      const maxAngka = angkaList.length > 0 ? Math.max(...angkaList) : 0;
      const nextAngka = maxAngka + 1;

      return prefix + nextAngka.toString().padStart(3, '0');
    },
    tambahKategori() {
      this.formMode = 'tambah';
      this.formData = {
        id: null,
        kode_kategori: this.generateKodeBaru(),
        nama_kategori: ''
      };
      this.showForm = true;
    },
    async editKategori(id) {
      try {
        const res = await this.$axios.get(`http://127.0.0.1:8000/api/kategori/${id}`);
        this.formMode = 'edit';
        this.formData = res.data;
        this.showForm = true;
      } catch (err) {
        console.error('Gagal mengambil data kategori:', err);
      }
    },
    async deleteKategori(id) {
      if (!confirm('Yakin ingin menghapus kategori ini?')) return;
      try {
        await this.$axios.delete(`http://127.0.0.1:8000/api/kategori/${id}`);
        this.fetchKategori();
      } catch (err) {
        console.error('Gagal menghapus kategori:', err);
      }
    },
    async submitForm() {
      const method = this.formMode === 'tambah' ? 'POST' : 'PUT';
      const url = this.formMode === 'tambah'
        ? 'http://127.0.0.1:8000/api/kategori'
        : `http://127.0.0.1:8000/api/kategori/${this.formData.id}`;

      try {
        await this.$axios({
          method,
          url,
          data: this.formData
        });
        this.fetchKategori();
        this.showForm = false;
      } catch (err) {
        console.error('Gagal menyimpan kategori:', err);
      }
    },
    batalForm() {
      this.showForm = false;
    }
  },
  mounted() {
    this.fetchKategori();
  }
};
</script>

<style scoped>
.kategori-barang {
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

.kategori-table {
  width: 100%;
  border-collapse: collapse;
}
.kategori-table th,
.kategori-table td {
  padding: 12px;
  border-bottom: 1px solid #ccc;
}
.kategori-table th {
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

.form-kategori {
  background: #f5f5f5;
  padding: 15px;
  margin-top: 20px;
  border-radius: 8px;
}
.form-kategori input {
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
