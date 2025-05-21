<?php
include 'connection.php';
include 'agent-navbar.php';

// Fetch SLA performance per agent
$query = "
SELECT 
    a.name AS agent_name,
    COUNT(t.ticket_id) AS total_tickets,
    SUM(CASE WHEN t.sla_status = 'Met' THEN 1 ELSE 0 END) AS sla_met_count,
    ROUND(
        SUM(CASE WHEN t.sla_status = 'Met' THEN 1 ELSE 0 END) / COUNT(t.ticket_id) * 100,
        2
    ) AS sla_met_percentage
FROM assignment ass
JOIN agent a ON ass.agent_id = a.agent_id
JOIN tickets t ON ass.ticket_id = t.ticket_id
WHERE t.status = 'closed'
GROUP BY a.agent_id";

$result = mysqli_query($conn, $query);

$agents = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['sla_not_met_count'] = $row['total_tickets'] - $row['sla_met_count'];
    $agents[] = $row;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent SLA Performance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background: #f0f2f5;
        }
        .sla-performance {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 16px;
        }
        th {
            background: darkviolet;
            color: white;
            font-size: 15px;
        }
        tr:nth-child(even) {
            background: #fafafa;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="sla-performance">
    <h2 style="text-align: center;">Agent SLA Performance</h2>

    <table>
        <thead>
            <tr>
                <th>Agent Name</th>
                <th>Total Tickets</th>
                <th>SLA Met</th>
                <th>SLA Not Met</th>
                <th>SLA %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agents as $agent): ?>
            <tr>
                <td><?php echo htmlspecialchars($agent['agent_name']); ?></td>
                <td><?php echo $agent['total_tickets']; ?></td>
                <td><?php echo $agent['sla_met_count']; ?></td>
                <td><?php echo $agent['sla_not_met_count']; ?></td>
                <td><?php echo $agent['sla_met_percentage']; ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="chart-container">
    <h2 style="text-align: center;">SLA Met vs Not Met</h2>
    <canvas id="agentChart" style="max-height: 500px;"></canvas>
</div>

<script>
const agents = <?php echo json_encode(array_column($agents, 'agent_name')); ?>;
const slaMet = <?php echo json_encode(array_column($agents, 'sla_met_count')); ?>;
const slaNotMet = <?php echo json_encode(array_column($agents, 'sla_not_met_count')); ?>;

const ctx = document.getElementById('agentChart').getContext('2d');
const agentChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: agents.map(name => name + " (Met)").concat(agents.map(name => name + " (Not Met)")),
        datasets: [{
            label: 'SLA Performance',
            data: slaMet.concat(slaNotMet),
            backgroundColor: [
                ...agents.map(() => 'orange'), // Green for Met
                ...agents.map(() => 'darkviolet')  // Red for Not Met
            ],
            borderColor: 'white',
            borderWidth: 2,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.parsed;
                        return label + ': ' + value + ' tickets';
                    }
                }
            },
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    boxWidth: 20
                }
            }
        }
    }
});
</script>

</body>
</html>
