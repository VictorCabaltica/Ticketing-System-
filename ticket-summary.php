<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #eef2f7;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    .back-button {
      background-color: #3498db;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 14px;
      cursor: pointer;
      margin-bottom: 20px;
      border-radius: 6px;
      text-decoration: none;
      display: inline-block;
      transition: background 0.3s;
    }
    .back-button:hover {
      background-color: #2980b9;
    }
    .charts-container {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: 30px;
    }
    .chart-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px;
      flex: 1 1 400px;
    }
    .chart-card h3 {
      text-align: center;
      margin-bottom: 15px;
      font-size: 20px;
      color: #2c3e50;
    }
    .summary-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      background: linear-gradient(#24243e, #3a3a5c, #acb6e5);
      color: #fff;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 6px 14px rgba(0,0,0,0.2);
    }
    .card h3 {
      margin: 0 0 10px;
      font-size: 18px;
    }
    .card p {
      font-size: 30px;
      font-weight: bold;
      margin: 0;
    }
    .table-controls {
      margin: 20px 0;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      display: none;
    }
    .table-controls input,
    .table-controls select {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .table-container {
      overflow-x: auto;
      margin-top: 20px;
      display: none;
    }
    .ticket-table {
      width: 100%;
      border-collapse: collapse;
    }
    .ticket-table th,
    .ticket-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .ticket-table th {
      background-color: #2c3e50;
      color: #fff;
      position: sticky;
      top: 0;
    }
    @media (max-width: 768px) {
      .charts-container,
      .summary-cards {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="main-content">
    <a href="admin.php" class="back-button">&larr; Back to Dashboard</a>

    <div class="charts-container">
      <div class="chart-card">
        <h3>Ticket Status Distribution</h3>
        <canvas id="statusChart"></canvas>
      </div>
      <div class="chart-card">
        <h3>Tickets per Agent</h3>
        <canvas id="agentChart"></canvas>
      </div>
    </div>

    <div class="summary-cards">
      <?php
        include('connection.php');
        // Summary counts
        $stats = [
          'Total Tickets' => "SELECT COUNT(*) FROM tickets",
          'Open Tickets'  => "SELECT COUNT(*) FROM tickets WHERE status='open'",
          'In Progress'   => "SELECT COUNT(*) FROM tickets WHERE status='in_progress'",
          'Closed Tickets'=> "SELECT COUNT(*) FROM tickets WHERE status='closed'",
          'Late Response' => "SELECT COUNT(*) FROM tickets WHERE response_due < NOW() AND status!='closed'",
          'Late Resolution'=>"SELECT COUNT(*) FROM tickets WHERE resolution_due < NOW() AND status!='closed'"
        ];
        foreach ($stats as $label => $sql) {
          $res = mysqli_query($conn, $sql);
          $cnt = mysqli_fetch_array($res)[0];
          echo "<div class='card' onclick=\"showTable('{$label}')\">
                  <h3>{$label}</h3>
                  <p>{$cnt}</p>
                </div>";
        }
      ?>
    </div>

    <div class="table-controls" id="tableControls">
      <input type="text" id="searchInput" placeholder="Search tickets...">
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
      </select>
    </div>

    <div class="table-container" id="tableContainer"></div>
  </div>

  <script>
    // Embed all tickets from database
    const tickets = <?php
      $all = mysqli_query($conn, "SELECT ticket_id, subject, status, priority, created_at FROM tickets");
      $arr = [];
      while ($r = mysqli_fetch_assoc($all)) $arr[] = $r;
      echo json_encode($arr);
    ?>;

    // Charts
    document.addEventListener('DOMContentLoaded', ()=>{
      const statusCounts = tickets.reduce((acc,t)=>{
        acc[t.status] = (acc[t.status]||0) + 1;
        return acc;
      },{});
      const ctx1 = document.getElementById('statusChart').getContext('2d');
      new Chart(ctx1, { type:'pie', data:{
          labels:['Open','In Progress','Closed'],
          datasets:[{ data:[statusCounts.open||0, statusCounts.in_progress||0, statusCounts.closed||0],
                     backgroundColor:['#e67e22','#2980b9','#27ae60'] }]
        }, options:{responsive:true}
      });

      const agentData = <?php
        $agents = mysqli_query($conn, "SELECT ag.name, COUNT(t.ticket_id) cnt FROM agent ag LEFT JOIN assignment a ON ag.agent_id=a.agent_id LEFT JOIN tickets t ON a.ticket_id=t.ticket_id GROUP BY ag.agent_id");
        $names=[]; $counts=[];
        while($a=mysqli_fetch_assoc($agents)){ $names[]=$a['name']; $counts[]=$a['cnt']; }
        echo json_encode(['names'=>$names,'counts'=>$counts]);
      ?>;
      const ctx2 = document.getElementById('agentChart').getContext('2d');
      new Chart(ctx2,{ type:'bar', data:{ labels:agentData.names, datasets:[{label:'Tickets', data:agentData.counts, backgroundColor:'#2c3e50'}] }, options:{responsive:true, scales:{y:{beginAtZero:true}}} });
    });

    function showTable(label) {
      const tableControls = document.getElementById('tableControls');
      const tableContainer = document.getElementById('tableContainer');
      tableControls.style.display = 'flex';
      tableContainer.style.display = 'block';

      // Determine filter based on label
      const map = {
        'Total Tickets': () => tickets,
        'Open Tickets': () => tickets.filter(t=>t.status==='open'),
        'In Progress': () => tickets.filter(t=>t.status==='in_progress'),
        'Closed Tickets': () => tickets.filter(t=>t.status==='closed'),
        'Late Response': () => tickets.filter(t=> new Date(t.created_at) < new Date(t.response_due) && t.status!=='closed'),
        'Late Resolution': () => tickets.filter(t=> new Date(t.created_at) < new Date(t.resolution_due) && t.status!=='closed')
      };
      const data = (map[label]||map['Total Tickets'])();

      // Build table
      let html = `<table class="ticket-table"><thead><tr>
        <th>ID</th><th>Subject</th><th>Status</th><th>Priority</th><th>Created At</th>
      </tr></thead><tbody id="tableBody">`;
      data.forEach(t=>{
        html += `<tr><td>#${t.ticket_id}</td><td>${t.subject}</td><td>${t.status.replace('_',' ')}</td><td>${t.priority}</td><td>${t.created_at}</td></tr>`;
      });
      html += '</tbody></table>';
      tableContainer.innerHTML = html;

      document.getElementById('searchInput').oninput = filterTable;
      document.getElementById('statusFilter').onchange = filterTable;
    }

    function filterTable() {
      const search = document.getElementById('searchInput').value.toLowerCase();
      const status = document.getElementById('statusFilter').value;
      document.querySelectorAll('#tableBody tr').forEach(row=>{
        const subj = row.cells[1].innerText.toLowerCase();
        const stat = row.cells[2].innerText.replace(' ','_');
        row.style.display = subj.includes(search) && (!status||stat===status) ? '' : 'none';
      });
    }
  </script>
</body>
</html>