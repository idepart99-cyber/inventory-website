<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Database\Database;

class ApiController extends Controller
{
    public function barcodeLookup(string $code): void
    {
        $product = Product::findByBarcode($code);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found with barcode / SKU: ' . $code], 404);
        }

        $stockByLoc = Product::getStockByLocation((int)$product['id']);
        $product['locations'] = $stockByLoc;

        $this->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function search(): void
    {
        $query = (string)$this->request('q', '');
        if (strlen($query) < 2) {
            $this->json([]);
        }

        $products = Product::all(['search' => $query], 10, 0);
        $this->json($products);
    }

    public function locationStock(string $productId, string $locationId): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT quantity FROM inventory WHERE product_id = ? AND location_id = ?");
        $stmt->execute([(int)$productId, (int)$locationId]);
        $qty = (int)$stmt->fetchColumn();

        $this->json([
            'success' => true,
            'quantity' => $qty
        ]);
    }
}
