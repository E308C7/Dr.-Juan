<?php
header('Content-Type: application/json');
$dataFile = __DIR__ . '/pacientes_data.json';

// Leer datos existentes
function getPatients($dataFile) {
    if (!file_exists($dataFile)) {
        // Datos iniciales si el archivo no existe
        $initial = [
            ["id"=>1,"name"=>"Ana López","dob"=>"1990-05-12","condition"=>"Diabetes","notes"=>"Control mensual. Última revisión bien."],
            ["id"=>2,"name"=>"Carlos Pérez","dob"=>"1985-11-23","condition"=>"Hipertensión","notes"=>"Requiere ajuste de medicación."],
            ["id"=>3,"name"=>"María García","dob"=>"1978-02-17","condition"=>"Asma","notes"=>"Sin crisis recientes."]
        ];
        file_put_contents($dataFile, json_encode($initial, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $initial;
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true) ?: [];
}

// Guardar datos
function savePatients($dataFile, $patients) {
    file_put_contents($dataFile, json_encode($patients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// GET: devolver lista
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(getPatients($dataFile), JSON_UNESCAPED_UNICODE);
    exit;
}

// POST: agregar o editar paciente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $patients = getPatients($dataFile);

    if (isset($input['id']) && $input['id']) {
        // Editar
        foreach ($patients as &$p) {
            if ($p['id'] == $input['id']) {
                $p['name'] = $input['name'];
                $p['dob'] = $input['dob'];
                $p['condition'] = $input['condition'];
                $p['notes'] = $input['notes'];
                break;
            }
        }
    } else {
        // Agregar
        $newId = count($patients) ? max(array_column($patients, 'id')) + 1 : 1;
        $patients[] = [
            "id" => $newId,
            "name" => $input['name'],
            "dob" => $input['dob'],
            "condition" => $input['condition'],
            "notes" => $input['notes']
        ];
    }
    savePatients($dataFile, $patients);
    echo json_encode(["success"=>true]);
    exit;
}
?>
