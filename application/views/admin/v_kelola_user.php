<div class="container-fluid pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users-cog mr-2"></i><?= $title; ?></h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahUser">
            <i class="fas fa-user-plus mr-2"></i> Tambah User Baru
        </button>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success'); ?>
            <button type="text" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error'); ?>
            <button type="text" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-table mr-2"></i>Daftar Pengguna Aktif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Penugasan Pos</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($users_list as $u): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><b><?= $u['nama_lengkap']; ?></b></td>
                            <td><code><?= $u['username']; ?></code></td>
                            <td><?= $u['email']; ?></td>
                            <td class="text-center">
                                <span class="badge badge-<?= ($u['role']=='superadmin'?'danger':($u['role']=='admin'?'warning':'info')); ?> p-2">
                                    <?= strtoupper($u['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    if ($u['role'] === 'petugas' && !empty($u['id_pos'])) {
                                        $pos_query = $this->db->get_where('master_pos', ['id_pos' => $u['id_pos']])->row();
                                        echo $pos_query ? '<i class="fas fa-map-marker-alt text-danger mr-1"></i> ' . $pos_query->nama_pos : '<span class="text-muted">Pos Terhapus</span>';
                                    } else {
                                        echo '<span class="text-muted text-center d-block">-</span>';
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-<?= $u['status']=='aktif'?'success':'secondary'; ?>-light text-capitalize">
                                    <?= $u['status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning mb-1" data-toggle="modal" data-target="#modalEditUser<?= $u['id_user']; ?>" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= base_url('admin/hapus_user/'.$u['id_user']); ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?');" title="Hapus User">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditUser<?= $u['id_user']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Edit Pengguna</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    </div>
                                    <form action="<?= base_url('admin/ubah_user'); ?>" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_user" value="<?= $u['id_user']; ?>">
                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control" value="<?= $u['nama_lengkap']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control" value="<?= $u['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= $u['email']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Password Baru <small class="text-danger">(Kosongkan jika tidak diganti)</small></label>
                                                <input type="password" name="password" class="form-control" placeholder="Masukkan password baru jika ingin diganti">
                                            </div>
                                            <div class="form-group">
                                                <label>Role Tingkat Akses</label>
                                                <select name="role" class="form-control select-role-edit" data-id="<?= $u['id_user']; ?>" required>
                                                    <option value="superadmin" <?= $u['role']=='superadmin'?'selected':''; ?>>Super Admin (Pusat)</option>
                                                    <option value="admin" <?= $u['role']=='admin'?'selected':''; ?>>Admin (Wilayah/Balai)</option>
                                                    <option value="petugas" <?= $u['role']=='petugas'?'selected':''; ?>>Petugas (Pos Lapangan)</option>
                                                </select>
                                            </div>
                                            <div class="form-group id-pos-wrapper-edit-<?= $u['id_user']; ?>" style="<?= $u['role']=='petugas'?'':'display:none;'; ?>">
                                                <label>Penugasan Lokasi Pos</label>
                                                <select name="id_pos" class="form-control">
                                                    <option value="">-- Pilih Penugasan Pos --</option>
                                                    <?php foreach($pos_list as $p): ?>
                                                        <option value="<?= $p['id_pos']; ?>" <?= $u['id_pos']==$p['id_pos']?'selected':''; ?>><?= $p['nama_pos']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Status Akun</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="aktif" <?= $u['status']=='aktif'?'selected':''; ?>>Aktif</option>
                                                    <option value="nonaktif" <?= $u['status']=='nonaktif'?'selected':''; ?>>Non-Aktif/Blokir</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                            <button class="btn btn-warning" type="submit">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Registrasi Pengguna Baru</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form action="<?= base_url('admin/simpan_user'); ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Muhammad Hanif Saputra" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: hanifsaputra" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Contoh: hanif@unila.ac.id" required>
                    </div>
                    <div class="form-group">
                        <label>Password Awal</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password akun" required>
                    </div>
                    <div class="form-group">
                        <label>Role Tingkat Akses</label>
                        <select name="role" id="selectRoleTambah" class="form-control" required>
                            <option value="admin">Admin (Wilayah/Balai)</option>
                            <option value="petugas" selected>Petugas (Pos Lapangan)</option>
                            <option value="superadmin">Super Admin (Pusat)</option>
                        </select>
                    </div>
                    <div class="form-group" id="idPosWrapperTambah">
                        <label>Penugasan Lokasi Pos</label>
                        <select name="id_pos" class="form-control">
                            <option value="">-- Pilih Penugasan Pos --</option>
                            <?php foreach($pos_list as $p): ?>
                                <option value="<?= $p['id_pos']; ?>"><?= $p['nama_pos']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Akun</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Daftarkan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Kontrol Dropdown Pos pada Modal Tambah
    const selectRoleTambah = document.getElementById("selectRoleTambah");
    const idPosWrapperTambah = document.getElementById("idPosWrapperTambah");
    
    selectRoleTambah.addEventListener("change", function() {
        if (this.value === "petugas") {
            idPosWrapperTambah.style.display = "block";
        } else {
            idPosWrapperTambah.style.display = "none";
        }
    });

    // Kontrol Dropdown Pos pada Modal Edit (Menggunakan class dinamis)
    const selectRoleEdits = document.querySelectorAll(".select-role-edit");
    selectRoleEdits.forEach(select => {
        select.addEventListener("change", function() {
            const idUser = this.getAttribute("data-id");
            const wrapper = document.querySelector(".id-pos-wrapper-edit-" + idUser);
            if (this.value === "petugas") {
                wrapper.style.display = "block";
            } else {
                wrapper.style.display = "none";
            }
        });
    });
});
</script>