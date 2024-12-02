<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/db.php';
include $_SERVER['DOCUMENT_ROOT'].'/Svalberg-Motell/www/assets/inc/functions.php'; // Assuming sanitize function is here

// Ensure the user has the necessary permissions
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Manager')) {
    echo "Access denied.";
    exit;
}

// Default values for the filters
$startDate = $_POST['start_date'] ?? '2024-01-01'; // default start date
$endDate = $_POST['end_date'] ?? date('Y-m-d'); // default end date

// Sanitize the dates
$startDate = sanitize($startDate);
$endDate = sanitize($endDate);

// Validate date input using PHP (not relying on HTML5 validation)
if (!strtotime($startDate)) {
    $startDate = '2024-01-01'; // Reset to default if invalid
}

if (!strtotime($endDate)) {
    $endDate = date('Y-m-d'); // Reset to current date if invalid
}

// Query to fetch financial data
$sql = "SELECT
            p.payment_method,
            SUM(p.amount) AS total_amount,
            COUNT(p.payment_id) AS payment_count,
            p.payment_date
        FROM
            swx_payment p
        WHERE
            p.payment_date BETWEEN ? AND ?
        GROUP BY
            p.payment_method
        ORDER BY
            p.payment_method ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$startDate, $endDate]);

// Fetch the data
$financialData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .container {
            max-width: 90%;
            margin: 0 auto;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
        .btn-back, .btn-logout {
            position: absolute;
            top: 10px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back {
            left: 10px;
            background-color: #6c757d;
        }
        .btn-logout {
            right: 10px;
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="adminIndex.php" class="btn btn-back">Back</a>

    <!-- Logout Button -->
    <a href="../../logout.php" class="btn btn-logout">Logout</a>

    <div class="container">
        <h1>Financial Report</h1>

        <!-- Date filter form -->
        <div class="form-container">
            <form method="POST" action="financial_reports.php">
                <div class="row">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Financial report table -->
        <div class="table-container">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                        <th>Number of Payments</th>
                        <th>Report Date Range</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($financialData) > 0): ?>
                        <?php foreach ($financialData as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                <td><?= number_format($row['total_amount'], 2) ?> NOK</td>
                                <td><?= $row['payment_count'] ?></td>
                                <td><?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No data found for this date range.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
