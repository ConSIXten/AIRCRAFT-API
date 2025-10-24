<?php
require("../db.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
	$stmt = $conn->prepare("SELECT * FROM Aircrafts");
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($result);
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
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
	// Get the raw PUT data
	$input = file_get_contents('php://input');
	parse_str($input, $data);
	
	$id = $data["id"] ?? null;
	$model = $data["model"] ?? null;
	$engine_amount = $data["engine_amount"] ?? null;
	$passenger_capacity = $data["passenger_capacity"] ?? null;
	$range_in_km = $data["range_in_km"] ?? null;

	if (!$id) {
		http_response_code(400);
		echo json_encode(["error" => "Aircraft ID is required"]);
		exit;
	}

	$stmt = $conn->prepare("UPDATE Aircrafts SET 
													`Model` = :model, 
													`engine_amount` = :engine_amount,
													`passenger_capacity` = :passenger_capacity,
													`range_in_km` = :range_in_km
													WHERE `id` = :id");
	
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->bindParam(":model", $model);
	$stmt->bindParam(":engine_amount", $engine_amount, PDO::PARAM_INT);
	$stmt->bindParam(":passenger_capacity", $passenger_capacity, PDO::PARAM_INT);
	$stmt->bindParam(":range_in_km", $range_in_km, PDO::PARAM_INT);

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
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
	// Get the raw DELETE data
	$input = file_get_contents('php://input');
	parse_str($input, $data);
	
	$id = $data["id"] ?? null;

	if (!$id) {
		http_response_code(400);
		echo json_encode(["error" => "Aircraft ID is required"]);
		exit;
	}

	$stmt = $conn->prepare("DELETE FROM Aircrafts WHERE `id` = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

	if ($stmt->execute()) {
		if ($stmt->rowCount() > 0) {
			echo json_encode(["message" => "Aircraft deleted successfully"]);
		} else {
			http_response_code(404);
			echo json_encode(["error" => "Aircraft not found"]);
		}
	} else {
		http_response_code(500);
		echo json_encode(["error" => "Failed to delete aircraft"]);
	}
}
?>