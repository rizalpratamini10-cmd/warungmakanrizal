<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header("Location: pesanan.php");
    exit();
}

// Ambil data pesanan
$order = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name, u.alamat, u.no_hp 
                          FROM orders o 
                          JOIN users u ON o.customer_id = u.id 
                          WHERE o.id = $order_id")->fetch_assoc();

if (!$order) {
    header("Location: pesanan.php");
    exit();
}

// Ambil detail produk
$items = $koneksi->query("SELECT od.*, p.nama_produk 
                          FROM order_details od 
                          JOIN products p ON od.product_id = p.id 
                          WHERE od.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #<?php echo $order['order_number']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .struk {
            background: white;
            width: 350px;
            max-width: 100%;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            border-radius: 5px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .info {
            border-bottom: 1px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .info p {
            margin: 3px 0;
        }
        .items {
            width: 100%;
            border-bottom: 1px dashed #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .items th, .items td {
            text-align: left;
            padding: 4px 0;
        }
        .items th:last-child, .items td:last-child {
            text-align: right;
        }
        .total {
            text-align: right;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            border-top: 1px dashed #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .btn-print {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 10px;
            background: #6F4E37;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-print:hover {
            background: #A0522D;
        }
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .btn-print {
                display: none;
            }
            .struk {
                box-shadow: none;
                padding: 10px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="header">
            <h2>WARUNG MAKAN RIZAL</h2>
            <p>Samping Kantor Camat Lubuk Baja, Batam</p>
            <p>Telp: 0812-3456-7890</p>
        </div>
        
        <div class="info">
            <p><strong>No. Pesanan:</strong> #<?php echo $order['order_number']; ?></p>
            <p><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Customer:</strong> <?php echo $order['customer_name']; ?></p>
            <p><strong>Alamat:</strong> <?php echo $order['alamat']; ?></p>
            <p><strong>No. HP:</strong> <?php echo $order['no_hp']; ?></p>
        </div>
        
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $item['nama_produk']; ?></td>
                    <td><?php echo $item['quantity']; ?>x</td>
                    <td><?php echo format_rupiah($item['harga_per_item']); ?></td>
                    <td><?php echo format_rupiah($item['subtotal']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="total">
            <p>Total: <?php echo format_rupiah($order['total_harga']); ?></p>
            <?php if($order['status'] == 'selesai'): ?>
                <p>Status: ✅ Selesai</p>
            <?php else: ?>
                <p>Status: ⏳ <?php echo ucfirst($order['status']); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>Terima kasih telah berbelanja!</p>
            <p>*** Layanan Antar 24 Jam ***</p>
        </div>
        
        <button class="btn-print" onclick="window.print();">🖨️ Cetak Struk</button>
    </div>
</body>
</html>