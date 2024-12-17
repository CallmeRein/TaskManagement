<?php
define ('FPDF_FONTPATH', 'font/');

// Include the FPDF library
require __DIR__ . '/fpdf.php';

// Include the database connection
require __DIR__ . '/database/database_connection.php';

// Fetch all tasks from the database (same query as the one used in your HTML)
$tasks_result = mysqli_query($conn, "SELECT tasks.*, clients.client_name, agents.agent_name AS agent_name FROM tasks JOIN clients ON tasks.client_id = clients.client_id JOIN agents ON tasks.agent_id = agents.agent_id");

// If there is a date search filter applied
if (isset($_POST['date_search'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $date_filter_sql = "SELECT tasks.*, clients.client_name, agents.name AS agent_name 
                        FROM tasks 
                        JOIN clients ON tasks.client_id = clients.client_id 
                        JOIN agents ON tasks.agent_id = agents.agent_id 
                        WHERE tasks.start_date >= '$start_date' 
                        AND tasks.end_date <= '$end_date'";
    $tasks_result = mysqli_query($conn, $date_filter_sql);
}

// If there is a status search filter applied
if (isset($_POST['status_search'])) {
    $status = $_POST['status'];
    $status_filter_sql = "SELECT tasks.*, clients.client_name, agents.name AS agent_name FROM tasks JOIN clients ON tasks.client_id = clients.client_id JOIN agents ON tasks.agent_id = agents.agent_id WHERE tasks.status = '$status'";
    $tasks_result = mysqli_query($conn, $status_filter_sql);
}

// Create instance of FPDF class with landscape orientation
$pdf = new FPDF('L', 'mm', 'A4'); // 'L' for landscape, 'mm' for millimeters, 'A4' for page size
$pdf->AddPage();

// Set title and font
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(270, 10, 'Task Report', 0, 1, 'C');  // Adjusted width to fit in landscape

// Add some space
$pdf->Ln(10);

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'Ticket Number', 1, 0, 'C');
$pdf->Cell(30, 10, 'Client Name', 1, 0, 'C');
$pdf->Cell(45, 10, 'Concern', 1, 0, 'C');
$pdf->Cell(30, 10, 'Severity', 1, 0, 'C');
$pdf->Cell(30, 10, 'Start Date', 1, 0, 'C');
$pdf->Cell(30, 10, 'End Date', 1, 0, 'C');
$pdf->Cell(45, 10, 'Assigned Agent', 1, 0, 'C');
$pdf->Cell(30, 10, 'Status', 1, 1, 'C');

// Table data
$pdf->SetFont('Arial', '', 10);
while ($row = mysqli_fetch_assoc($tasks_result)) {
    $pdf->Cell(30, 10, $row['ticket_number'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['client_name'], 1, 0, 'C');
    $pdf->Cell(45, 10, $row['client_concern'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['severity'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['start_date'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['end_date'], 1, 0, 'C');
    $pdf->Cell(45, 10, $row['agent_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['status'], 1, 1, 'C');
}

// Output the PDF
$pdf->Output();
?>
