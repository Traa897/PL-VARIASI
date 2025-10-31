<?php
/**
 * RECYCLE BIN CONTROLLER
 * File: motor_modif_shop/controllers/RecycleBinController.php
 * 
 * UPDATE: Tambah CSRF protection di semua POST actions
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Product.php';

class RecycleBinController extends BaseController {
    private $productModel;
    
    public function __construct($db) {
        $this->productModel = new Product($db);
    }
    
    public function index() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $products = $this->productModel->getTrashed($search, $page, $limit);
        $total = $this->productModel->countTrashed($search);
        $totalPages = ceil($total / $limit);
        
        $this->view('recyclebin/index', [
            'products' => $products,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    public function restore() {
        // ========================================
        // CSRF PROTECTION (NEW)
        // ========================================
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = $this->productModel->restore($id);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function restoreAll() {
        // ========================================
        // CSRF PROTECTION (NEW)
        // ========================================
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $result = $this->productModel->restoreAll();
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function forceDelete() {
        // ========================================
        // CSRF PROTECTION (NEW)
        // ========================================
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = $this->productModel->forceDelete($id);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function empty() {
        // ========================================
        // CSRF PROTECTION (NEW)
        // ========================================
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $result = $this->productModel->emptyTrash();
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    /**
     * Auto-Delete produk yang sudah >30 hari di Recycle Bin
     * GET request (no CSRF needed)
     */
    public function autoDelete() {
        $result = $this->productModel->runAutoDelete();
        
        if ($result['success']) {
            if ($result['deleted'] > 0) {
                $message = "Auto-delete berhasil: {$result['deleted']} produk dihapus otomatis (>30 hari di Recycle Bin)";
                if ($result['skipped'] > 0) {
                    $message .= ". {$result['skipped']} produk dilewati (masih ada di transaksi).";
                }
                $this->setFlash('success', $message);
            } else {
                $this->setFlash('info', $result['message']);
            }
        } else {
            $this->setFlash('danger', 'Gagal menjalankan auto-delete');
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
}
