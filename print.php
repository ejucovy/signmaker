<?php
echo "<html><head><style type='text/css' media='print'>body { margin: 0; } img { margin: auto; } @page { margin: 0; size: landscape; }</style></head><body>";
echo "<img src='/image.php?" . $_SERVER["QUERY_STRING"] . "&resize=paper'>";
echo "<script type='text/javascript'>window.print();</script>";
echo "</body></html>";
?>
