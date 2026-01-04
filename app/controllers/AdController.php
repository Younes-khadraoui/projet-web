<?php

class AdController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $categoryController = new CategoryController($this->db);
        $categories = $categoryController->getAll();
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $delivery_type = isset($_POST['delivery_type']) ? implode(',', array_filter($_POST['delivery_type'])) : '';
            
            // Validate input
            if (strlen($title) < 5 || strlen($title) > 30) {
                $errors[] = "Le titre doit contenir entre 5 et 30 caractères.";
            }
            
            if (strlen($description) < 5 || strlen($description) > 200) {
                $errors[] = "La description doit contenir entre 5 et 200 caractères.";
            }
            
            if (!is_numeric($price) || $price < 0) {
                $errors[] = "Le prix doit être un nombre positif.";
            }
            
            if (!$category_id) {
                $errors[] = "Veuillez sélectionner une catégorie.";
            }
            
            if (!$delivery_type) {
                $errors[] = "Veuillez sélectionner au moins un mode de livraison.";
            }

            // Handle file uploads
            $uploaded_files = [];
            if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                if (count($_FILES['photos']['name']) > 5) {
                    $errors[] = "Maximum 5 photos autorisées.";
                } else {
                    foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                            $photo_file = [
                                'name' => $_FILES['photos']['name'][$key],
                                'tmp_name' => $tmp_name,
                                'size' => $_FILES['photos']['size'][$key],
                                'error' => $_FILES['photos']['error'][$key],
                            ];
                            
                            $validation = validateImageUpload($photo_file);
                            if (!$validation['success']) {
                                $errors[] = "Photo " . ($key + 1) . ": " . $validation['message'];
                                continue;
                            }

                            // Corrected path to be relative from the project root
                            $upload_dir = __DIR__ . '/../../public/uploads/';
                            if (!is_dir($upload_dir)) {
                                mkdir($upload_dir, 0755, true);
                            }
                            $filename = generateUniqueFilename($photo_file['name']);
                            $destination = $upload_dir . $filename;

                            if (move_uploaded_file($tmp_name, $destination)) {
                                $uploaded_files[] = $filename;
                            } else {
                                $errors[] = "Erreur lors du déplacement de la photo " . ($key + 1) . ".";
                            }
                        }
                    }
                }
            }
            
            if (empty($errors)) {
                // Create ad
                $ad = new Ad($this->db);
                $result = $ad->create(
                    $_SESSION['user_id'],
                    $category_id,
                    $title,
                    $description,
                    floatval($price),
                    $delivery_type
                );
                
                if ($result['success']) {
                    $ad_id = $result['ad_id'];
                    
                    // Upload photos
                    $upload_dir = __DIR__ . '/../../public/uploads/';
                    foreach ($uploaded_files as $index => $file) {
                        $unique_name = generateUniqueFilename($file['name']);
                        $target_path = $upload_dir . $unique_name;
                        
                        if (move_uploaded_file($file['tmp_name'], $target_path)) {
                            // Insert photo record (first photo is primary)
                            $stmt = $this->db->prepare(
                                'INSERT INTO photos (ad_id, filename, is_primary) VALUES (?, ?, ?)'
                            );
                            $is_primary = ($index === 0) ? 1 : 0;
                            $stmt->execute([$ad_id, $unique_name, $is_primary]);
                        }
                    }
                    
                    header('Location: /?action=dashboard');
                    exit;
                } else {
                    $errors[] = $result['message'];
                }
            }
        }
        
        return [
            'categories' => $categories,
            'errors' => $errors
        ];
    }
}
