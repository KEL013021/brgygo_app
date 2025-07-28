<?php
include('../database/connection.php');

$query = "SELECT 
            r.id,
            CONCAT(res.last_name, ', ', res.first_name, ' ', LEFT(res.middle_name, 1), '.') AS full_name,
            s.service_name,
            r.purpose,
            r.request_date,
            r.status
          FROM requests r
          JOIN residents res ON r.resident_id = res.id
          JOIN services s ON r.service_id = s.id
          ORDER BY r.request_date DESC";

$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['full_name']}</td>
            <td>{$row['service_name']}</td>
            <td>{$row['purpose']}</td>
            <td>{$row['request_date']}</td>
            <td>{$row['status']}</td>
            <td>
                <button class='btn btn-primary btn-sm' onclick='printRequest({$row['id']})'>Print</button>
                <button class='btn btn-danger btn-sm' onclick='declineRequest({$row['id']})'>Declined</button>
            </td>
          </tr>";
}
?>
