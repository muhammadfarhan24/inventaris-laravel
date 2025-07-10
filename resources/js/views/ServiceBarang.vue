<template>
  <div class="service-barang">
    <h2>Service Barang</h2>

    <!-- Tombol tambah hanya tampil jika bukan ketua_yayasan -->
    <button
      v-if="!isKetuaYayasan"
      class="btn-show-form"
      @click="showForm = !showForm"
    >
      {{ showForm ? 'Tutup Form' : '+ Tambah Barang ke Service' }}
    </button>

    <!-- Form tambah service barang -->
    <div v-if="showForm && !isKetuaYayasan" class="form-service">
      <h3>Tambah Barang ke Service</h3>
      <form @submit.prevent="tambahService">
        <div class="form-row">
          <label>Nama Barang:</label>
          <select v-model="form.barang_id" required>
            <option disabled value="">Pilih Barang</option>
            <option v-for="b in barangList" :key="b.id" :value="b.id">
              {{ b.nama }}
            </option>
          </select>
        </div>
        <div class="form-row">
          <label>Keterangan Kerusakan:</label>
          <input v-model="form.kerusakan" type="text" required />
        </div>
        <div class="form-row">
          <label>Tanggal Masuk:</label>
          <input v-model="form.tanggalMasuk" type="date" required />
        </div>
        <button type="submit" class="btn-tambah">Simpan</button>
      </form>
    </div>

    <div class="table-container">
      <h3>Riwayat Service Barang</h3>
      <table class="service-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Kerusakan</th>
            <th>Tanggal Masuk</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in serviceList" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.barang ? item.barang.nama : 'Data Barang Tidak Ditemukan' }}</td> <!-- Menampilkan nama barang -->
            <td>{{ item.deskripsi }}</td>
            <td>{{ item.tanggal_service }}</td>
            <td>{{ item.status }}</td>
            <td>
              <!-- Tombol aksi hanya tampil jika bukan ketua_yayasan -->
              <button
                v-if="!isKetuaYayasan"
                @click="selesaiService(item.id)"
                class="btn-selesai"
              >
                Selesai
              </button>
              <button
                v-if="!isKetuaYayasan"
                @click="hapusService(item.id)"
                class="btn-delete"
              >
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "ServiceBarang",
  data() {
    return {
      showForm: false,
      form: {
        barang_id: '',
        kerusakan: '',
        tanggalMasuk: '',
      },
      serviceList: [],
      barangList: [],
      currentUser: JSON.parse(localStorage.getItem('user')) || {}
    };
  },
  computed: {
    isKetuaYayasan() {
      return this.currentUser.role === 'ketua_yayasan';
    }
  },
  mounted() {
    this.getServiceList();
    this.ambilBarangList();
  },
  methods: {
    // Ambil daftar service barang
    getServiceList() {
      fetch("http://localhost:8000/api/service-barang")
        .then(res => res.json())
        .then(data => {
          console.log("Data Service:", data);  // Periksa data di console
          this.serviceList = data;  // Mengisi data ke dalam serviceList
        })
        .catch((error) => {
          console.error("Gagal mengambil data service:", error);
          alert("Gagal mengambil data service.");
        });
    },

    // Ambil daftar barang yang rusak
    async ambilBarangList() {
      try {
        const res = await fetch("http://localhost:8000/api/barang");
        const data = await res.json();
        console.log("Barang List:", data);  // Periksa data barang yang diterima
        this.barangList = data.filter(b => b.status === "Rusak");
      } catch (err) {
        console.error("Gagal mengambil data barang:", err);
      }
    },

    // Menambah barang ke service
    tambahService() {
      if (this.isKetuaYayasan) {
        alert('Anda tidak memiliki akses untuk menambah service.');
        return;
      }
      const payload = {
        barang_id: this.form.barang_id,
        deskripsi: this.form.kerusakan,
        tanggal_service: this.form.tanggalMasuk,
        status: "Dalam Service"
      };
      fetch("http://localhost:8000/api/service-barang", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
        .then(() => {
          this.getServiceList();
          this.form = { barang_id: '', kerusakan: '', tanggalMasuk: '' };
          this.showForm = false;
        })
        .catch(() => alert("Gagal menambah data."));
    },

    // Menandai service sebagai selesai
    selesaiService(id) {
      if (this.isKetuaYayasan) {
        alert('Anda tidak memiliki akses untuk mengubah status service.');
        return;
      }
      fetch(`http://localhost:8000/api/service-barang/selesai?id=${id}`, {
        method: "PUT"
      })
        .then(() => this.getServiceList())
        .catch(() => alert("Gagal update status."));
    },

    // Menghapus data service
    hapusService(id) {
      if (this.isKetuaYayasan) {
        alert('Anda tidak memiliki akses untuk menghapus service.');
        return;
      }
      if (!confirm("Hapus data service ini?")) return;
      fetch(`http://localhost:8000/api/service-barang/${id}`, {
        method: "DELETE"
      })
        .then(() => this.getServiceList())
        .catch(() => alert("Gagal menghapus data."));
    }
  }
};
</script>

<style scoped>
/* tetap sama seperti sebelumnya */
.service-barang {
  padding: 20px;
}
.btn-show-form {
  background-color: #2c3e50;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 15px;
}
.btn-show-form:hover {
  background-color: #1a252f;
}
.form-service {
  background-color: white;
  padding: 20px;
  margin-bottom: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.form-row {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
}
label {
  margin-bottom: 4px;
  font-weight: bold;
}
input {
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
  cursor: pointer;
  margin-top: 10px;
}
.btn-tambah:hover {
  background-color: #219150;
}
.table-container {
  overflow-x: auto;
  background: white;
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.service-table {
  width: 100%;
  min-width: 900px;
  border-collapse: collapse;
}
.service-table th,
.service-table td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
}
.service-table th {
  background-color: #2c3e50;
  color: white;
}
.btn-selesai {
  background-color: #3498db;
  color: white;
  padding: 6px 10px;
  border-radius: 4px;
  border: none;
  margin-right: 5px;
}
.btn-delete {
  background-color: #e74c3c;
  color: white;
  padding: 6px 10px;
  border-radius: 4px;
  border: none;
}
.btn-selesai:hover {
  background-color: #2980b9;
}
.btn-delete:hover {
  background-color: #c0392b;
}
</style>
