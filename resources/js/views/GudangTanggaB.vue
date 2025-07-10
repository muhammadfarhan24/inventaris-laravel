<template>
  <div class="gudang-tangga-b">
    <div class="header-bar">
      <h2>Daftar Barang - Gudang Tangga B</h2>
      <button class="btn-tambah" @click="tambahBarang">+ Tambah Barang</button>
    </div>

    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Cari Barang..." />
    </div>

    <div v-if="editMode" class="form-edit">
      <h3>Edit Barang</h3>
      <form @submit.prevent="submitEdit">
        <div class="form-row">
          <label>Nama Barang:</label>
          <input v-model="form.nama" type="text" required />
        </div>
        <div class="form-row">
          <label>Kategori:</label>
          <select v-model="form.kategori_id" required>
            <option disabled value="">Pilih Kategori</option>
            <option v-for="k in kategoriList" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
          </select>
        </div>
        <div class="form-row">
          <label>Merk:</label>
          <select v-model="form.merk_id" required>
            <option disabled value="">Pilih Merk</option>
            <option v-for="m in merkList" :key="m.id" :value="m.id">{{ m.nama_merk }}</option>
          </select>
        </div>
        <div class="form-row">
          <label>Status:</label>
          <select v-model="form.status" required>
            <option value="Tersedia">Tersedia</option>
            <option value="Dipinjam">Dipinjam</option>
            <option value="Diservis">Diservis</option>
            <option value="Rusak">Rusak</option>
          </select>
        </div>
        <div class="form-row">
          <label>Deskripsi:</label>
          <input v-model="form.deskripsi" type="text" />
        </div>
        <button type="submit" class="btn-tambah">Simpan Perubahan</button>
        <button type="button" class="btn-delete" @click="batalEdit">Batal</button>
      </form>
    </div>

    <table class="barang-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Barang</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Merk</th>
          <th>Status</th>
          <th>Deskripsi</th>
          <th>Gambar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(barang, index) in filteredBarangList" :key="barang.id">
          <td>{{ index + 1 }}</td>
          <td>{{ barang.kode_barang || '_' }}</td>
          <td>{{ barang.nama }}</td>
          <td>{{ getNamaKategori(barang.kategori_id) }}</td>
          <td>{{ getNamaMerk(barang.merk_id) }}</td>
          <td>{{ barang.status }}</td>
          <td>{{ barang.deskripsi }}</td>
          <td>
            <img
              v-if="barang.gambar"
              :src="`http://localhost:3000/uploads/${barang.gambar}`"
              alt="Gambar Barang"
              class="thumbnail"
            />
            <span v-else>-</span>
          </td>
          <td>
            <button @click="editBarang(barang)" class="btn-edit">Edit</button>
            <button @click="deleteBarang(barang.id)" class="btn-delete">Delete</button>
          </td>
        </tr>
        <tr v-if="filteredBarangList.length === 0">
          <td colspan="7" style="text-align: center;">Data Tidak Ditemukan</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'GudangTanggaB',
  data() {
    return {
      searchQuery: '',
      barangList: [],
      kategoriList: [],
      merkList: [],
      editMode: false,
      form: {
        id: null,
        nama: '',
        kategori_id: '',
        merk_id: '',
        status: '',
        deskripsi: '',
        ruangan_id: 4 // karena ini Gudang Imam A
      }
    }
  },
  computed: {
    filteredBarangList() {
      const query = this.searchQuery.toLowerCase();
      return this.barangList.filter(item =>
        item.nama.toLowerCase().includes(query) ||
        this.getNamaKategori(item.kategori_id).toLowerCase().includes(query) ||
        this.getNamaMerk(item.merk_id).toLowerCase().includes(query)
      );
    }
  },
  methods: {
    fetchBarang() {
      fetch('http://localhost:3000/barang')
        .then(res => res.json())
        .then(data => {
          this.barangList = data.filter(item => item.ruangan_id == 4);
        })
        .catch(err => console.error(err));
    },
    fetchKategori() {
      fetch('http://localhost:3000/kategori')
        .then(res => res.json())
        .then(data => {
          this.kategoriList = data;
        });
    },
    fetchMerk() {
      fetch('http://localhost:3000/merk')
        .then(res => res.json())
        .then(data => {
          this.merkList = data;
        });
    },
    getNamaKategori(id) {
      const kategori = this.kategoriList.find(k => k.id === id);
      return kategori ? kategori.nama_kategori : '-';
    },
    getNamaMerk(id) {
      const merk = this.merkList.find(m => m.id === id);
      return merk ? merk.nama_merk : '-';
    },
    tambahBarang() {
      alert('Navigasi ke form tambah barang');
    },
    editBarang(barang) {
      this.editMode = true;
      this.form = {
        id: barang.id,
        nama: barang.nama,
        kategori_id: barang.kategori_id,
        merk_id: barang.merk_id,
        status: barang.status,
        deskripsi: barang.deskripsi,
        kode_barang: barang.kode_barang || '',
        ruangan_id: barang.ruangan_id
      };
    },
    submitEdit() {
  fetch(`http://localhost:3000/barang/${this.form.id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(this.form)
  })
    .then(res => {
      if (!res.ok) throw new Error('Gagal update barang');
      return res.json();
    })
    .then(() => {
      this.editMode = false;
      this.resetForm();
      this.fetchBarang();
    })
    .catch(err => alert("Gagal menyimpan perubahan: " + err.message));
},
    batalEdit() {
      this.editMode = false;
      this.resetForm();
    },
    resetForm() {
      this.form = {
        id: null,
        nama: '',
        kategori_id: '',
        merk_id: '',
        status: '',
        deskripsi: '',
        ruangan_id: 4
      };
    },
    deleteBarang(id) {
      if (confirm('Yakin ingin menghapus barang ini?')) {
        fetch(`http://localhost:3000/barang?id=${id}`, {
          method: 'DELETE'
        })
          .then(() => this.fetchBarang())
          .catch(err => console.error(err));
      }
    }
  },
  mounted() {
    this.fetchBarang();
    this.fetchKategori();
    this.fetchMerk();
  }
}
</script>

<style scoped>
.gudang-tangga-b {
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
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
.btn-tambah:hover {
  background-color: #219150;
}
.search-bar {
  margin-bottom: 20px;
}
.search-bar input {
  width: 100%;
  padding: 10px 12px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.barang-table {
  width: 100%;
  border-collapse: collapse;
}
.barang-table th,
.barang-table td {
  padding: 12px;
  border-bottom: 1px solid #ccc;
  text-align: left;
}
.barang-table th {
  background-color: #2c3e50;
  color: white;
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
.btn-edit:hover {
  background-color: #2980b9;
}
.btn-delete:hover {
  background-color: #c0392b;
}
.form-edit {
  background-color: #f4f4f4;
  padding: 20px;
  margin-bottom: 20px;
  border-radius: 8px;
}
.form-row {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
}

.thumbnail {
  max-width: 80px;
  max-height: 60px;
  border-radius: 4px;
  object-fit: cover;
  box-shadow: 0 0 6px rgba(0,0,0,0.2);
}

</style>
