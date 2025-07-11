<template>
  <div class="main-content">
    <header class="header">
      <h1>Daftar Barang</h1>
    </header>

    <section class="content">
      <!-- Form Tambah Barang -->
      <div v-if="!isKetuaYayasan">
        <h2>Tambah Barang</h2>
        <form @submit.prevent="tambahBarang" class="form-barang" enctype="multipart/form-data">
          <input v-model="form.nama" placeholder="Nama Barang" required />
          <select v-model="form.kategori_id" required>
            <option disabled value="">Pilih Kategori</option>
            <option v-for="k in kategoriList" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
          </select>

          <select v-model="form.merk_id" required>
            <option disabled value="">Pilih Merk</option>
            <option v-for="m in merkList" :key="m.id" :value="m.id">{{ m.nama_merk }}</option>
          </select>

          <select v-model="form.status" required>
            <option disabled value="">Pilih Status</option>
            <option value="Tersedia">Tersedia</option>
            <option value="Rusak">Rusak</option>
            <option value="Dipinjam">Dipinjam</option>
          </select>

          <select v-model="form.ruangan_id" required>
            <option disabled value="">Pilih Ruangan</option>
            <option v-for="ruangan in ruanganList" :key="ruangan.id" :value="ruangan.id">
              {{ ruangan.nama }}
            </option>
          </select>
          <input v-model="form.kode_barang" placeholder="Kode Barang (misal: KB001)" required />

          <!-- Selalu tampilkan input deskripsi -->
          <input v-model="form.deskripsi" placeholder="Deskripsi" required />

          <button type="submit">Tambah</button>
        </form>
      </div>

      <!-- Ringkasan Status -->
      <h2>Ringkasan Status Barang</h2>
      <div class="cards">
        <div class="card available" @click="setStatus('Tersedia')">
          <h3>Barang Tersedia</h3>
          <p>{{ jumlahStatus('Tersedia') }}</p>
        </div>
        <div class="card damaged" @click="setStatus('Rusak')">
          <h3>Barang Rusak</h3>
          <p>{{ jumlahStatus('Rusak') }}</p>
        </div>
        <div class="card borrowed" @click="setStatus('Dipinjam')">
          <h3>Barang Dipinjam</h3>
          <p>{{ jumlahStatus('Dipinjam') }}</p>
        </div>
      </div>

      <!-- Tabel berdasarkan status -->
      <div v-if="activeStatus" class="table-section">
        <button class="close-btn" @click="activeStatus = ''" v-if="!isKetuaYayasan">Tutup</button>
        <h3>Daftar Barang {{ activeStatus }}</h3>
        <table class="summary-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Barang</th>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Merk</th>
              <th>Status</th>
              <th>Kode Barang</th>
              <th>Deskripsi</th>
              <th>Created_at</th>
              <!-- <th>Gambar</th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in filteredBarang" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ item.kode_barang }}</td>
              <td>{{ item.nama }}</td>
              <td>{{ item.kategori?.nama_kategori || '-' }}</td>
              <td>{{ item.merk?.nama_merk || '-' }}</td>
              <td>{{ item.status }}</td>
              <td>{{ item.kode_barang }}</td>
              <td>{{ item.deskripsi }}</td>
              <td>{{ item.created_at }}</td>
              <!-- <td>
                <img
                  v-if="item.gambar"
                  :src="`http://localhost:3000/uploads/${item.gambar}`"
                  alt="Gambar Barang"
                  class="thumbnail"
                />
                <span v-else>-</span>
              </td> -->
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>


<script>
export default {
  name: 'DaftarBarangPage',
  data() {
    return {
      activeStatus: 'tersedia', // lowercase agar cocok dengan normalisasi
      barangList: [],
      ruanganList: [],
      kategoriList: [],
      merkList: [],
      form: {
        nama: '',
        kategori_id: '',
        merk_id: '',
        status: '',
        ruangan_id: '',
        deskripsi: ''
      }
    };
  },
  computed: {
    isKetuaYayasan() {
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      return user.role === 'ketua_yayasan';
    },
    filteredBarang() {
      return this.barangList.filter(b => b.status === this.activeStatus);
    }
  },
  methods: {
    setStatus(status) {
      this.activeStatus = status.toLowerCase().trim();
    },
    jumlahStatus(status) {
      const normalized = status.toLowerCase().trim();
      return this.barangList.filter(b => b.status === normalized).length;
    },
    async ambilDataBarang() {
      try {
        const res = await this.$api.get('http://127.0.0.1:8000/api/barang')
        this.barangList = Array.isArray(res.data)
          ? res.data.map(b => ({
              ...b,
              status: b.status?.toLowerCase().trim()
            }))
          : [];
      } catch (err) {
        console.error('Gagal ambil data barang:', err);
        this.barangList = [];
      }
    },
    async ambilDataRuangan() {
      try {
        const res = await this.$api.get('http://127.0.0.1:8000/api/ruangan')
        this.ruanganList = res.data;
      } catch (err) {
        console.error('Gagal ambil data ruangan:', err);
      }
    },
    async ambilDataKategori() {
      try {
        const res = await this.$api.get('http://127.0.0.1:8000/api/kategori')
        this.kategoriList = res.data;
      } catch (err) {
        console.error('Gagal ambil data kategori:', err);
      }
    },
    async ambilDataMerk() {
      try {
        const res = await this.$api.get('http://127.0.0.1:8000/api/merk')
        this.merkList = res.data;
      } catch (err) {
        console.error('Gagal ambil data merk:', err);
      }
    },
    async tambahBarang() {
      // Pastikan semua field yang diperlukan sudah terisi
      if (!this.form.kategori_id || !this.form.merk_id || !this.form.ruangan_id || !this.form.status || !this.form.nama) {
        alert('Pastikan semua field terisi dengan benar!');
        return;
      }

      const payload = {
        nama: this.form.nama,
        kategori_id: this.form.kategori_id,
        merk_id: this.form.merk_id,
        status: this.form.status,
        ruangan_id: this.form.ruangan_id,
        deskripsi: this.form.deskripsi || '', // Deskripsi boleh kosong
        kode_barang: this.form.kode_barang // Kode barang otomatis menggunakan timestamp
      };

      try {
        const res = await this.$api.post('http://127.0.0.1:8000/api/barang', payload);
        this.form = {
          nama: '',
          kategori_id: '',
          merk_id: '',
          status: '',
          ruangan_id: '',
          deskripsi: '',
          kode_barang: ''
        };
        this.ambilDataBarang(); // Ambil data barang terbaru
      } catch (err) {
        console.error('Gagal tambah barang:', err);
      }
    }
  },
  mounted() {
    this.ambilDataBarang();
    this.ambilDataRuangan();
    this.ambilDataKategori();
    this.ambilDataMerk();
  },
  watch: {
    'form.status'(newStatus) {
      if (newStatus !== 'Rusak') {
        this.form.deskripsi = ''; // Kosongkan deskripsi jika status bukan rusak
      }
    }
  }
};
</script>

<style scoped>
.main-content {
  background-color: #f4f4f4;
  padding: 20px;
}
.header {
  background-color: white;
  padding: 20px;
  border-bottom: 1px solid #ddd;
}
.cards {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}
.card {
  flex: 1;
  background: white;
  padding: 20px;
  border-radius: 8px;
  cursor: pointer;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.card.available p { color: #27ae60; }
.card.damaged p { color: #e74c3c; }
.card.borrowed p { color: #f39c12; }
.table-section {
  margin-top: 30px;
}
.summary-table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  margin-top: 10px;
}
.summary-table th, .summary-table td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}
.summary-table thead {
  background-color: #2c3e50;
  color: white;
}
.close-btn {
  background-color: #e74c3c;
  color: white;
  padding: 6px 12px;
  border: none;
  margin-bottom: 10px;
  border-radius: 4px;
}
.form-barang {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 30px;
}
.form-barang input, .form-barang select {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.form-barang button {
  width: fit-content;
  padding: 8px 16px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.thumbnail {
  max-width: 80px;
  max-height: 60px;
  border-radius: 4px;
  object-fit: cover;
}
</style>
