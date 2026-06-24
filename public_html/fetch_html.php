<?php
$html = file_get_contents("http://127.0.0.1:8000/registration-success.php?token=c4ca4238a0b923820dcc509a6f75849b");
file_put_contents("raw_output.txt", $html);
print "Fetched success page HTML successfully!\n";
?>
