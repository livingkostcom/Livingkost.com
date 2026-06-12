<?php
header('Content-Type: application/json');

// Sample listings data
$listings = [
    [
        'id' => 1,
        'title' => 'Kost Nyaman di Depok',
        'price' => 'Rp 1.500.000/bulan',
        'location' => 'Depok, Jawa Barat',
        'description' => 'Kost dengan fasilitas lengkap, WiFi, AC, dan air panas'
    ],
    [
        'id' => 2,
        'title' => 'Kost Strategis di Jakarta',
        'price' => 'Rp 2.000.000/bulan',
        'location' => 'Jakarta Pusat',
        'description' => 'Lokasi strategis dekat dengan pusat bisnis dan transportasi umum'
    ],
    [
        'id' => 3,
        'title' => 'Kost Terjangkau di Bogor',
        'price' => 'Rp 1.200.000/bulan',
        'location' => 'Bogor, Jawa Barat',
        'description' => 'Kost terjangkau dengan lingkungan yang tenang dan aman'
    ]
];

// Return JSON response
http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $listings,
    'count' => count($listings)
]);
?>
