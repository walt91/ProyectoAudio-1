<?php 

	# We need to include the library and use the necessary classes
	require_once __DIR__ . '/vendor/autoload.php';
	use PhpAmqpLib\Connection\AMQPConnection;
	use PhpAmqpLib\Message\AMQPMessage;

	# Then we can create a connection to the server:
	$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
	$channel = $connection->channel();	

	# To send, we must declare a queue for us to send to; then we can publish
	# a message to the queue:
	$channel->queue_declare('myQueue', false, false, false, false);

	$message = '';
	$continue = true;
	
	while($continue) {

		# -----------------------------------
		# Capture message from the keyboard
		$stdin = fopen("php://stdin", "r");
		print("Digite el mensaje a encolar:\n");
		fscanf(STDIN, "%[^\n]s", $message);

		# Ask whether to continue
		print("Desea encolar otro mensaje (s/n): ");
		fscanf(STDIN, "%s\n", $continue);
		$continue = strtoupper($continue) === 'S' ? true : false;
		fclose($stdin);
		# -----------------------------------

		# Queue the message
		$msg = new AMQPMessage($message);
		$channel->basic_publish($msg, '', 'myQueue');

		echo " [x] Sent '". $msg->body ."'\n\n";
	}

	# Lastly, we close the channel and the connection;
	$channel->close();
	$connection->close();

?>