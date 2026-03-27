<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { supabase } from '../../lib/supabase'; // Fixed import path

// --- STATE ---
import { useRouter } from 'vue-router';

const router = useRouter();
const suppliers = ref([]);
const obatList = ref([]);
const isLoading = ref(false);
const errorMsg = ref("");

const form = reactive({
    supplier_id: "",
    no_faktur: "",
    items: [
        { obat_id: "", jumlah: 1, harga_satuan: 0 }
    ]
});

// Fetch Suppliers & Obat for Dropdowns
const fetchData = async () => {
    try {
        const [suppRes, obatRes] = await Promise.all([
            supabase.from('suppliers').select('id, nama_suppliers'),
            supabase.from('obat').select('id, nama_obat, stok, harga_beli, harga_jual')
        ]);
        
        if (suppRes.error) throw suppRes.error;
        if (obatRes.error) throw obatRes.error;

        suppliers.value = suppRes.data || [];
        obatList.value = obatRes.data || [];
    } catch (error) {
        console.error("Gagal load data", error);
        errorMsg.value = "Gagal memuat data master.";
    }
};

onMounted(() => {
    fetchData();
});

// Helper: Add Item Row
const addItem = () => {
    form.items.push({ obat_id: "", jumlah: 1, harga_satuan: 0 });
};

// Helper: Remove Item Row
const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

// Helper: Calculate Total
const grandTotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.jumlah * item.harga_satuan), 0);
});

// Submit Penerimaan using Supabase (Client-Side Sequential Operations)
const submitPenerimaan = async () => {
    isLoading.value = true;
    errorMsg.value = "";
    
    try {
        // 1. Get current logged in user
        const { data: { user }, error: userError } = await supabase.auth.getUser();
        if (userError) throw userError;

        // Look up numeric user_id from custom 'users' table (bigint FK)
        let numericUserId = null;
        if (user) {
            const { data: userData } = await supabase
                .from('users')
                .select('id')
                .eq('email', user.email)
                .single();
            if (userData) numericUserId = userData.id;
        }

        // 2. Insert Penerimaan Header
        const nowIso = new Date().toISOString();
        const { data: penerimaanData, error: headerError } = await supabase
            .from('penerimaan')
            .insert([{
                supplier_id: form.supplier_id,
                user_id: numericUserId,
                no_faktur: form.no_faktur,
                tgl_penerimaan: nowIso.slice(0, 10),
                total_harga: grandTotal.value,
                created_at: nowIso,
                updated_at: nowIso
            }])
            .select()
            .single();

        if (headerError) throw headerError;
        
        const penerimaanId = penerimaanData.id;

        // 3. Prepare details data
        const detailsData = form.items.map(item => ({
            penerimaan_id: penerimaanId,
            obat_id: item.obat_id,
            qty: item.jumlah,
            harga: item.harga_satuan,
            created_at: nowIso,
            updated_at: nowIso
        }));

        // Insert Details in bulk
        if (detailsData.length > 0) {
            const { error: detailsError } = await supabase
                .from('penerimaan_details')
                .insert(detailsData);
            
            // Note: Laravel table might be 'penerimaan_detail' or 'penerimaan_details'
            // In most standard Laravel apps it's plural: 'penerimaan_details'
            if (detailsError) {
                // If the plural name failed, maybe the singular table name is used
                if (detailsError.code === '42P01') {
                     const { error: detailsErrorAlt } = await supabase.from('penerimaan_detail').insert(detailsData);
                     if (detailsErrorAlt) throw detailsErrorAlt;
                } else {
                     throw detailsError;
                }
            }
        }

        // 4. Update Stok & Harga in `obat` table sequentially
        for (const item of form.items) {
            const obatCurrent = obatList.value.find(o => o.id === item.obat_id);
            if (obatCurrent) {
                const newStok = obatCurrent.stok + item.jumlah;
                let newHargaBeli = item.harga_satuan;
                let updateData = { stok: newStok, harga_beli: newHargaBeli };

                // Margin logic from Laravel API
                if (newHargaBeli > obatCurrent.harga_beli) {
                    const margin = 0.20;
                    const hargaJualBaru = newHargaBeli + (newHargaBeli * margin);
                    
                    if (hargaJualBaru > obatCurrent.harga_jual) {
                        updateData.harga_jual = Math.ceil(hargaJualBaru / 500) * 500;
                    }
                }

                const { error: updateError } = await supabase
                    .from('obat')
                    .update(updateData)
                    .eq('id', item.obat_id);
                    
                if (updateError) throw updateError;
            }
        }

        alert("Penerimaan Barang Berhasil Disimpan!");
        router.push('/admin/inventori');
    } catch (error) {
        console.error(error);
        errorMsg.value = error.message || "Terjadi kesalahan saat menyimpan data.";
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="penerimaan-container">
        
        <div class="header">
            <h3>📦 Penerimaan Barang Masuk</h3>
            <p>Input data obat masuk dari supplier untuk menambah stok.</p>
        </div>

        <div v-if="errorMsg" class="alert-error">⚠️ {{ errorMsg }}</div>

        <form @submit.prevent="submitPenerimaan" class="form-card">
            
            <div class="form-section">
                <h4>Informasi Supplier & Faktur</h4>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select v-model="form.supplier_id" required class="form-input">
                            <option value="" disabled>Pilih Supplier</option>
                            <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">
                                {{ sup.nama_suppliers }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Faktur</label>
                        <input v-model="form.no_faktur" type="text" required placeholder="Contoh: INV-001" class="form-input">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="flex-between">
                    <h4>Daftar Barang</h4>
                    <button type="button" @click="addItem" class="btn-sm btn-add">+ Tambah Item</button>
                </div>
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th width="100">Jumlah</th>
                            <th width="150">Harga Satuan (Rp)</th>
                            <th width="150">Subtotal</th>
                            <th width="50">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in form.items" :key="index">
                            <td>
                                <select v-model="item.obat_id" required class="form-input-sm">
                                    <option value="" disabled>Pilih Obat</option>
                                    <option v-for="obat in obatList" :key="obat.id" :value="obat.id">
                                        {{ obat.nama_obat }} (Stok: {{ obat.stok }})
                                    </option>
                                </select>
                            </td>
                            <td>
                                <input v-model.number="item.jumlah" type="number" min="1" required class="form-input-sm">
                            </td>
                            <td>
                                <input v-model.number="item.harga_satuan" type="number" min="0" required class="form-input-sm">
                            </td>
                            <td>
                                Rp {{ (item.jumlah * item.harga_satuan).toLocaleString() }}
                            </td>
                            <td>
                                <button type="button" @click="removeItem(index)" class="btn-del" :disabled="form.items.length === 1">×</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right label-total">Total Penerimaan:</td>
                            <td class="value-total">Rp {{ grandTotal.toLocaleString() }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-actions">
                <button type="button" @click="$router.back()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-save" :disabled="isLoading">
                    {{ isLoading ? 'Menyimpan...' : 'Simpan Penerimaan' }}
                </button>
            </div>

        </form>

    </div>
</template>

<style scoped>
.penerimaan-container { animation: fadeIn 0.3s ease; max-width: 900px; margin: 0 auto; }
.header { margin-bottom: 20px; }
.header h3 { margin: 0; color: var(--text-main); font-size: 22px; }
.header p { margin: 5px 0 0; color: var(--text-muted); font-size: 14px; }

.form-card { background: var(--bg-card); padding: 30px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }

.form-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
.form-section h4 { margin: 0 0 15px; color: var(--text-main); font-size: 16px; font-weight: 700; }
.form-section:last-child { border-bottom: none; }

.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: var(--text-muted); }
.form-input { width: 100%; padding: 10px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); border-radius: 8px; outline: none; transition: 0.2s; box-sizing: border-box;}
.form-input:focus { border-color: var(--primary); }

.items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.items-table th { text-align: left; padding: 10px; background: var(--bg-hover); font-size: 12px; color: var(--text-muted); }
.items-table td { padding: 10px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
.form-input-sm { width: 100%; padding: 8px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); border-radius: 6px; }

.btn-add { background: var(--info-bg); color: var(--info); border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 12px; }
.btn-del { background: var(--danger-bg); color: var(--danger); border: none; width: 24px; height: 24px; border-radius: 4px; cursor: pointer; font-weight: bold; }
.btn-del:disabled { opacity: 0.5; cursor: not-allowed; }

.text-right { text-align: right; }
.label-total { font-weight: 700; color: var(--text-main); padding-right: 15px; }
.value-total { font-weight: 800; color: var(--success); font-size: 16px; }

.form-actions { display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px; }
.btn-cancel { padding: 12px 25px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 10px; font-weight: 600; cursor: pointer; color: var(--text-muted); }
.btn-save { padding: 12px 25px; background: var(--primary); border: none; border-radius: 10px; font-weight: 600; cursor: pointer; color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
.btn-save:hover { background: var(--primary-dark); }

.alert-error { background: var(--danger-bg); color: var(--danger); padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid var(--danger-border); }

.flex-between { display: flex; justify-content: space-between; align-items: center; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
