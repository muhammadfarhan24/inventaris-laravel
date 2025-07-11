<template>
  <div class="service-barang">
    <h2>Service Barang</h2>

    <!-- Tombol tambah hanya tampil jika bukan ketua_yayasan -->
    <button v-if="!isKetuaYayasan" class="btn-show-form" @click="toggleForm">
      {{ showForm ? 'Tutup Form' : '+ Tambah Barang ke Service' }}
    </button>

    <!-- Form tambah/edit service barang -->
    <div v-if="showForm && !isKetuaYayasan" class="form-service">
      <h3>{{ formMode === 'tambah' ? 'Tambah' : 'Edit' }} Service</h3>
      <form @submit.prevent="submitForm">
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
          <input v-model="form.deskripsi" type="text" required />
        </div>
        <div class="form-row">
          <label>Tanggal Masuk:</label>
          <input v-model="form.tanggal_service" type="date" required />
        </div>
        <button type="submit" class="btn-tambah">Simpan</button>
        <button type="button" @click="batalForm" class="btn-batal">Batal</button>
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
            <th v-if="!isKetuaYayasan">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in serviceList" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.barang?.nama || 'Barang tidak ditemukan' }}</td>
            <td>{{ item.deskripsi }}</td>
            <td>{{ item.tanggal_service }}</td>
            <td>{{ item.status }}</td>
            <td v-if="!isKetuaYayasan">
              <button @click="selesaiService(item.id)" class="btn-selesai">Selesai</button>
              <button @click="hapusService(item.id)" class="btn-delete">Hapus</button>
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
      formMode: 'tambah',
      showForm: false,
      form: {
        id: null,
        barang_id: '',
        deskripsi: '',
        tanggal_service: '',
        status: 'Dalam Service'
      },
      serviceList: [],
      barangList: [],
      currentUser: JSON.parse(localStorage.getItem('user') || '{}')
    };
  },
  computed: {
    isKetuaYayasan() {
      return this.currentUser.role === 'ketua_yayasan';
    }
  },
  methods: {
    toggleForm() {
      this.showForm = !this.showForm;
      this.resetForm();
    },
    resetForm() {
      this.formMode = 'tambah';
      this.form = {
        id: null,
        barang_id: '',
        deskripsi: '',
        tanggal_service: '',
        status: 'Dalam Service'
      };
    },
    async getServiceList() {
      try {
        const res = await this.$api.get('/api/service-barang');
        this.serviceList = res.data;
      } catch (err) {
        console.error("Gagal ambil data service:", err);
      }
    },
    async ambilBarangList() {
      try {
        const res = await this.$api.get('/api/barang');
        this.barangList = res.data.filter(b => b.status === "Rusak");
      } catch (err) {
        console.error("Gagal ambil barang:", err);
      }
    },
    async submitForm() {
      const payload = {
        barang_id: this.form.barang_id,
        deskripsi: this.form.deskripsi,
        tanggal_service: this.form.tanggal_service,
        // status: this.form.status
      };

      try {
        if (this.formMode === 'tambah') {
          await this.$api.post('/api/service-barang', payload);
        } else {
          await this.$api.put(`/api/service-barang/${this.form.id}`, {
        ...payload,
        status: this.form.status
      });
        }

        this.getServiceList();
        this.toggleForm();
      } catch (err) {
        console.error("Gagal simpan data service:", err);
        alert("Gagal menyimpan data.");
      }
    },
    async selesaiService(id) {
      try {
        await this.$api.put(`/api/service-barang/${id}`, { status: 'Selesai' });
        this.getServiceList();
      } catch (err) {
        console.error("Gagal update status:", err);
      }
    },
    async hapusService(id) {
      if (!confirm("Yakin ingin menghapus data ini?")) return;
      try {
        await this.$api.delete(`/api/service-barang/${id}`);
        this.getServiceList();
      } catch (err) {
        console.error("Gagal hapus data:", err);
      }
    },
    batalForm() {
      this.toggleForm();
    }
  },
  mounted() {
    this.getServiceList();
    this.ambilBarangList();
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
