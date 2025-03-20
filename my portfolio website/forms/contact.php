<?php
  $receiving_email_address = 'agbogunadeyinka@gmail.com';

  if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
      include($php_email_form);
  } else {
      die('Unable to load the "PHP Email Form" Library!');
  }

  // ✅ Sanitize input to prevent XSS attacks
  function clean_input($data) {
      return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
  }

  $name = clean_input($_POST['name']);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $subject = clean_input($_POST['subject']);
  $message = clean_input($_POST['message']);

  // ✅ Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      die('Invalid email format!');
  }

  // ✅ Validate required fields
  if (empty($name) || empty($email) || empty($subject) || empty($message)) {
      die('All fields are required!');
  }

  // ✅ Prevent email header injection
  if (preg_match('/[\r\n]/', $name) || preg_match('/[\r\n]/', $email) || preg_match('/[\r\n]/', $subject)) {
      die('Invalid input detected!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = $name;
  $contact->from_email = $email;
  $contact->subject = $subject;

  // ✅ SMTP Setup (If Needed)
  /*
  $contact->smtp = array(
      'host' => 'smtp.yourserver.com',
      'username' => 'your_username',
      'password' => 'your_password',
      'port' => '587'
  );
  */

  $contact->add_message($name, 'From');
  $contact->add_message($email, 'Email');
  $contact->add_message($message, 'Message', 10);

  // ✅ Error handling for email sending
  if ($contact->send()) {
      echo 'Message sent successfully!';
  } else {
      echo 'Failed to send message. Please try again.';
  }
?>
