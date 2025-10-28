<?php
require("../db.php");

$aircraft_id = null;

if (isset($_GET['id'])) {
    $aircraft_id = (int)$_GET['id'];
} else {
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path_parts = explode('/', trim($path, '/'));
    
    if (count($path_parts) >= 3 && $path_parts[2] !== '' && is_numeric($path_parts[2])) {
        $aircraft_id = (int)$path_parts[2];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
	
	if ($aircraft_id) {
		$sql = "
			SELECT 
				a.id,
				a.Model,
				a.engine_amount,
				a.passenger_capacity,
				a.range_in_km,
				at.type_name AS airplane_type,
				et.engine_name AS engine_type,
				m.image_url
			FROM Aircrafts a
			LEFT JOIN media m ON a.media_id = m.id
			LEFT JOIN airplane_types at ON a.airplane_type_id = at.id
			LEFT JOIN engine_types et ON a.engine_type_id = et.id
			WHERE a.id = :id
			LIMIT 1
		";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":id", $aircraft_id, PDO::PARAM_INT);
		$stmt->execute();
		$aircraft = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$aircraft) {
			http_response_code(404);
			echo json_encode(["error" => "Aircraft not found"], JSON_PRETTY_PRINT);
			exit;
		}
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($aircraft, JSON_PRETTY_PRINT);
		
	} else {
		$sql = "SELECT id, Model FROM Aircrafts ORDER BY id";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$result = [];
		foreach ($aircrafts as $aircraft) {
			$result[] = [
				'id' => (int)$aircraft['id'],
				'model' => $aircraft['Model'],
				'link' => "http://localhost:8888/aircraft-api/aircrafts/?id=" . $aircraft['id']
			];
		}
		
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($result, JSON_PRETTY_PRINT);
	}
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$model = $_POST["model"];
	$engine_amount = $_POST["engine_amount"];
	$passenger_capacity = $_POST["passenger_capacity"];
	$range_in_km = $_POST["range_in_km"];

	$stmt = $conn->prepare("INSERT INTO Aircrafts (`Model`, `engine_amount`, `passenger_capacity`, `range_in_km`)
													VALUES(:model, :engine_amount, :passenger_capacity, :range_in_km)");
	
	$stmt->bindParam(":model", $model);
	$stmt->bindParam(":engine_amount", $engine_amount, PDO::PARAM_INT);
	$stmt->bindParam(":passenger_capacity", $passenger_capacity, PDO::PARAM_INT);
	$stmt->bindParam(":range_in_km", $range_in_km, PDO::PARAM_INT);

	$stmt->execute();
	
	header("Content-Type: application/json; charset=utf-8");
	http_response_code(201);
	echo json_encode(["message" => "Aircraft created successfully"], JSON_PRETTY_PRINT);
	exit;
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
	if (!$aircraft_id) {
		http_response_code(400);
		echo json_encode(["error" => "Aircraft ID is required in URL path"], JSON_PRETTY_PRINT);
		exit;
	}
	
	$input = file_get_contents('php://input');
	parse_str($input, $data);
	
	$model = $data["model"] ?? null;
	$engine_amount = $data["engine_amount"] ?? null;
	$passenger_capacity = $data["passenger_capacity"] ?? null;
	$range_in_km = $data["range_in_km"] ?? null;
	$media_id = $data["media_id"] ?? null;

	$stmt = $conn->prepare("UPDATE Aircrafts SET 
													`Model` = :model, 
													`engine_amount` = :engine_amount,
													`passenger_capacity` = :passenger_capacity,
													`range_in_km` = :range_in_km,
													`media_id` = :media_id
													WHERE `id` = :id");
	
	$stmt->bindParam(":id", $aircraft_id, PDO::PARAM_INT);
	$stmt->bindParam(":model", $model);
	$stmt->bindParam(":engine_amount", $engine_amount, PDO::PARAM_INT);
	$stmt->bindParam(":passenger_capacity", $passenger_capacity, PDO::PARAM_INT);
	$stmt->bindParam(":range_in_km", $range_in_km, PDO::PARAM_INT);
	$stmt->bindParam(":media_id", $media_id, PDO::PARAM_INT);

	if ($stmt->execute()) {
		if ($stmt->rowCount() > 0) {
			echo json_encode(["message" => "Aircraft updated successfully"]);
		} else {
			http_response_code(404);
			echo json_encode(["error" => "Aircraft not found"]);
		}
	} else {
		http_response_code(500);
		echo json_encode(["error" => "Failed to update aircraft"]);
	}
	exit;
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
	header("Content-Type: application/json; charset=utf-8");
	
	$input = file_get_contents('php://input');
	parse_str($input, $data);
	
	$id = $aircraft_id ?? $data["id"] ?? null;
	
	if (!$id) {
		http_response_code(400);
		echo json_encode([
			"error" => "Aircraft ID is required"
		], JSON_PRETTY_PRINT);
		exit;
	}

	$stmt = $conn->prepare("DELETE FROM Aircrafts WHERE `id` = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

	if ($stmt->execute()) {
		if ($stmt->rowCount() > 0) {
			echo json_encode([
				"message" => "Aircraft deleted successfully",
				"deleted_id" => $id
			], JSON_PRETTY_PRINT);
		} else {
			http_response_code(404);
			echo json_encode([
				"error" => "Aircraft not found",
				"searched_id" => $id
			], JSON_PRETTY_PRINT);
		}
	} else {
		http_response_code(500);
		echo json_encode([
			"error" => "Failed to delete aircraft"
		], JSON_PRETTY_PRINT);
	}
	exit;
}
?>