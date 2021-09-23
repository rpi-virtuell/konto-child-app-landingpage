<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="wp-user-form">
	<div class="username">
		<label for="user_login" class="hide"><?php _e('Username or Email'); ?>: </label>
		<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1001" />
	</div>
	<div class="login_fields">
		<?php //do_action('login_form', 'resetpass'); ?>
		<input type="submit" name="user-submit" value="<?php _e('Reset password'); ?>" class="user-submit" tabindex="1002" />

		<?php
		if (isset($_POST['reset_pass']))
		{
			global $wpdb;
			$username = trim($_POST['user_login']);
			$user_exists = false;
			if (username_exists($username))
			{
				$user_exists = true;
				$user_data = get_userdatabylogin($username);
			} elseif (email_exists($username))
			{

				$user_exists = true;
				$user = get_user_by_email($username);
			} else
			{
				$error[] = '<p>' . __('Username or Email was not found, try again!') . '</p>';
			}
			if ($user_exists)
			{
				$user_login = $user->user_login;
				$user_email = $user->user_email;
				// Generate something random for a password... md5'ing current time with a rand salt
				$key = substr(md5(uniqid(microtime())), 0, 8);
				// Now insert the new pass md5'd into the db
				$wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$key' WHERE user_login = '$user_login'");
				//create email message
				$message = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
				$message .= get_option('siteurl') . "\r\n\r\n";
				$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
				$message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
				$message .= get_option('siteurl') . "/wp-login.php?action=rp&key=$key\r\n";
				//send email meassage
				if (FALSE == wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_option('blogname')), $message))
					$error[] = '<p>' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>';
			}
			if (count($error) > 0)
			{
				foreach ($error as $e)
				{
					echo $e . '<br/>';
				}
			} else
			{
				echo '<p>' . __('A message will be sent to your email address.') . '</p>';
			}
		}
		?>
		<input type="hidden" name="reset_pass" value="1" />
		<input type="hidden" name="user-cookie" value="1" />
	</div>
</form>
