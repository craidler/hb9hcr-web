<?php
// Set headers to communicate with JSON
header('Content-Type: application/json');

// Check if the request contains the file field
if (!isset($_FILES['webcam_snapshot'])) {
    echo json_encode(['status' => 'error', 'message' => 'No file received.']);
    exit;
}

$file = $_FILES['webcam_snapshot'];

// Basic validation: Check if file transfer had errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'upload error code: ' . $file['error']]);
    exit;
}

// Security: Define the upload directory and create it if it doesn't exist
$uploadDir = __DIR__ . '/';

// Security: Enforce allowed extensions (only allow standard images)
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($fileExtension !== 'jpg' && $fileExtension !== 'jpeg') {
    echo json_encode(['status' => 'error', 'message' => 'invalid file format. only jpg is allowed.']);
    exit;
}

// Generate a completely unique name using the current date and time
$filename = 'snapshot.jpg';
$destination = $uploadDir . $filename;

// Move the temporary upload file to its permanent destination
if (move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode([
        'status' => 'success', 
        'message' => 'image saved successfully.',
        'filename' => $filename
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'failed to save file on the server.']);
}