<?php
use App\NumberHelper;
use App\NombreTest;
use App\TableHelper;
use App\URLHelper;

define('PER_PAGE', 20);

require_once "vendor/autoload.php";
$pdo = new PDO("sqlite:./data.db", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$query = "SELECT * FROM products";
$queryCount = "SELECT COUNT(id) as count FROM products";
$params = [];
$sortable = ["id", "name", "city", "price", "address"];


//Recherche par ville
if(!empty($_GET['q'])) {
    $query .= " WHERE city LIKE :city";
    $queryCount .= " WHERE city LIKE :city";
    $params['city'] = '%' . $_GET['q'] . '%';
}

// Organisation
if (!empty($_GET['sort']) && in_array($_GET['sort'], $sortable)) {
    $direction = $_GET['dir'] ?? 'asc';
    if (!in_array($direction, ['asc', 'desc'])) {
        $direction = 'asc';
    }
    $query .= " ORDER BY " . $_GET['sort'] . " $direction";
}



// Pagination
$page = (int)($_GET['p'] ?? 1);
$offset = ($page-1) * PER_PAGE;

$query .= " LIMIT " . PER_PAGE . " OFFSET $offset";

$statement = $pdo->prepare($query);
$statement->execute($params);
$products = $statement->fetchAll();


$statement = $pdo->prepare($queryCount);
$statement->execute($params);
$count = (int)$statement->fetch()['count'];
$pages = ceil($count / PER_PAGE);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Biens immobiliers</title>
</head>
<body class="p-4">

    <h1>Les biens immobilers</h1>

<form action="" class="mb-4">
    <div class="form-group">
        <input type="text" class="form-control" name="q" placeholder="Rechercher par ville" value="<?= htmlentities($_GET['q'] ?? null)  ?>">
    </div>
    <button class="btn btn-primary">Rechercher</button>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th><?= TableHelper::sort('id', 'ID', $_GET) ?></th>
            <th><?= TableHelper::sort('name', 'Nom', $_GET) ?></th>
            <th><?= TableHelper::sort('price', 'Prix', $_GET) ?></th>
            <th><?= TableHelper::sort('city', 'Ville', $_GET) ?></th>
            <th><?= TableHelper::sort('address', 'Adresse', $_GET) ?></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= $product['name'] ?></td>
            <td><?= NumberHelper::price($product['price']) ?></td>
            <td><?= $product['city'] ?></td>
            <td><?= $product['address'] ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>

</table>


<?php if($pages > 1 && $page > 1): ?>
    <a href="?<?= URLHelper::withParam($_GET, "p", $page - 1)?>" class="btn btn-primary">Page précédente</a>
<?php endif ?>


<?php if($pages > 1 && $page < $pages): ?>
    <a href="?<?= URLHelper::withParam($_GET, "p", $page + 1) ?>" class="btn btn-primary">Page suivante</a>
<?php endif ?>












<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script> 
</body>
</html>