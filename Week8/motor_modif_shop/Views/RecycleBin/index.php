<!-- views/recyclebin/index.php - CSRF FIXED -->

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2><i class="fas fa-trash-restore"></i> Recycle Bin - Produk Terhapus</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php?c=products&a=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Produk
            </a>
            <?php if ($total > 0): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#restoreAllModal">
                <i class="fas fa-undo-alt"></i> Kembalikan Semua
            </button>
            <a href="index.php?c=recyclebin&a=autoDelete" class="btn btn-warning"
               onclick="return confirm('Jalankan auto-delete untuk produk >30 hari?')">
                <i class="fas fa-clock"></i> Auto-Delete (>30 hari)
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
                <i class="fas fa-trash-alt"></i> Kosongkan Recycle Bin
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table produk terhapus -->
    <?php if (!empty($products)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Dihapus Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = ($page - 1) * 10 + 1;
                    foreach($products as $product): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($product['code']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($product['deleted_at'])) ?></td>
                        <td>
                            <!-- RESTORE BUTTON -->
                            <form method="POST" action="index.php?c=recyclebin&a=restore" style="display:inline;">
                                <?= Csrf::field() ?> <!-- CSRF TOKEN -->
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-success" title="Kembalikan">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                            
                            <!-- FORCE DELETE BUTTON -->
                            <form method="POST" action="index.php?c=recyclebin&a=forceDelete" style="display:inline;"
                                  onsubmit="return confirm('PERINGATAN! Produk akan dihapus permanen. Lanjutkan?')">
                                <?= Csrf::field() ?> <!-- CSRF TOKEN -->
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- RESTORE ALL MODAL -->
<div class="modal fade" id="restoreAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-undo-alt"></i> Konfirmasi Kembalikan Semua</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan mengembalikan <strong>SEMUA <?= $total ?> produk</strong> dari Recycle Bin.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="index.php?c=recyclebin&a=restoreAll" style="display:inline;">
                    <?= Csrf::field() ?> <!-- CSRF TOKEN -->
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo-alt"></i> Ya, Kembalikan Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- EMPTY TRASH MODAL -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Kosongkan Recycle Bin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>PERINGATAN!</strong>
                </div>
                <p>Data yang sudah dihapus <strong>TIDAK BISA dikembalikan</strong>.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="index.php?c=recyclebin&a=empty" style="display:inline;">
                    <?= Csrf::field() ?> <!-- CSRF TOKEN -->
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>