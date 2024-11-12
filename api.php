<?php
$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = 'root';
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

function GetAllFrom_k72623_lo($pdo)
{
    $sql = 'SELECT * FROM k72623_lo ORDER BY time DESC';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao executar a consulta: ' . $e->getMessage()]);
    }
}

function GetAllFrom_nit2xli($pdo)
{
    $sql = 'SELECT * FROM nit2xli ORDER BY time DESC';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao executar a consulta: ' . $e->getMessage()]);
    }
}

function GetDadosDevice($pdo, $data)
{
    $sql = 'SELECT * FROM ' . $data['tabela'] . ' WHERE "devEui"=' . $data['dispositivo']. 'ORDER BY time DESC';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao executar a consulta: ' . $e->getMessage()]);
    }
}

function GetDadosData($pdo, $data)
{
    $sql = 'SELECT * FROM '. $data['tabela'] .' WHERE TRUE';

    if (isset($_GET['min_date']) || !empty($_GET['min_date'])) {
        $sql .= ' AND DATE(time) >=  ' . $data['min_date'];
    }
    if (isset($_GET['max_date']) || !empty($_GET['max_date'])) {
        $sql .= ' AND DATE(time) <=  ' . $data['max_date'];
    }

    $sql .= 'ORDER BY time DESC';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        echo json_encode($resultados);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao executar a consulta: ' . $e->getMessage()]);
    }
}

$rota = $_GET['rota'] ?? '';

if ($rota === 'GetAllFrom_k72623_lo')
    return GetAllFrom_k72623_lo($pdo);
if ($rota === 'GetAllFrom_nit2xli')
    return GetAllFrom_nit2xli($pdo);
if ($rota === 'GetDadosDevice') {
    if (!isset($_GET['dispositivo']) || empty($_GET['dispositivo'])) {
        echo die(json_encode(['error' => 'É necessário informar o disposivo']));
    }
    if (!isset($_GET['tabela']) || empty($_GET['tabela'])) {
        echo die(json_encode(['error' => 'É necessário informar a tabela']));
    }
    $data = array(
        'dispositivo' => "'".$_GET['dispositivo']."'",
        'tabela' => $_GET['tabela'],
    );
    return GetDadosDevice($pdo, $data);
}
if ($rota === 'GetDadosData') {
    if (!isset($_GET['tabela']) || empty($_GET['tabela'])) {
        echo die(json_encode(['error' => 'É necessário informar a tabela']));
    }
    $data = array(
        'tabela' => $_GET['tabela'],
    );
    if ((!isset($_GET['min_date']) || empty($_GET['min_date'])) && (!isset($_GET['max_date']) || empty($_GET['max_date']))) {
        echo die(json_encode(['error' => 'É necessário informar alguma data']));
    }
    if (isset($_GET['min_date']) || !empty($_GET['min_date'])) {
        $data['min_date'] = "'".$_GET['min_date']."'";
    }
    if (isset($_GET['max_date']) || !empty($_GET['max_date'])) {
        $data['max_date'] = "'".$_GET['max_date']."'";
    }
    return GetDadosData($pdo, $data);
}
echo json_encode(['error' => 'Rota não encontrada']);
