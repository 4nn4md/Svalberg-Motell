<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/db.php");
$email = $_SESSION['email'];

// Hent brukerinfo
$sql = $pdo->prepare(
  "SELECT 
    user_id, 
    firstName, 
    lastName, 
    tlf, 
    point 
  FROM swx_users 
  WHERE username = :email");

$sql->execute([':email' => $email]);
$user = $sql->fetch(PDO::FETCH_ASSOC);

$firstName = $user['firstName'];
$lastName = $user['lastName'];
$phone = $user['tlf'];
$point = $user['point'];
$user_id = $user['user_id'];

// Hent kommende reservasjoner (der `check_in_date` er i fremtiden)
$upcomingQuery = $pdo->prepare(
  "SELECT 
    b.booking_id, 
    b.check_in_date, 
    b.check_out_date, 
    p.amount AS total_price, 
    b.name, 
    b.number_of_guests, 
    b.comments
  FROM swx_booking AS b
  INNER JOIN swx_payment AS p 
  ON b.payment_id = p.payment_id
  WHERE b.user_id = :user_id 
  AND b.check_in_date >= CURDATE()
  ORDER BY b.check_in_date ASC");

$upcomingQuery->execute([':user_id' => $user_id]);
$upcomingReservations = $upcomingQuery->fetchAll(PDO::FETCH_ASSOC);

// Hent alle reservasjoner (historikk)
$historyQuery = $pdo->prepare(
  "SELECT 
    b.booking_id, 
    b.check_in_date, 
    b.check_out_date, 
    b.name,
    b.email,
    b.tlf,
    p.amount AS total_price 
  FROM 
    swx_booking AS b
  INNER JOIN 
    swx_payment AS p 
  ON 
    b.payment_id = p.payment_id
  WHERE 
    b.user_id = :user_id 
    AND b.check_out_date < CURDATE()
  ORDER BY 
    b.check_in_date DESC");

    $historyQuery->execute([':user_id' => $user_id]);
    $bookingHistory = $historyQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<body>
<section style="margin-bottom: 0;">
    <div class="container py-3">
        <div class="row d-flex justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="rounded-top text-white d-flex flex-row" style="background-color: #0A3D62; height:200px;">
                        <div class="ms-4 mt-5 d-flex flex-column" style="width: 150px;">
                            <img src="http://localhost/Svalberg-Motell/www/assets/image/profile-pic.webp"
                                 alt="Generic placeholder image" class="img-fluid img-thumbnail mt-4 mb-2"
                                 style="width: 150px; z-index: 1">
                            <button type="button" class="btn btn-outline-dark text-body edit-profile" style="z-index: 1;">
                                Edit profile
                            </button>
                        </div>
                        <div class="ms-3" style="margin-top: 130px;">
                            <h5 style="color: #B3D8F2;"><?php echo "$firstName $lastName"; ?></h5>
                        </div>
                    </div>
                    <div class="p-2 text-black bg-body-tertiary mt-0">
                        <div class="d-flex justify-content-end text-center py-1 text-body">
                            <div>
                                <p class="mb-1 h5"><?php echo $point; ?></p>
                                <p class="small text-muted mb-0">Point</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 text-black mb-0">
                        <div class="mb-5 text-body">
                            <p class="lead fw-normal mb-1">Contact information</p>
                            <div class="p-0 bg-body-tertiary">
                                <p class="font-italic mb-1"><?php echo "Telephone number: $phone"; ?></p>
                                <p class="font-italic mb-0"><?php echo "Email: $email"; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container" style="margin-bottom: 200px;">
    <div class="row">
        <div class="col-12">
            <div class="nav col-4 nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" id="reservasjon-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab"
                   aria-controls="v-pills-home" aria-selected="true">My Reservations</a>
                <a class="nav-link" id="historikk-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab"
                   aria-controls="v-pills-profile" aria-selected="false">History</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="tab-content" id="v-pills-tabContent">
                <!-- Kommende reservasjoner -->
                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="reservasjon-tab">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Arrival</th>
                            <th scope="col">Departure</th>
                            <th scope="col">Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($upcomingReservations)) : ?>
                              <?php foreach ($upcomingReservations as $reservation) : ?>
                                  <tr>
                                      <td><?php echo htmlspecialchars($reservation['check_in_date']); ?></td>
                                      <td><?php echo htmlspecialchars($reservation['check_out_date']); ?></td>
                                      <td><?php echo htmlspecialchars($reservation['total_price']); ?> NOK</td>
                                      <td>
                                          <button class="btn btn-primary toggle-details" data-target=".details-row<?php echo $reservation['booking_id']; ?>">Vis romdetaljer</button>
                                      </td>
                                  </tr>
                                  <tr class="collapse details-row<?php echo $reservation['booking_id']; ?>">
                                      <td colspan="4">
                                          <div class="card card-body">
                                              <p><strong>Navn:</strong> <?php echo htmlspecialchars($reservation['name']); ?></p>
                                              <p><strong>Antall personer:</strong> <?php echo htmlspecialchars($reservation['number_of_guests']); ?></p>
                                              <p><strong>Kommentarer:</strong> <?php echo htmlspecialchars($reservation['comments']); ?></p>
                                          </div>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                          <?php else : ?>
                              <tr>
                                  <td colspan="6">No upcoming reservations found.</td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                    </table>
                </div>
                <!-- Historikk -->
                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="historikk-tab">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone number</th>
                            <th scope="col">Arrival</th>
                            <th scope="col">Departure</th>
                            <th scope="col">Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($bookingHistory)): ?>
                            <?php foreach ($bookingHistory as $history): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($history['name']); ?></td> 
                                    <td><?php echo htmlspecialchars($history['email']); ?></td> 
                                    <td><?php echo htmlspecialchars($history['tlf']); ?></td> 
                                    <td><?php echo htmlspecialchars($history['check_in_date']); ?></td>
                                    <td><?php echo htmlspecialchars($history['check_out_date']); ?></td>
                                    <td><?php echo htmlspecialchars($history['total_price']); ?> NOK</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No previous reservations found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-details');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetSelector = button.getAttribute('data-target');
                const target = document.querySelector(targetSelector);

                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    button.textContent = 'Show more details';
                } else {
                    target.classList.add('show');
                    button.textContent = 'Hide details';
                }
            });
        });
    });
</script>
</body>
</html>