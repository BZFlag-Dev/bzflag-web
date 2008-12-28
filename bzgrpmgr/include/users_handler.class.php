<?php

// Template class for user data handling

abstract class users_handler {
	abstract protected function getUsername();
	abstract protected function getUserID();
	abstract protected function getEncryptedPass();
}

?>