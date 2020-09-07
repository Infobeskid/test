<?php

class MailReminder {

	public function mailStatus($my_filename, $d_student_nr_indeks, $d_assigned_to_mail, $d_assigned_to, $d_time_meeting, $d_stage, $d_user, $d_time_creation, $d_email, $d_user_email, $d_address_student, $d_title, $d_comment_s, $d_telphone, $d_comment_secretariat) {

		/**
		 * Jmail
		 *
		 */

		$subject = "This is a confirmation message that the form has been $d_stage.";
		$body    = "This is a confirmation message that the form has been $d_stage.".'<br>
			Details:
			<hr>'.
			'Date sent by the student:: '.$d_time_creation.'<br><br>
			Student name: '.
			$d_user.'<br><br>'.
			'Adress: <br>'
			.$d_address_student.'<br><br>'.
			'Student number Index: '.$d_student_nr_indeks.'<br><br>'.
			'email: '.$d_email.'<br><br>'.
			'Telphone: '.$d_telphone.'<br><br>';

		if ($d_time_meeting != "--------") {
			$body .= 'Within the specified time: '.'<p style="color:red;">'.$d_time_meeting.'</p> we will try to contact you by phone <br><hr>';
		}

		$body .= 'Concerns: '.'<br>'
			.$d_title.
			'<br><br> Case handler: '.$d_assigned_to.'<br>'.
			'Stage: '.$d_stage.'<br><hr>';

		$body .= 'Student`s comment: '.'<br>'
			.$d_comment_s.'<br><br> 
			Comment secretariat: <br>'
			.$d_comment_secretariat.'<br>';

		$recipient = array(
			'xxx@pums.ump.edu.pl',
			$d_email,
			$d_assigned_to_mail,
			$d_user_email
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

# Invoke JMail Class
		$mailer = JFactory::getMailer();

# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		if ($my_filename != 0) {
			foreach ($my_filename as $file) {
				if (file_exists('../media/com_document/documents_forms/doc/'.$d_student_nr_indeks.'/'.$file)) {
					$mailer->addAttachment('../media/com_document/documents_forms/doc/'.$d_student_nr_indeks.'/'.$file);
				}
				if (file_exists('./media/com_document/documents_forms/doc/'.$d_student_nr_indeks.'/'.$file)) {
					$mailer->addAttachment('./media/com_document/documents_forms/doc/'.$d_student_nr_indeks.'/'.$file);
				}
			}
		}

//# Send once you have set all of your options
		$mailer->send();

	}

	public function mailReminderMe($remainer_id, $time_rem) {

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'created_by',
			'time_creation',
			'email',
			'address_student',
			'title',
			'assigned_to',
			'telephone',
			'student_nr_indeks'
		)));
		$query->from($db->quoteName('#__document_documents'));
		$query->where($db->quoteName('id').' LIKE '.$remainer_id);
		$db->setQuery($query);
		$results_document = $db->loadObjectList();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'name',
			'email'
		)));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id').' LIKE '.$results_document[0]->assigned_to);
		$db->setQuery($query);
		$results_assigned = $db->loadObjectList();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'name',
			'email'
		)));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id').' LIKE '.$results_document[0]->created_by);
		$db->setQuery($query);
		$results_created = $db->loadObjectList();

		$subject = "This is a reminder message that is due in $time_rem days";
		$body    = "".'<p style="color:red;">This is a reminder message that is due in '.$time_rem.' days!</p>'.'<br><br>'.
			'Details:
			<br>
			<hr>			
			Date sent by the student: '.$results_document[0]->time_creation.'<br><br>'.
			'Student name: '.$results_created[0]->name.'<br><br>'.
			'Adress: <br>'
			.$results_document[0]->address_student.'<br><br>'.
			'Student number Index: '.$results_document[0]->student_nr_indeks.'<br><br>'.
			'email: '.$results_created[0]->email.'<br><br>'.
			'Telphone: '.$results_document[0]->telephone.'<br><br>
			<hr>
			Concerns: '.'<br>'
			.$results_document[0]->title.
			'<br><br> Case handler: '.$results_assigned[0]->name.'<br>';

		$recipient = array(
			'xxx@pums.ump.edu.pl',
			$results_assigned[0]->email
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();

	}

	public function cronMailReminder($title, $telephone, $time_creation, $assigned_to, $email_s, $address_student, $student_nr_indeks, $time_rem) {

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'name',
			'email'
		)));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id').' LIKE '.$assigned_to);
		$db->setQuery($query);
		$results_assigned = $db->loadObjectList();

		$subject = "This is a reminder message that is due in $time_rem days";
		$body    = "".'<p style="color:red;">This is a reminder message that is due in '.$time_rem.' days!</p>'.'<br><br>'.
			'Details:
			<br>
			<hr>			
			Date sent by the student: '.$time_creation.'<br><br>'.
			'Student number Index: '.$student_nr_indeks.'<br><br>'.
			'Adress: <br>'
			.$address_student.'<br><br>'.
			'email: '.$email_s.'<br><br>'.
			'Telphone: '.$telephone.'<br><br>
			<hr>
			Concerns: '.'<br>'
			.$title.
			'<br><br> Case handler: '.$results_assigned[0]->name.'<br>';

		$recipient = array(
			'xxx@pums.ump.edu.pl',
			$results_assigned[0]->email
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();

	}

	public function deletePlikiReminder($email) {

		$subject = "This is a reminder message!";
		$body    = "".'<p style="color:red;">This is a reminder message!</p>'.'<br><br>'.
			'Your old documents on the student platform will be deleted in 2 days. 
			Log in to the platform and download the necessary files.';

		$recipient = array(
			$email
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();

	}

	public function MailActivate($d_token, $stan, $d_assigned_to_mail, $d_address_student, $d_title, $d_telphone, $d_student_nr_indeks) {

		$subject = "Poznan University Of Medical Sciences - document";
		$body    = "";

		if ($stan == 'active_start') {
			$body .= 'Document created by the student: '.$d_address_student.'<br>
			 index number: '.$d_student_nr_indeks.'<br>
			 telephone: '.$d_telphone.'<br>
			 title: '.$d_title.'<br>
			 you can activate the start of work on it by logging in to your account on the platform, 
			 or by clicking on the link below to start.<br><br>			
			<a href="http://localhost/communityhub/index.php?option=com_document&view=documentactive&activeStart='.$d_token.'"'.'>'
				.'started document'.'</a><br>';
		}
		if ($stan == 'active_finish') {
			$body .= 'Document created by the student: '.$d_address_student.'<br>
			 index number: '.$d_student_nr_indeks.'<br>
			  telephone: '.$d_telphone.'<br>
			 title: '.$d_title.'<br>
			 you can activate the finish of work on it by logging in to your account on the platform, 
			 or by clicking on the link below to start.<br><br>
			<a href="http://localhost/communityhub/index.php?option=com_document&view=documentactive&activeFinish='.$d_token.'"'.'>'
				.'finish document'.'</a><br>';
		}
		if ($stan == 'confirm_finish') {
			$body .= 'Your document title: '.$d_title.'<br>
				has been completed.<br>
				Log in to the student platform and see the details';
		}

		$recipient = array(
			$d_assigned_to_mail
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();

	}

	public function mailStatusStudent($stan, $tytul, $mail) {

		$subject = "Poznan University Of Medical Sciences - document";
		$body    = "";

		if ($stan == 'active_finish') {
			$body .= 'Your document title: '.$tytul.'<br>
				has been started.<br>
				Log in to the student platform and see the details!';
		}
		if ($stan == 'confirm_finish') {
			$body .= 'Your document title: '.$tytul.'<br>
				has been completed.<br>
				Log in to the student platform and see the details!';
		}

		$recipient = array(
			$mail
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();

	}

	public function mailActivateError($title, $student_nr_indeks) {

		$subject = "Error activating the start or end of work on a document";
		$body    = "".
			'Error activating the start or end of work on a document sent by the student<br>
			title document: '.$title.'<br>
			student index: '.$student_nr_indeks.'';

		$recipient = array(
			'xxx@pums.ump.edu.pl',
		);

		$from = array(
			"xxx@pums.ump.edu.pl",
			"Poznan University Of Medical Sciences"
		);

		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($recipient);

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		# If you would like to send as HTML, include this line; otherwise, leave it out
		$mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();

	}

}