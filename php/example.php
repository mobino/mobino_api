<html>
<head>
<title>Example Mobino-Integration with PHP</title>
</head>
<body>
<?php

	/**
	 * Include  Mobino class
	 */
	require("Mobino/Mobino.php");
	
	/**
	 * Initialize Mobino, set the following parameters:
	 * @param int Amount in Euro
	 * @param int Your own reference number of the transaction
	 * @param string Optional, describe payment type: 'regular' or 'gift'. Default is 'regular'.
	 * @param string Optional, describe environmet: 'production' or 'staging'. Default is 'production'.
	 */
	$Mobino = new Mobino(10.00, 9999, 'regular', 'staging');
	
	/**
	 * Use getWidget() to get the code to embed the payment widget within your website
	 */
	print $Mobino->getWidget();
?>
</body>
</html>