<!-- views/recyclebin/index.php - FIXED -->

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

    <!-- INFORMASI ATAU TABLE -->
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Total: <?= $total ?> produk di Recycle Bin</p>
            
            <?php if (empty($products)): ?>
                <!-- TAMPILAN KETIKA KOSONG -->
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h4>Recycle Bin Kosong</h4>
                    <p class="mb-0">Tidak ada produk yang dihapus. Semua produk sudah dikembalikan atau dihapus permanen.</p>
                    <hr class="my-4">
                    <a href="index.php?c=products&a=index" class="btn btn-primary">
                        <i class="fas fa-box"></i> Lihat Semua Produk
                    </a>
                </div>
            <?php else: ?>
                <!-- TABLE PRODUK TERHAPUS -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
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
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($product['code']) ?></span></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($product['category_name']) ?></span></td>
                                <td>
                                    <?php 
                                    $deletedAt = strtotime($product['deleted_at']);
                                    $daysAgo = floor((time() - $deletedAt) / (60 * 60 * 24));
                                    echo date('d/m/Y H:i', $deletedAt);
                                    ?>
                                    <br>
                                    <small class="text-muted">(<?= $daysAgo ?> hari yang lalu)</small>
                                </td>
                                <td>
                                    <!-- RESTORE BUTTON -->
                                    <form method="POST" action="index.php?c=recyclebin&a=restore" style="display:inline;">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" title="Kembalikan">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- FORCE DELETE BUTTON -->
                                    <form method="POST" action="index.php?c=recyclebin&a=forceDelete" style="display:inline;"
                                          onsubmit="return confirm('PERINGATAN! Produk akan dihapus permanen. Lanjutkan?')">
                                        <?= Csrf::field() ?>
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

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?c=recyclebin&a=index&page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
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
                    <?= Csrf::field() ?>
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
                <p class="mb-0"><small class="text-muted">Catatan: Produk yang masih ada di transaksi tidak akan dihapus.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="index.php?c=recyclebin&a=empty" style="display:inline;">
                    <?= Csrf::field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>