<?php include 'components/header.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Business Reports</h2>
            <p class="text-muted">Analyze your resort's financial performance and occupancy.</p>
        </div>
        <button class="btn btn-outline-primary rounded-pill px-4" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Generate PDF Report
        </button>
    </div>

    <!-- Revenue & Volume Summary -->
    <div class="row g-4 mb-5">
        <?php
        // Kunin ang data mula sa iyong SQL View: vw_monthly_revenue
        $current_month = date('n');
        $current_year = date('Y');
        $sql_rev = "SELECT * FROM vw_monthly_revenue WHERE year = '$current_year' AND month = '$current_month'";
        $res_rev = $conn->query($sql_rev);
        $rev_data = $res_rev->fetch_assoc();

        // Fallback kung wala pang data para sa kasalukuyang buwan
        $total_rev = $rev_data['gross_revenue'] ?? 0;
        $total_book = $rev_data['total_bookings'] ?? 0;
        ?>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="opacity-75">Monthly Gross Revenue (<?php echo date('F'); ?>)</h6>
                        <h1 class="fw-bold display-5 mb-0">₱<?php echo number_format($total_rev, 2); ?></h1>
                    </div>
                    <div class="fs-1 opacity-25"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="opacity-75">Total Bookings This Month</h6>
                        <h1 class="fw-bold display-5 mb-0"><?php echo $total_book; ?></h1>
                    </div>
                    <div class="fs-1 opacity-25"><i class="bi bi-journal-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Monthly Performance Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">Revenue History (Year <?php echo $current_year; ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">Month</th>
                                    <th>Bookings</th>
                                    <th>Discounts</th>
                                    <th>Tax Collected</th>
                                    <th class="pe-4">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_history = "SELECT * FROM vw_monthly_revenue WHERE year = '$current_year' ORDER BY month DESC";
                                $res_history = $conn->query($sql_history);

                                if ($res_history->num_rows > 0) {
                                    while($row = $res_history->fetch_assoc()) {
                                        $monthName = date("F", mktime(0, 0, 0, $row['month'], 10));
                                        echo "<tr>
                                            <td class='ps-4 fw-bold'>$monthName</td>
                                            <td>".$row['total_bookings']."</td>
                                            <td class='text-danger'>-₱".number_format($row['total_discounts'], 2)."</td>
                                            <td>₱".number_format($row['total_tax'], 2)."</td>
                                            <td class='pe-4 fw-bold text-success'>₱".number_format($row['gross_revenue'], 2)."</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No data recorded for this year.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Performance Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <h6 class="text-muted text-uppercase small mb-3">Today's Occupancy Rate</h6>
                    <?php
                        $total_rooms = $conn->query("SELECT COUNT(*) as total FROM rooms")->fetch_assoc()['total'];
                        $occupied_rooms = $conn->query("SELECT COUNT(*) as total FROM room_current_status WHERE status = 'occupied'")->fetch_assoc()['total'];
                        $rate = ($total_rooms > 0) ? ($occupied_rooms / $total_rooms) * 100 : 0;
                    ?>
                    <h2 class="fw-bold mb-1"><?php echo number_format($rate, 1); ?>%</h2>
                    <div class="progress rounded-pill mb-3" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: <?php echo $rate; ?>%"></div>
                    </div>
                    <p class="small text-muted"><?php echo $occupied_rooms; ?> out of <?php echo $total_rooms; ?> rooms are currently booked.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Popular Room Types</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        <?php
                        $sql_popular = "SELECT rt.type_name, COUNT(b.booking_id) as count 
                                        FROM bookings b 
                                        JOIN rooms r ON b.room_id = r.room_id 
                                        JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                        GROUP BY rt.type_name ORDER BY count DESC LIMIT 5";
                        $res_popular = $conn->query($sql_popular);
                        while($pop = $res_popular->fetch_assoc()) {
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center px-4 py-3'>
                                    ".$pop['type_name']."
                                    <span class='badge bg-soft-sky text-primary rounded-pill'>".$pop['count']." bookings</span>
                                  </li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>