<?php
// Hash a password securely
$plainPassword = 'yourpassword123';
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
echo $hashedPassword;
