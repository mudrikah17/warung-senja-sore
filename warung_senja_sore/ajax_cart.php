<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? ($input['action'] ?? '');

switch($action) {
    case 'get':
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart']
        ]);
        break;
        
    case 'sync':
        if (isset($input['cart'])) {
            $_SESSION['cart'] = $input['cart'];
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'add':
        $id = $_POST['id'] ?? $input['id'] ?? null;
        $name = $_POST['name'] ?? $input['name'] ?? '';
        $price = $_POST['price'] ?? $input['price'] ?? 0;
        $foto = $_POST['foto'] ?? $input['foto'] ?? '';
        
        if ($id) {
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => 1,
                    'foto' => $foto
                ];
            }
        }
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        break;
        
    case 'update':
        $id = $_POST['id'] ?? $input['id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? $input['quantity'] ?? 0);
        
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                if ($quantity <= 0) {
                    $item['quantity'] = 0;
                } else {
                    $item['quantity'] = $quantity;
                }
                break;
            }
        }
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], fn($item) => $item['quantity'] > 0));
        echo json_encode(['success' => true]);
        break;
        
    case 'remove':
        $id = $_GET['id'] ?? $input['id'] ?? null;
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], fn($item) => $item['id'] != $id));
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
?>