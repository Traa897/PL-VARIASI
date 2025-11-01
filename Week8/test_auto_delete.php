<?php
/**
 * TEST AUTO-DELETE (>30 hari)
 * File: Week8/test_auto_delete.php
 * 
 * Test Week 6 Enhancement - Auto-Delete Old Records
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');
require_once BASE_PATH . 'config/database.php';
require_once BASE_PATH . 'models/Product.php';

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Check produk yang akan dihapus
$checkSql = "SELECT p.*, 
             DATEDIFF(NOW(), p.deleted_at) as days_in_trash,
             (SELECT COUNT(*) FROM transaction_details WHERE product_id = p.id) as transaction_count
             FROM products p
             WHERE p.deleted_at IS NOT NULL 
             AND p.deleted_at <= DATE_SUB(NOW(), INTERVAL 30 DAY)
             ORDER BY p.deleted_at ASC";

$result = $db->query($checkSql);
$oldProducts = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Auto-Delete (>30 hari)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 40px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h1 class="mb-0"><i class="fas fa-clock"></i> Test Auto-Delete (>30 hari)</h1>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Informasi:</strong> 
                    Auto-delete akan menghapus produk yang sudah lebih dari 30 hari di Recycle Bin.
                </div>
                
                <?php if (empty($oldProducts)): ?>
                    <div class="alert alert-success text-center py-5">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>Tidak ada produk yang perlu dihapus otomatis</h4>
                        <p class="mb-0">Semua produk di Recycle Bin masih kurang dari 30 hari</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-triangle"></i> Ditemukan <?= count($oldProducts) ?> produk</strong> 
                        yang akan dihapus otomatis:
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Deleted At</th>
                                    <th>Days in Trash</th>
                                    <th>In Transactions?</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($oldProducts as $product): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($product['code']) ?></span></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($product['deleted_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-fire"></i> <?= $product['days_in_trash'] ?> hari
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($product['transaction_count'] > 0): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                Yes (<?= $product['transaction_count'] ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> No
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['transaction_count'] > 0): ?>
                                            <span class="text-muted"><i class="fas fa-ban"></i> Will be skipped</span>
                                        <?php else: ?>
                                            <span class="text-danger"><i class="fas fa-trash"></i> Will be deleted</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="index.php?c=recyclebin&a=autoDelete" 
                           class="btn btn-danger btn-lg px-5 py-3"
                           onclick="return confirm('⚠️ PERINGATAN!\n\nIni akan MENGHAPUS PERMANEN produk yang >30 hari di Recycle Bin.\n\nProduk yang masih ada di transaksi akan dilewati.\n\nLanjutkan?')">
                            <i class="fas fa-trash-alt"></i> Jalankan Auto-Delete Sekarang
                        </a>
                    </div>
                <?php endif; ?>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between">
                    <a href="index.php?c=recyclebin&a=index" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Recycle Bin
                    </a>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($oldProducts)): ?>
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Cara Kerja Auto-Delete</h5>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>Scan Recycle Bin:</strong> Sistem akan mencari produk yang deleted_at > 30 hari</li>
                    <li><strong>Check Transaksi:</strong> Produk yang masih ada di transaksi akan DILEWATI (skip)</li>
                    <li><strong>Hard Delete:</strong> Produk yang tidak ada di transaksi akan DIHAPUS PERMANEN</li>
                    <li><strong>Delete Image:</strong> File gambar produk (jika ada) juga akan dihapus</li>
                </ol>
                
                <div class="alert alert-warning mt-3">
                    <strong><i class="fas fa-exclamation-triangle"></i> Catatan Penting:</strong>
                    <ul class="mb-0">
                        <li>Data yang sudah dihapus permanen TIDAK BISA dikembalikan</li>
                        <li>Sebaiknya jalankan auto-delete secara berkala (misalnya 1 bulan sekali)</li>
                        <li>Atau bisa dibuat cron job untuk otomatis</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $database->close(); ?>