<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>CF demo</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }
	::-webkit-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<h1>CF user settings and messaging</h1>

	<div id="body">
		<p>Use the forms below to update user settings and to send a test message. The user settings will be checked agaist and notification results returned.</p>

		<?php if (!empty($response)): ?>
			<p>
			<h2>Response</h2>
			<strong><?= $response; ?></strong>

			<h3>Log output:</h3>
			<code><?= $log ?></code>
			</p>
		<?php endif; ?>

		<p>Recipient UID defaulted to:</p>
		<code>bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d</code>

		<p>Sender UID defaulted to:</p>
		<code>8d1302e8aa54bd59e2f4f398c66ff94f2650d4d34679ee02cf5fc61b9cabcee5</code>

		<p>Below are forms for full CRUD on settings, followed by sending a test message. The results of the notification will be returned.</p>

		<h3>CRUD for account settings</h3>
		<h4>Create settings</h4>
		<p>These are not pulled from the database and will all default to true. Settings pulled from database are in update section below</p>
		<code>POST /account/settings</code>

		<form action="/http/create" method="POST">
			<label>For user:</label><input name="uid" value="bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d" readonly>
			<br />
			<select name="notify_send">
				<option value="0">Send notifications INACTIVE</option>
				<option value="1" selected='selected'>Send notifications ACTIVE</option>
			</select>
			<br />
			<select name="notify_receive">
				<option value="0">Receive notifications INACTIVE</option>
				<option value="1" selected="selected">Receive notifications ACTIVE</option>
			</select>
			<br />
			<select name="notify_deliver">
				<option value="0">Deliver notifications INACTIVE</option>
				<option value="1" selected="selected">Deliver notifications ACTIVE</option>
			</select>
			<br />

			<input type="submit" value="Save settings" />
		</form>
		<h4>Update settings</h4>
		<code>PUT /account/settings</code>

		<form action="/http/update" method="POST">
			<label>For user:</label><input name="uid" value="bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d" readonly>
			<br />
			<select name="notify_send">
				<option value="0" <?= (isset($notify_send) && ($notify_send == 0)) ? "selected='selected'" : '' ?>>Send notifications INACTIVE</option>
				<option value="1" <?= (isset($notify_send) && ($notify_send == 1)) ? "selected='selected'" : '' ?>>Send notifications ACTIVE</option>
			</select>
			<br />
			<select name="notify_receive">
				<option value="0" <?= (isset($notify_receive) && ($notify_receive == 0)) ? "selected='selected'" : '' ?>>Receive notifications INACTIVE</option>
				<option value="1" <?= (isset($notify_receive) && ($notify_receive == 1)) ? "selected='selected'" : '' ?>>Receive notifications ACTIVE</option>
			</select>
			<br />
			<select name="notify_deliver">
				<option value="0" <?= (isset($notify_deliver) && ($notify_deliver == 0)) ? "selected='selected'" : '' ?>>Deliver notifications INACTIVE</option>
				<option value="1" <?= (isset($notify_deliver) && ($notify_deliver == 1)) ? "selected='selected'" : '' ?>>Deliver notifications ACTIVE</option>
			</select>
			<br />

			<input type="submit" value="Update settings" />
		</form>
		<hr />

		<h4>Delete settings</h4>
		<code>DELETE /account/settings</code>

		<form action="/http/delete" method="POST">
			<label>For user:</label><input name="uid" value="bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d" readonly>
			<br />
			<input type="submit" value="Delete settings" />
		</form>
		<hr />

		<h3>Message sending</h3>
		<code>POST /message </code>

		<form action="/http/message" method="POST">
			<label>To user:</label><input name="to" value="bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d" readonly>
			<br />
			<label>From user:</label><input name="from" value="8d1302e8aa54bd59e2f4f398c66ff94f2650d4d34679ee02cf5fc61b9cabcee5" readonly>
			<br />
			<textarea name="message" placeholder="Type message here">
			</textarea>
			<input type="submit" value="Send message" />
		</form>
		<hr />
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>
