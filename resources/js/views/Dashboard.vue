<template>
  <div class="dashboard">
    <main class="main-content">
     

      <section class="content">
        <h2>Selamat datang di Dashboard Yayasan AL-Istiqomah</h2>
        <p>Berikut adalah ringkasan data inventaris</p>

        <div class="cards">
          <button class="card" @click="activeView = 'barang'">
            <h3>Total Barang</h3>
            <p>{{ barangList.length }}</p>
          </button>
          <button class="card" @click="activeView = 'kategori'">
            <h3>Jumlah Kategori</h3>
            <p>{{ kategoriList.length }}</p>
          </button>
          <button class="card" @click="activeView = 'ruangan'">
            <h3>Jumlah Ruangan</h3>
            <p>{{ ruanganList.length }}</p>
          </button>
          <button class="card" @click="activeView = 'pengguna'">
            <h3>Total Pengguna</h3>
            <p>{{ penggunaList.length }}</p>
          </button>
        </div>

        <div class="content-area" v-if="activeView" style="margin-top: 30px;">
          <button @click="activeView = ''" class="close-btn">Tutup</button>

          <!-- BARANG -->
          <div v-if="activeView === 'barang'">
            <h3>Daftar Barang</h3>
            <table class="summary-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Merk</th>
                  <th>Status</th>
                  <th>Deskripsi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, i) in barangList" :key="item.id">
                  <td>{{ i + 1 }}</td>
                  <td>{{ item.nama }}</td>
                  <td>{{ item.merk_id }}</td>
                  <td>{{ item.status }}</td>
                  <td>{{ item.deskripsi }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- KATEGORI -->
          <div v-else-if="activeView === 'kategori'">
            <h3>Daftar Kategori</h3>
            <table class="summary-table">
              <thead>
                <tr><th>No</th><th>Nama Kategori</th></tr>
              </thead>
              <tbody>
                <tr v-for="(item, i) in kategoriList" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ item }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- RUANGAN -->
          <div v-else-if="activeView === 'ruangan'">
            <h3>Daftar Ruangan</h3>
            <table class="summary-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Ruangan</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, i) in ruanganList" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ item }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- PENGGUNA -->
          <div v-else-if="activeView === 'pengguna'">
            <h3>Daftar Pengguna</h3>
            <table class="summary-table">
              <thead>
                <tr><th>No</th><th>Nama</th><th>Peran</th></tr>
              </thead>
              <tbody>
                <tr v-for="(item, i) in penggunaList" :key="i">
                  <td>{{ i + 1 }}</td>
                  <td>{{ item.nama }}</td>
                  <td>{{ item.role }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'DashboardPage',
  data() {
    return {
      sidebarOpen: false,
      activeView: '',
      barangList: [],
      kategoriList: [],
      ruanganList: [],
      penggunaList: []
    }
  },
  mounted() {
    this.fetchBarang()
    this.fetchKategori()
    this.fetchRuangan()
    this.fetchPengguna()
  },
  methods: {
    async fetchBarang() {
      const res = await this.$api.get('/api/barang')
       console.log('Isi res.data:', res.data)
      this.barangList = res.data
    },
    async fetchKategori() {
      const res = await this.$api.get('/api/kategori')
      console.log('Isi res.data:', res.data)
      this.kategoriList = res.data.map(k => k.nama_kategori) // sesuaikan field
    },
    async fetchRuangan() {
      const res = await this.$api.get('/api/ruangan')
      console.log('Isi res.data:', res.data)
      this.ruanganList = res.data.map(r => r.nama) // sesuaikan field
    },
    async fetchPengguna() {
      const res = await axios.get('http://127.0.0.1:8000/api/user')
      this.penggunaList = res.data
    }
  }
}
</script>


<style scoped>
.dashboard {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.main-content {
  flex: 1;
  background-color: #f4f4f4;
  display: flex;
  flex-direction: column;
}

.header {
  background-color: white;
  padding: 20px;
  border-bottom: 1px solid #ddd;
}

.content {
  padding: 20px;
}

.toggle-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.cards {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-top: 20px;
}

.card {
  background-color: white;
  padding: 20px;
  flex: 1 1 200px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba( 0, 0, 0, 0.1);
  text-align: center;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
}

.card:hover {
  background-color: #ecf0f1;
}

.card h3 {
  margin-bottom: 10px;
  color: #2c3e50;
}

.card p {
  font-size: 24px;
  font-weight: bold;
  color: #2980b9;
}

.summary-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background-color: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.summary-table th, 
.summary-table td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.summary-table thead {
  background-color: #2c3e50;
  color: white;
}

.summary-table tbody tr:hover {
  background-color: #f1f1f1;
}

.close-btn {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  margin-bottom: 10px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .cards {
    flex-direction: column;
  }

  .card {
    width: 100%;
  }
}
</style>
