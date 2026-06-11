<?php
// api.php - NEO Industry IT Management API v2.0
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

require_once __DIR__ . '/config/config.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["error" => "Connexion BDD impossible: " . $e->getMessage()]));
}

function sendJson($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
function getInput() {
    $data = json_decode(file_get_contents("php://input"));
    if (json_last_error() !== JSON_ERROR_NONE) sendJson(["error" => "JSON invalide"], 400);
    return $data;
}
function nStr($v)  { return (isset($v) && $v !== '') ? (string)$v  : null; }
function nInt($v)  { return (isset($v) && $v !== '') ? (int)$v     : null; }
function nFloat($v){ return (isset($v) && $v !== '') ? (float)$v   : null; }

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$entity = $_GET['entity'] ?? '';

// ═══════════════════════════════════════════════════════════════
// EMPLOYES
// ═══════════════════════════════════════════════════════════════
if ($entity === 'employees') {

    if ($method === 'GET' && $action === 'get_all') {
        $stmt = $conn->query(
            "SELECT e.id, e.firstname, e.lastname, e.email, e.phone,
                    e.department_id, e.service, e.position, e.hire_date, e.active, e.created_at,
                    d.name AS department
             FROM employees e
             LEFT JOIN departments d ON e.department_id = d.id
             ORDER BY e.lastname, e.firstname"
        );
        sendJson($stmt->fetchAll());
    }

    if ($method === 'GET' && $action === 'get' && isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT e.*, d.name AS department FROM employees e LEFT JOIN departments d ON e.department_id=d.id WHERE e.id=:id");
        $stmt->execute([':id' => (int)$_GET['id']]);
        $row = $stmt->fetch();
        $row ? sendJson($row) : sendJson(["error" => "Employé non trouvé"], 404);
    }

    if ($method === 'POST' && $action === 'create') {
        try {
            $d = getInput();
            if (empty($d->firstname) || empty($d->lastname)) sendJson(["error" => "Prénom et nom requis"], 400);
            $stmt = $conn->prepare("INSERT INTO employees (firstname,lastname,email,phone,department_id,service,position,hire_date,active) VALUES (:fn,:ln,:em,:ph,:dept,:srv,:pos,:hd,:act)");
            $stmt->execute([':fn'=>trim($d->firstname),':ln'=>trim($d->lastname),':em'=>nStr($d->email??null),':ph'=>nStr($d->phone??null),':dept'=>nInt($d->department_id??null),':srv'=>nStr($d->service??null),':pos'=>nStr($d->position??null),':hd'=>nStr($d->hire_date??null),':act'=>isset($d->active)?(int)$d->active:1]);
            $id = $conn->lastInsertId();
            $s2 = $conn->prepare("SELECT e.*,d.name AS department FROM employees e LEFT JOIN departments d ON e.department_id=d.id WHERE e.id=:id");
            $s2->execute([':id'=>$id]);
            sendJson(["message"=>"Employé créé","id"=>$id,"employee"=>$s2->fetch()], 201);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'PUT' && $action === 'update') {
        try {
            $d = getInput();
            if (empty($d->id)) sendJson(["error"=>"ID requis"], 400);
            $stmt = $conn->prepare("UPDATE employees SET firstname=:fn,lastname=:ln,email=:em,phone=:ph,department_id=:dept,service=:srv,position=:pos,hire_date=:hd,active=:act WHERE id=:id");
            $stmt->execute([':fn'=>trim($d->firstname),':ln'=>trim($d->lastname),':em'=>nStr($d->email??null),':ph'=>nStr($d->phone??null),':dept'=>nInt($d->department_id??null),':srv'=>nStr($d->service??null),':pos'=>nStr($d->position??null),':hd'=>nStr($d->hire_date??null),':act'=>isset($d->active)?(int)$d->active:1,':id'=>(int)$d->id]);
            sendJson(["message"=>"Employé mis à jour"]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'DELETE' && $action === 'delete' && isset($_GET['id'])) {
        try {
            $id = (int)$_GET['id'];
            $conn->prepare("UPDATE equipment SET employee_id=NULL,status='stock' WHERE employee_id=:id")->execute([':id'=>$id]);
            $conn->prepare("DELETE FROM employees WHERE id=:id")->execute([':id'=>$id]);
            sendJson(["message"=>"Employé supprimé"]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }
}

// ═══════════════════════════════════════════════════════════════
// DEPARTEMENTS
// ═══════════════════════════════════════════════════════════════
if ($entity === 'departments') {
    if ($method === 'GET' && $action === 'get_all') {
        sendJson($conn->query("SELECT id, name, description FROM departments ORDER BY name")->fetchAll());
    }
}

// ═══════════════════════════════════════════════════════════════
// TYPES D'EQUIPEMENT
// ═══════════════════════════════════════════════════════════════
if ($entity === 'equipment_types') {
    if ($method === 'GET' && $action === 'get_all') {
        sendJson($conn->query("SELECT id, name, expected_lifespan, maintenance_interval FROM equipment_types ORDER BY name")->fetchAll());
    }
    if ($method === 'POST' && $action === 'create') {
        try {
            $d = getInput();
            if (empty($d->name)) sendJson(["error"=>"Nom requis"], 400);
            $stmt = $conn->prepare("INSERT INTO equipment_types (name) VALUES (:name)");
            $stmt->execute([':name'=>trim($d->name)]);
            sendJson(["message"=>"Type créé","id"=>$conn->lastInsertId()], 201);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }
    if ($method === 'DELETE' && $action === 'delete' && isset($_GET['id'])) {
        try {
            $conn->prepare("DELETE FROM equipment_types WHERE id=:id")->execute([':id'=>(int)$_GET['id']]);
            sendJson(["message"=>"Type supprimé"]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }
}

// ═══════════════════════════════════════════════════════════════
// EQUIPEMENTS  (colonne BDD = purchase_date, PAS year)
// ═══════════════════════════════════════════════════════════════
if ($entity === 'equipment') {

    if ($method === 'GET' && $action === 'get_all') {
        $stmt = $conn->query(
            "SELECT e.id, et.name AS type, et.id AS type_id,
                    e.identifier, e.model, e.serial,
                    e.purchase_date, e.purchase_price,
                    e.warranty_until, e.location, e.notes,
                    e.employee_id, e.status, e.photo, e.created_at,
                    emp.firstname, emp.lastname, emp.service,
                    d.name AS department, emp.department_id
             FROM equipment e
             LEFT JOIN equipment_types et  ON e.type_id=et.id
             LEFT JOIN employees       emp ON e.employee_id=emp.id
             LEFT JOIN departments     d   ON emp.department_id=d.id
             ORDER BY e.created_at DESC"
        );
        sendJson($stmt->fetchAll());
    }

    if ($method === 'GET' && $action === 'get' && isset($_GET['id'])) {
        $stmt = $conn->prepare(
            "SELECT e.*, et.name AS type, et.id AS type_id,
                    emp.firstname, emp.lastname, d.name AS department
             FROM equipment e
             LEFT JOIN equipment_types et  ON e.type_id=et.id
             LEFT JOIN employees       emp ON e.employee_id=emp.id
             LEFT JOIN departments     d   ON emp.department_id=d.id
             WHERE e.id=:id"
        );
        $stmt->execute([':id'=>(int)$_GET['id']]);
        $row = $stmt->fetch();
        $row ? sendJson($row) : sendJson(["error"=>"Équipement non trouvé"], 404);
    }

    if ($method === 'POST' && $action === 'create') {
        try {
            $d = getInput();
            if (empty($d->type_id) || empty($d->model)) sendJson(["error"=>"Type et modèle requis"], 400);
            if (empty($d->identifier)) sendJson(["error"=>"L'identifiant est requis"], 400);
            $emp = nInt($d->employee_id??null);
            $st  = nStr($d->status??'stock') ?: 'stock';
            if ($emp && $st==='stock')    $st = 'assigned';
            if (!$emp && $st==='assigned') $st = 'stock';
            $stmt = $conn->prepare("INSERT INTO equipment (type_id,identifier,model,serial,purchase_date,purchase_price,warranty_until,location,notes,employee_id,status) VALUES (:tid,:idn,:mdl,:ser,:pd,:pp,:wu,:loc,:nt,:eid,:st)");
            $stmt->execute([':tid'=>(int)$d->type_id,':idn'=>nStr($d->identifier??null),':mdl'=>trim($d->model),':ser'=>nStr($d->serial??null),':pd'=>nStr($d->purchase_date??null),':pp'=>nFloat($d->purchase_price??null),':wu'=>nStr($d->warranty_until??null),':loc'=>nStr($d->location??null),':nt'=>nStr($d->notes??null),':eid'=>$emp,':st'=>$st]);
            $id = $conn->lastInsertId();
            $s2 = $conn->prepare("SELECT e.*,et.name AS type,et.id AS type_id,emp.firstname,emp.lastname,d.name AS department FROM equipment e LEFT JOIN equipment_types et ON e.type_id=et.id LEFT JOIN employees emp ON e.employee_id=emp.id LEFT JOIN departments d ON emp.department_id=d.id WHERE e.id=:id");
            $s2->execute([':id'=>$id]);
            sendJson(["message"=>"Équipement créé","id"=>$id,"equipment"=>$s2->fetch()], 201);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'PUT' && $action === 'update') {
        try {
            $d = getInput();
            if (empty($d->id)) sendJson(["error"=>"ID requis"], 400);
            $emp = nInt($d->employee_id??null);
            $st  = nStr($d->status??'stock') ?: 'stock';
            if ($emp && $st==='stock')    $st = 'assigned';
            if (!$emp && $st==='assigned') $st = 'stock';
            $stmt = $conn->prepare("UPDATE equipment SET type_id=:tid,identifier=:idn,model=:mdl,serial=:ser,purchase_date=:pd,purchase_price=:pp,warranty_until=:wu,location=:loc,notes=:nt,employee_id=:eid,status=:st WHERE id=:id");
            $stmt->execute([':tid'=>(int)$d->type_id,':idn'=>nStr($d->identifier??null),':mdl'=>trim($d->model),':ser'=>nStr($d->serial??null),':pd'=>nStr($d->purchase_date??null),':pp'=>nFloat($d->purchase_price??null),':wu'=>nStr($d->warranty_until??null),':loc'=>nStr($d->location??null),':nt'=>nStr($d->notes??null),':eid'=>$emp,':st'=>$st,':id'=>(int)$d->id]);
            sendJson(["message"=>"Équipement mis à jour"]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'DELETE' && $action === 'delete' && isset($_GET['id'])) {
        try {
            $conn->prepare("DELETE FROM equipment WHERE id=:id")->execute([':id'=>(int)$_GET['id']]);
            sendJson(["message"=>"Équipement supprimé"]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'PUT' && $action === 'update_status') {
        try {
            $d = getInput();
            $conn->prepare("UPDATE equipment SET status=:st WHERE id=:id")->execute([':st'=>$d->status,':id'=>(int)$d->id]);
            sendJson(["message"=>"Statut mis à jour"]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'GET' && $action === 'stats') {
        try {
            $g = $conn->query("SELECT COUNT(*) AS total, SUM(status='assigned') AS assigned, SUM(status IN ('maintenance','repair')) AS maintenance, SUM(status='stock') AS stock, SUM(status='repair') AS repair FROM equipment")->fetch();
            $byType = $conn->query("SELECT et.name AS type, COUNT(*) AS count FROM equipment e LEFT JOIN equipment_types et ON e.type_id=et.id GROUP BY et.id,et.name ORDER BY count DESC")->fetchAll();
            $byYear = $conn->query("SELECT YEAR(purchase_date) AS year, COUNT(*) AS count FROM equipment WHERE purchase_date IS NOT NULL GROUP BY YEAR(purchase_date) ORDER BY year DESC")->fetchAll();
            $byDept = $conn->query("SELECT d.name AS department, COUNT(*) AS count FROM equipment e JOIN employees emp ON e.employee_id=emp.id LEFT JOIN departments d ON emp.department_id=d.id WHERE e.employee_id IS NOT NULL GROUP BY d.id,d.name ORDER BY count DESC")->fetchAll();
            sendJson(['general'=>$g,'by_type'=>$byType,'by_year'=>$byYear,'by_department'=>$byDept]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }

    if ($method === 'GET' && $action === 'maintenance_alerts') {
        try {
            $stmt = $conn->query(
                "SELECT e.id, et.name AS type, e.model, e.identifier, e.purchase_date, e.status,
                        TIMESTAMPDIFF(MONTH, e.purchase_date, NOW()) AS age_months,
                        TIMESTAMPDIFF(YEAR,  e.purchase_date, NOW()) AS age_years,
                        emp.firstname, emp.lastname, d.name AS department
                 FROM equipment e
                 LEFT JOIN equipment_types et  ON e.type_id=et.id
                 LEFT JOIN employees       emp ON e.employee_id=emp.id
                 LEFT JOIN departments     d   ON emp.department_id=d.id
                 WHERE e.purchase_date IS NOT NULL
                   AND TIMESTAMPDIFF(YEAR, e.purchase_date, NOW()) >= 1
                   AND e.status NOT IN ('discarded','lost')
                 ORDER BY age_months DESC"
            );
            $a = $stmt->fetchAll();
            sendJson(['alerts'=>$a,'count'=>count($a)]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }
}


// ═══════════════════════════════════════════════════════════════
// MAINTENANCE LOGS
// ═══════════════════════════════════════════════════════════════
if ($entity === 'maintenance_logs') {

    if ($method === 'GET' && $action === 'get_all') {
        $where  = '1=1';
        $params = [];
        if (!empty($_GET['equipment_id'])) {
            $where .= ' AND ml.equipment_id = :eid';
            $params[':eid'] = (int)$_GET['equipment_id'];
        }
        $stmt = $conn->prepare(
            "SELECT ml.*,
                    et.name AS equipment_type,
                    e.model AS equipment_model,
                    e.identifier AS equipment_identifier
             FROM maintenance_logs ml
             LEFT JOIN equipment       e  ON ml.equipment_id = e.id
             LEFT JOIN equipment_types et ON e.type_id = et.id
             WHERE $where
             ORDER BY ml.created_at DESC"
        );
        $stmt->execute($params);
        sendJson($stmt->fetchAll());
    }

    if ($method === 'POST' && $action === 'create') {
        try {
            $d = getInput();
            if (empty($d->equipment_id) || empty($d->description) || empty($d->start_date)) {
                sendJson(["error" => "equipment_id, description et start_date sont requis"], 400);
            }
            $stmt = $conn->prepare(
                "INSERT INTO maintenance_logs
                    (equipment_id, maintenance_type, description, start_date, end_date,
                     cost, technician_name, status, notes)
                 VALUES (:eid, :mt, :desc, :sd, :ed, :cost, :tech, :st, :notes)"
            );
            $stmt->execute([
                ':eid'   => (int)$d->equipment_id,
                ':mt'    => nStr($d->maintenance_type ?? 'corrective') ?: 'corrective',
                ':desc'  => trim($d->description),
                ':sd'    => nStr($d->start_date),
                ':ed'    => nStr($d->end_date   ?? null),
                ':cost'  => nFloat($d->cost      ?? null),
                ':tech'  => nStr($d->technician_name ?? null),
                ':st'    => nStr($d->status      ?? 'pending') ?: 'pending',
                ':notes' => nStr($d->notes       ?? null),
            ]);
            // Si la maintenance est terminée, repasser l'équipement en stock ou assigned
            if (isset($d->status) && $d->status === 'completed') {
                $emp = $conn->prepare("SELECT employee_id FROM equipment WHERE id=:id");
                $emp->execute([':id' => (int)$d->equipment_id]);
                $row = $emp->fetch();
                $newStatus = ($row && $row['employee_id']) ? 'assigned' : 'stock';
                $conn->prepare("UPDATE equipment SET status=:st WHERE id=:id AND status='maintenance'")
                     ->execute([':st' => $newStatus, ':id' => (int)$d->equipment_id]);
            }
            sendJson(["message" => "Maintenance créée", "id" => $conn->lastInsertId()], 201);
        } catch (PDOException $e) { sendJson(["error" => $e->getMessage()], 500); }
    }

    if ($method === 'PUT' && $action === 'update') {
        try {
            $d = getInput();
            if (empty($d->id)) sendJson(["error" => "ID requis"], 400);
            $stmt = $conn->prepare(
                "UPDATE maintenance_logs
                 SET maintenance_type=:mt, description=:desc, start_date=:sd, end_date=:ed,
                     cost=:cost, technician_name=:tech, status=:st, notes=:notes
                 WHERE id=:id"
            );
            $stmt->execute([
                ':mt'    => nStr($d->maintenance_type ?? 'corrective'),
                ':desc'  => trim($d->description),
                ':sd'    => nStr($d->start_date),
                ':ed'    => nStr($d->end_date   ?? null),
                ':cost'  => nFloat($d->cost      ?? null),
                ':tech'  => nStr($d->technician_name ?? null),
                ':st'    => nStr($d->status      ?? 'pending'),
                ':notes' => nStr($d->notes       ?? null),
                ':id'    => (int)$d->id,
            ]);
            // Maintenance terminée → remettre l'équipement en service
            if (isset($d->status) && $d->status === 'completed' && isset($d->equipment_id)) {
                $emp = $conn->prepare("SELECT employee_id FROM equipment WHERE id=:id");
                $emp->execute([':id' => (int)$d->equipment_id]);
                $row = $emp->fetch();
                $newStatus = ($row && $row['employee_id']) ? 'assigned' : 'stock';
                $conn->prepare("UPDATE equipment SET status=:st WHERE id=:id AND status='maintenance'")
                     ->execute([':st' => $newStatus, ':id' => (int)$d->equipment_id]);
            }
            sendJson(["message" => "Maintenance mise à jour"]);
        } catch (PDOException $e) { sendJson(["error" => $e->getMessage()], 500); }
    }

    if ($method === 'DELETE' && $action === 'delete' && isset($_GET['id'])) {
        try {
            $conn->prepare("DELETE FROM maintenance_logs WHERE id=:id")->execute([':id' => (int)$_GET['id']]);
            sendJson(["message" => "Entrée supprimée"]);
        } catch (PDOException $e) { sendJson(["error" => $e->getMessage()], 500); }
    }
}

// ═══════════════════════════════════════════════════════════════
// HISTORIQUE DES ATTRIBUTIONS
// ═══════════════════════════════════════════════════════════════
if ($entity === 'assignments') {

    if ($method === 'GET' && $action === 'get_all') {
        $where  = '1=1';
        $params = [];
        if (!empty($_GET['equipment_id'])) {
            $where .= ' AND ea.equipment_id = :eid';
            $params[':eid'] = (int)$_GET['equipment_id'];
        }
        if (!empty($_GET['employee_id'])) {
            $where .= ' AND ea.employee_id = :empid';
            $params[':empid'] = (int)$_GET['employee_id'];
        }
        $stmt = $conn->prepare(
            "SELECT ea.*,
                    e.model AS equipment_model, e.identifier AS equipment_identifier,
                    et.name AS equipment_type,
                    emp.firstname, emp.lastname,
                    d.name AS department
             FROM equipment_assignments ea
             LEFT JOIN equipment       e   ON ea.equipment_id = e.id
             LEFT JOIN equipment_types et  ON e.type_id = et.id
             LEFT JOIN employees       emp ON ea.employee_id = emp.id
             LEFT JOIN departments     d   ON emp.department_id = d.id
             WHERE $where
             ORDER BY ea.assignment_date DESC"
        );
        $stmt->execute($params);
        sendJson($stmt->fetchAll());
    }

    if ($method === 'POST' && $action === 'create') {
        try {
            $d = getInput();
            if (empty($d->equipment_id) || empty($d->assignment_date)) {
                sendJson(["error" => "equipment_id et assignment_date requis"], 400);
            }
            $conn->prepare(
                "INSERT INTO equipment_assignments
                    (equipment_id, employee_id, assignment_date, return_date,
                     assignment_reason, replaced_equipment_id, replacement_reason, notes, created_by)
                 VALUES (:eid, :empid, :ad, :rd, :ar, :reid, :rr, :notes, :by)"
            )->execute([
                ':eid'   => (int)$d->equipment_id,
                ':empid' => nInt($d->employee_id ?? null),
                ':ad'    => nStr($d->assignment_date),
                ':rd'    => nStr($d->return_date ?? null),
                ':ar'    => nStr($d->assignment_reason ?? 'other'),
                ':reid'  => nInt($d->replaced_equipment_id ?? null),
                ':rr'    => nStr($d->replacement_reason ?? null),
                ':notes' => nStr($d->notes ?? null),
                ':by'    => nStr($d->created_by ?? null),
            ]);
            sendJson(["message" => "Attribution enregistrée", "id" => $conn->lastInsertId()], 201);
        } catch (PDOException $e) { sendJson(["error" => $e->getMessage()], 500); }
    }
}

// ═══════════════════════════════════════════════════════════════
// SETTINGS
// ═══════════════════════════════════════════════════════════════
if ($entity === 'settings') {
    if ($action === 'reset_system' && $method === 'POST') {
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS=0");
            $conn->exec("TRUNCATE TABLE equipment");
            $conn->exec("TRUNCATE TABLE employees");
            $conn->exec("TRUNCATE TABLE maintenance_logs");
            $conn->exec("SET FOREIGN_KEY_CHECKS=1");
            sendJson(["message"=>"Système réinitialisé."]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }
    if ($action === 'repair_data' && $method === 'POST') {
        try {
            $conn->exec("UPDATE equipment SET status='stock' WHERE employee_id IS NULL AND status='assigned'");
            $conn->exec("UPDATE equipment SET status='assigned' WHERE employee_id IS NOT NULL AND status='stock'");
            $conn->exec("UPDATE equipment e LEFT JOIN employees emp ON e.employee_id=emp.id SET e.employee_id=NULL,e.status='stock' WHERE e.employee_id IS NOT NULL AND emp.id IS NULL");
            sendJson(["message"=>"Données réparées."]);
        } catch (PDOException $e) { sendJson(["error"=>$e->getMessage()], 500); }
    }
}

// Racine
if ($entity==='' && $action==='') {
    sendJson(['api'=>'NEO Industry IT Management API','version'=>'2.0','status'=>'online','timestamp'=>date('Y-m-d H:i:s')]);
}
