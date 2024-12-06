<?php
require_once 'header.php';

$sql = "SELECT p.*, u.email 
        FROM payments p
        JOIN kullanicilar u ON p.kullanici_id = u.id
        WHERE p.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
        AND (p.last_reminder_sent IS NULL OR p.last_reminder_sent < CURDATE())";

$result = $db->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $to = $row["email"];
        $subject = "Ödeme Hatırlatması";
        $message = "Sayın " . $row["isim"] . ",\n\n" .
                   "Ödemenizin son tarihi " . $row["due_date"] . " olduğunu hatırlatmak isteriz.\n\n" .
                   "Teşekkürler,\nEkibiniz";
        
        mail($to, $subject, $message);
    
        $update_sql = "UPDATE payments SET last_reminder_sent = CURDATE() WHERE id = " . $row["id"];
        $db->query($update_sql);
    }
}
?>