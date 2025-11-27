<?php
$id = $_GET['id'];

header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=ticket_cita_$id.txt");

echo "Ticket de cita #$id\n";
echo "Gracias por confiar en Boutique Elegante\n";
echo "Fecha de descarga: " . date("Y-m-d H:i:s");
