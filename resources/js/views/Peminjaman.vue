<template>
  <div class="peminjaman">
    <!-- === DAFTAR PEMINJAMAN === -->
    <div class="header-bar">
      <h2>Daftar Peminjaman Aktif</h2>
      <button v-if="!isKetuaYayasan" class="btn-tambah" @click="tambahPeminjam">+ Tambah Peminjam</button>
    </div>

    <!-- FORM TAMBAH PEMINJAMAN -->
    <div v-if="showForm && !isKetuaYayasan" class="form-card">
      <div class="form-group">
        <label>Nama Barang</label>
        <select v-model.number="formData.barang_id">
          <option value="" disabled>Pilih Barang</option>
          <option v-for="barang in barangTersedia" :key="barang.id" :value="barang.id">
            {{ barang.nama }}
          </option>
        </select>
      </div>
      <div v-if="selectedBarang" class="form-group">
        <label>Kode Barang</label>
        <input type="text" :value="selectedBarang.kode_barang" readonly />
      </div>
      <div class="form-group">
        <label>Nama Peminjam</label>
        <input type="text" v-model="formData.peminjam" placeholder="Masukkan nama peminjam" />
      </div>
      <div class="form-group">
        <label>Tanggal Pinjam</label>
        <input type="date" v-model="formData.tanggal_peminjam" />
      </div>
      <div class="form-group">
        <label>Tanggal Kembali</label>
        <input type="date" v-model="formData.tanggal_kembali" />
      </div>
      <div class="form-group">
        <label>Status Pengembalian</label>
        <select v-model="formData.status_pengembalian">
          <option value="Dipinjam">Dipinjam</option>
          <option value="Dikembalikan">Dikembalikan</option>
        </select>
      </div>
      <div class="form-actions">
        <button @click="submitPeminjaman" class="btn-tambah">Simpan</button>
        <button @click="showForm = false" class="btn-delete">Batal</button>
      </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="search-bar">
      <input type="text" v-model="searchQuery" placeholder="Cari peminjam atau barang..." />
    </div>

    <!-- DESKTOP TABLE -->
    <div class="table-container desktop-only">
      <table class="peminjaman-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Peminjam</th>
            <th>Nama Barang</th>
            <th>Kode Barang</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Persetujuan</th>
            <th v-if="!isKetuaYayasan">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in filteredPeminjaman" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.peminjam }}</td>
            <td>{{ item.nama_barang }}</td>
            <td>{{ item.kode_barang }}</td>
            <td>{{ item.tanggal_peminjam }}</td>
            <td>{{ item.tanggal_kembali || '-' }}</td>
            <td>{{ item.status_pengembalian }}</td>
            <td>
              <div v-if="isKetuaMasjid && item.status_peminjaman === 'Menunggu'">
                <button @click="setujui(item.id)" class="btn-approve">Setujui</button>
                <button @click="tolak(item.id)" class="btn-delete">Tolak</button>
              </div>
              <div v-else>
                {{ item.status_peminjaman || 'Menunggu' }}
              </div>
            </td>
            <td v-if="!isKetuaYayasan">
              <button @click="editPeminjaman(item.id)" class="btn-edit">Edit</button>
              <button @click="hapusPeminjaman(item.id)" class="btn-delete">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MOBILE CARD AKTIF -->
    <div class="mobile-card-container mobile-only">
      <div class="peminjaman-card" v-for="item in filteredPeminjaman" :key="item.id">
        <p><strong>ID:</strong> {{ item.id }}</p>
        <p><strong>Nama Peminjam:</strong> {{ item.peminjam }}</p>
        <p><strong>Nama Barang:</strong> {{ item.nama_barang }}</p>
        <p><strong>Kode Barang:</strong> {{ item.kode_barang }}</p>
        <p><strong>Tanggal Pinjam:</strong> {{ item.tanggal_peminjam }}</p>
        <p><strong>Tanggal Kembali:</strong> {{ item.tanggal_kembali || '-' }}</p>
        <p><strong>Status:</strong> {{ item.status_pengembalian }}</p>
        <div v-if="!isKetuaYayasan" class="card-actions">
          <button @click="editPeminjaman(item.id)" class="btn-edit">Edit</button>
          <button @click="hapusPeminjaman(item.id)" class="btn-delete">Hapus</button>
        </div>
      </div>
    </div>

    <!-- === RIWAYAT PEMINJAMAN === -->
    <h2 class="section-title">Riwayat Peminjaman</h2>

    <div class="table-container desktop-only">
      <table class="peminjaman-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Peminjam</th>
            <th>Nama Barang</th>
            <th>Kode Barang</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="riwayat in riwayatPeminjaman" :key="riwayat.id">
            <td>{{ riwayat.id }}</td>
            <td>{{ riwayat.peminjam }}</td>
            <td>{{ riwayat.nama_barang }}</td>
            <td>{{ riwayat.kode_barang }}</td>
            <td>{{ riwayat.tanggal_peminjam }}</td>
            <td>{{ riwayat.tanggal_kembali }}</td>
            <td>{{ riwayat.status_pengembalian }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MOBILE RIWAYAT -->
    <div class="mobile-card-container mobile-only riwayat-scroll">
      <div class="peminjaman-card" v-for="riwayat in riwayatPeminjaman" :key="riwayat.id">
        <p><strong>ID:</strong> {{ riwayat.id }}</p>
        <p><strong>Nama:</strong> {{ riwayat.peminjam }}</p>
        <p><strong>Nama Barang:</strong> {{ riwayat.nama_barang }}</p>
        <p><strong>Tanggal Pinjam:</strong> {{ riwayat.tanggal_peminjam }}</p>
        <p><strong>Tanggal Kembali:</strong> {{ riwayat.tanggal_kembali }}</p>
        <p><strong>Status:</strong> {{ riwayat.status_pengembalian }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      daftarPeminjaman: [],
      riwayatPeminjaman: [],
      barangTersedia: [],
      formData: {
        barang_id: null,
        peminjam: '',
        tanggal_peminjam: '',
        tanggal_kembali: '',
        status_pengembalian: 'Dipinjam',
      },
      searchQuery: '',
      showForm: false,
    };
  },
  created() {
    this.fetchPeminjaman();
    this.fetchBarangTersedia();
  },
  computed: {
    filteredPeminjaman() {
      return this.daftarPeminjaman.filter(item =>
        item.peminjam.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        item.nama_barang.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    }
  },
  methods: {
    async fetchPeminjaman() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/peminjaman');
        const data = await res.json();
        this.daftarPeminjaman = data.filter(item => item.status_pengembalian === 'Dipinjam');
        this.riwayatPeminjaman = data.filter(item => item.status_pengembalian === 'Dikembalikan');
      } catch (err) {
        console.error('Gagal mengambil data peminjaman:', err);
      }
    },
    async fetchBarangTersedia() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/barang');
        const data = await res.json();
        this.barangTersedia = data.filter(b => b.status === "Tersedia");
      } catch (err) {
        console.error("Gagal mengambil data barang:", err);
      }
    },
    async setujuiPeminjaman(id) {
      try {
        await fetch(`/peminjaman/setujui/${id}`, { method: 'POST' });
        this.fetchPeminjaman(); // Refresh daftar peminjaman
      } catch (err) {
        console.error('Gagal menyetujui peminjaman:', err);
      }
    },
    async tolakPeminjaman(id) {
      try {
        await fetch(`/peminjaman/{id}/tolak`, { method: 'POST' });
        this.fetchPeminjaman(); // Refresh daftar peminjaman
      } catch (err) {
        console.error('Gagal menolak peminjaman:', err);
      }
    },
    openModal() {
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    async submitPeminjaman() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/peminjaman', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.formData),
        });

        if (res.ok) {
          this.closeModal();
          this.fetchPeminjaman(); // Refresh daftar peminjaman
        } else {
          console.error('Gagal menambahkan peminjaman');
        }
      } catch (err) {
        console.error('Error saat menambahkan peminjaman:', err);
      }
    },
  },
};
</script>

<style scoped>
.peminjaman {
  max-height: 80vh;
  overflow-y: auto;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.btn-tambah {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.btn-tambah:hover {
  background-color: #219150;
}

/* .section-title {
  font-size: 1.2rem;
  margin: 30px 0 15px;
  padding-bottom: 5px;
  border-bottom: 2px solid #eee;
} */

.search-bar {
  margin-bottom: 16px;
}

.search-bar input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.table-container {
  overflow-x: auto;
  margin-bottom: 20px;
}

.peminjaman-table {
  width: 100%;
  border-collapse: collapse;
}

.peminjaman-table th,
.peminjaman-table td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.peminjaman-table th {
  background-color: #2c3e50;
  color: white;
}

.btn-edit,
.btn-delete {
  padding: 6px 10px;
  border-radius: 4px;
  border: none;
  color: white;
  cursor: pointer;
}

.btn-edit {
  background-color: #3498db;
}

.btn-edit:hover {
  background-color: #2980b9;
}

.btn-delete {
  background-color: #e74c3c;
}

.btn-delete:hover {
  background-color: #c0392b;
}

.btn-approve {
  background-color: #2ecc71;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 5px;
}

.form-card {
  background: #f9f9f9;
  border: 1px solid #ddd;
  padding: 16px;
  margin-bottom: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 12px;
}

.form-group label {
  font-weight: bold;
  margin-bottom: 4px;
}

.form-group input,
.form-group select {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}


.mobile-only {
  display: none;
}

.desktop-only {
  display: block;
}

@media (max-width: 900px) {
  .desktop-only {
    display: none;
  }

  .mobile-only {
    display: flex;
    flex-direction: column;
  }

  .mobile-card-container {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 30px;
  }

  .peminjaman-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 12px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.07);
  }
}
</style>
