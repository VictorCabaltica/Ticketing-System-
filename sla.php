<?php
// Database connection
include 'connection.php';

// Query to get all closed tickets
$query = "SELECT * FROM tickets WHERE status = 'closed'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ticket_id = $row['ticket_id'];
        $priority = $row['priority'];
        $created_at = strtotime($row['created_at']);
        
        if (isset($row['closed_at'])) {
            $closed_at = strtotime($row['closed_at']);
        } else {
            continue;
        }
        
        $sla_target = 0;

        if ($priority == 'critical') {
            $sla_target = 1;
        } elseif ($priority == 'high') {
            $sla_target = 2;
        } elseif ($priority == 'medium') {
            $sla_target = 3;
        } elseif ($priority == 'low') {
            $sla_target = 4;
        }

        $time_difference = ($closed_at - $created_at) / 60;

        $sla_status = ($time_difference <= $sla_target) ? 'Met' : 'Not Met';

        // Update silently without echoing
        mysqli_query($conn, "UPDATE tickets SET sla_status = '$sla_status' WHERE ticket_id = '$ticket_id'");
    }
}

mysqli_close($conn);

// Reconnect to fetch metrics
$conn = mysqli_connect("localhost", "root", "", "ticketing_db");

$total_tickets = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_tickets FROM tickets"))['total_tickets'];
$sla_met_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS sla_met_count FROM tickets WHERE sla_status = 'Met'"))['sla_met_count'];
$avg_resolution_time = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, closed_at)) AS avg_resolution_time FROM tickets WHERE status = 'closed'"))['avg_resolution_time'];

$sla_met_percentage = ($total_tickets > 0) ? ($sla_met_count / $total_tickets) * 100 : 0;

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SLA Performance Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:#0f0c29 ;
            margin: 0;
            padding: 20px;
        }
        .sla-performance {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: auto;
        }
        .sla-performance h2 {
            text-align: center;
            color: #333;
        }
        .sla-metrics {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .metric-card {
            background: linear-gradient(#24243e, #3a3a5c, #acb6e5);
            padding: 20px;
            border-radius: 12px;
            flex: 1 1 250px;
            text-align: center;
            color: white;
            transition: transform 0.3s;
        }
        .metric-card:hover {
            transform: translateY(-8px);
        }
        .metric-card h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .metric-card p {
            font-size: 2rem;
            font-weight: bold;
        }
        .chart-container {
            margin-top: 50px;
            position: relative;
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<div class="sla-performance">
    <h2>SLA Performance</h2>

    <div class="sla-metrics">
        <div class="metric-card">
            <h3>Total Tickets</h3>
            <p><?php echo $total_tickets; ?></p>
        </div>
        <div class="metric-card">
            <h3>SLA Met</h3>
            <p><?php echo $sla_met_count; ?> (<?php echo round($sla_met_percentage, 2); ?>%)</p>
        </div>
    </div>

    <div class="chart-container">
        <canvas id="slaChart"></canvas>
    </div>

    <!-- Back button -->
    <button class="back-btn" onclick="window.history.back();">Back</button>
</div>

<script>
    const ctx = document.getElementById('slaChart').getContext('2d');
    const slaChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['SLA Met', 'SLA Not Met'],
            datasets: [{
                label: 'SLA Performance',
                data: [<?php echo $sla_met_count; ?>, <?php echo $total_tickets - $sla_met_count; ?>],
                backgroundColor: ['orange', 'darkviolet'],
                hoverOffset: 10,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#ccc',
                    borderWidth: 1
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#555',
                        font: {
                            size: 14
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'SLA Met vs Not Met',
                    color: '#333',
                    font: {
                        size: 18
                    }
                }
            }
        }
    });
</script>

</body>
</html>
