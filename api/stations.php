<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $query = "
        SELECT 
            fs.id,
            fs.name,
            fs.address,
            fs.phone,
            MAX(CASE WHEN fp.fuel_type = 'petrol' THEN fp.price END) as petrol_price,
            MAX(CASE WHEN fp.fuel_type = 'diesel' THEN fp.price END) as diesel_price
        FROM fuel_stations fs
        LEFT JOIN fuel_prices fp ON fs.id = fp.station_id
        WHERE fs.is_active = 1
        GROUP BY fs.id, fs.name, fs.address, fs.phone
        ORDER BY fs.name
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get services for each station
    foreach ($stations as &$station) {
        $serviceQuery = "
            SELECT service_name, is_available, price, description 
            FROM station_services 
            WHERE station_id = :station_id 
              AND is_available = 1
              AND service_name IN ('Air Filling', 'Restrooms')
        ";
        $serviceStmt = $db->prepare($serviceQuery);
        $serviceStmt->bindParam(':station_id', $station['id']);
        $serviceStmt->execute();
        $station['services'] = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($stations);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch stations']);
}
?>